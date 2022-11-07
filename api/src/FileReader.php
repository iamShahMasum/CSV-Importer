<?php


namespace api;


use Exception;

class FileReader extends Db
{

    private $tmpTableFields = array();

    /**
     * @param $file
     * @param $startFrom
     * @param $endTo
     * @return bool
     */
    public function read($file, $startFrom, $endTo)
    {
        try {
            return $this->getFileChunk($file['tmp_name'], 4096,
                function ($chunk, &$handle, $iteration) use (&$data, &$startFrom, &$endTo) {

                    /**
                     *creating the table in not exists
                     */
                    if ($iteration == 0) {
                        $this->pushToDb($iteration, $chunk);
                    }
                    /**
                     *inserting data to the table
                     * from start index to end index
                     */
                    if (!empty($startFrom) && !empty($endTo)) {
                        if ($iteration >= $startFrom && $iteration <= $endTo) {
                            $this->pushToDb($iteration, $chunk);
                        }
                    } else {
                        /**
                         * pushing the whole data
                         * if start and end index not set
                         */
                        $this->pushToDb($iteration, $chunk);
                    }
                });

        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public function getFileChunk($file, $chunk_size, $callback)
    {
        try {
            $handle = fopen($file, "r");
            $i = 0;
            while (!feof($handle)) {
                call_user_func_array($callback, [fgetcsv($handle, $chunk_size), &$handle, $i]);
                $i++;
            }
            fclose($handle);
        } catch (Exception $e) {
            echo $e->getMessage();
        } finally {
            return true;
        }
    }

    public function pushToDb($iteration, $data)
    {
        if ($data)
            if ($iteration == 0) {
                $this->tmpTableFields = $this->formatKeys($data);
                $this->createTmpTable($data);
            } else {
                $formatedRow = array();
                foreach ($this->tmpTableFields as $index => $item) {
                    if ('date' == strtolower($item))
                        array_push($formatedRow, date('Y-m-d', strtotime($data[$index])));
                    elseif ('remarks' == strtolower($item))
                        array_push($formatedRow, $this->formatDbValue($data[$index]));
                    else
                        array_push($formatedRow, $data[$index]);
                }

                try {
                    return $this->insertRow($this->tmpTable, $this->tmpTableFields, $formatedRow);
                } catch (Exception $exception) {
                    echo $exception->getMessage();
                }
            }
    }
}