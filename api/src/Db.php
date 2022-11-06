<?php


namespace api;


class Db
{
    protected $db;
    private $table;
    private $rowId;

    /**
     * Db constructor.
     */
    public function __construct($table = null, $rowId = null)
    {
        global $db;
        $this->table = $table;
        $this->rowId = $rowId;
        $this->db = new \mysqli($db["HOST"], $db["USER"], $db["PASS"], $db["DB"]);
    }

    public function __destruct()
    {
        $this->db->close();
    }

    public function createTable($fieldArray)
    {
        $keyArray = $this->formatKeys($fieldArray);
        $keyString = $this->formatSql($keyArray);
        return $this->db->query("CREATE TABLE IF NOT EXISTS data($keyString)");
    }

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

    protected function formatSql($keyArray)
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
            $values[] = str_replace(['/', '*', '".'], '', "'" . $row[$index] . "'");
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
    protected function updateRow($data, $customWhere = null)
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
        $sql = "UPDATE " . $this->table . " SET " . $updateQuery . " WHERE `id` = '$this->rowId' " . $customWhere . " ";
        if ($this->db->query($sql)) {
            return 200;
        } else {
            return 500;
        }
    }


}