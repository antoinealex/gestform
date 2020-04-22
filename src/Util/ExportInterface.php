<?php


namespace App\Util;


interface ExportInterface
{
    /**
     * Export an array to a table in file.
     * @author Kadir AVCI
     * @param array $tableContent Main content of the table to export, without headers.
     * @param array $tableHead Optional header to include in exported tables
     * @return mixed
     */
    public function exportTable(Array $tableContent, Array $tableHead = [null]);

    /**
     * Save the created file under specific filename.
     * @author Kadir AVCI
     * @param String $filename Name of the file to be exported.
     * @return String Path of the exported file on server. Does not include FQDN.
     */
    public function save(String $filename) : String;
}