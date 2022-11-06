<?php


namespace api;


use Exception;

class FileReader extends Db
{

    private $tableFields = array();

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
                $paramArray = array(fgetcsv($handle, $chunk_size), &$handle, $i);
                call_user_func_array($callback, $paramArray);
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
                $this->tableFields = $this->formatKeys($data);
                $this->createTable($data);
            } else {
                $formatedRow = array();
                foreach ($this->tableFields as $index => $item) {
                    if ('date' == strtolower($item))
                        array_push($formatedRow, date('Y-m-d', strtotime($data[$index])));
                    else
                        array_push($formatedRow, $data[$index]);
                }

                try {
                    $this->insertRow('data', $this->tableFields, $formatedRow);
                } catch (Exception $exception) {
                    echo $exception->getMessage();
                }
            }
    }


    public function pushToTextFile($data)
    {
        $file = 'test.txt';
        $content = json_encode($data);
        file_put_contents($file, $content . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}