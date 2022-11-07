<?php


namespace api;


class Distribute extends Db
{
    private $data;

    public function __construct($table = null, $rowId = null)
    {
        parent::__construct($table, $rowId);
    }

}