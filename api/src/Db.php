<?php


namespace api;


class Db
{
    protected $db;
    protected $tmpTable;

    /**
     * Db connection on init.
     */
    public function __construct()
    {
        global $db;
        $this->db = new \mysqli($db["HOST"], $db["USER"], $db["PASS"], $db["DB"]);
        $this->tmpTable = "tmp_excel_data";
    }

    /**
     * database connection close on task done
     */
    public function __destruct()
    {
        $this->db->close();
    }

    /**
     * @param $fieldArray
     * @return bool|\mysqli_result
     */
    public function createTmpTable($fieldArray)
    {
        $keyArray = $this->formatKeys($fieldArray);
        $keyString = $this->formatCreateSql($keyArray);
        return $this->db->query("CREATE TABLE IF NOT EXISTS $this->tmpTable($keyString)");
    }

    /**
     * @param $fields
     * @return array
     */
    protected function formatKeys($fields)
    {
        $formattedFields = array();
        foreach ($fields as $index => $item) {
            array_push($formattedFields,
                str_replace('.', '',
                    str_replace(['/', ' '], "_",
                        $item
                    )));
        }
        return $formattedFields;
    }

    /**
     * @param $keyArray
     * @return string
     */
    protected function formatCreateSql($keyArray)
    {
        $keyString = "";
        foreach ($keyArray as $key => $value) {
            if ($key == 0) {
                $keyString .= $value . " INT(11) AUTO_INCREMENT PRIMARY KEY, ";
            } else if ($value == "Date") {
                $keyString .= $value . " DATE, ";
            } else if ($key == count($keyArray) - 1) {
                $keyString .= $value . " VARCHAR(255) ";
            } else {
                $keyString .= $value . " VARCHAR(255), ";
            }
        }
        return $keyString;
    }


    /**
     * @param $tableName
     * @param $tabelFields
     * @param $row
     * @return int
     */
    protected function insertRow($tableName, $tabelFields, $row)
    {
        $keys = array();
        $values = array();

        foreach ($tabelFields as $index => $value) {
            $keys[] = '`' . $value . '`';
            $values[] = "'" . $row[$index] . "'";
        }
        $keys = implode(',', $keys);
        $values = implode(',', $values);
        $sql = "REPLACE INTO " . $tableName . " ($keys)VALUES ($values)";
        if ($this->db->query($sql)) {
            return 200;
        } else {
            echo json_encode($sql);
            return 500;
        }
    }

    /**
     * @param $data
     * @param null $customWhere
     * @return int
     */
    protected function updateRow($tableName, $rowId, $data, $customWhere = null)
    {
        $updateQuery = ' ';
        $count = 0;
        foreach ($data as $key => $value) {
            $count++;
            $updateQuery .= "`" . $key . "`" . "='" . $value . "'";
            if ($count < count($data)) {
                $updateQuery .= ", ";
            } else {
                $updateQuery .= " ";
            }
        }
        $customWhere = $customWhere != null ? " AND " . $customWhere : " ";
        $sql = "UPDATE " . $tableName . " SET " . $updateQuery . " WHERE `id` = '$rowId' " . $customWhere . " ";
        if ($this->db->query($sql)) {
            return 200;
        } else {
            return 500;
        }
    }


    /**
     * @param $value
     * @return string|string[]
     */
    protected function formatDbValue($value)
    {
        return str_replace("'", "\\'",
            str_replace('"', '\\"',
                str_replace(['/', '*', '".'], ' ', $value)
            ));
    }


}