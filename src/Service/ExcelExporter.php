<?php


namespace App\Service;


use App\Util\ExportInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class ExcelExporter
 * @package App\Service
 */
class ExcelExporter implements ExportInterface
{
    private $document;

    public function __construct()
    {
        $this->document = new Spreadsheet();
    }

    /**
     * @inheritDoc
     */
    public function exportTable(array $tableContent, array $tableHead = [null])
    {
        // TODO: Implement exportTable() method.
    }

    /**
     * @inheritDoc
     */
    public function save(String $filename): String
    {
        // TODO: Implement save() method.
        return '00';
    }
}