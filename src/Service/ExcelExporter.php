<?php


namespace App\Service;


use App\Util\ExportInterface;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

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
     * @inheritDoc  Prepare the table to export
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportTable(array $tableContent, array $tableHead = [null])
    {
        $sheet = $this->document->getActiveSheet();

        if($tableHead != [null]){
            $count = 1;
            $column = 'A';
            foreach ($tableHead as $cell){
                $sheet->setCellValue($column.$count, $cell);
                $column++;
            }
        }

        $col = 'A';
        $row = 2;

        foreach ($tableContent as $rowData) {
            foreach ($rowData as $cellData){
                $sheet->setCellValue($col.$row, $cellData);
                $col++;
            }
            $col = 'A';
            $row++;
        }
    }

    /**
     * Create the excel file name
     * @return string
     */
    public function createFile(){
        $date = new DateTime();
        $fileCreateDate = $date->getTimestamp();
        return "you_stud_$fileCreateDate.xlsx";
    }

    /**
     * Save the excel file in the server
     * @inheritDoc
     * @throws Exception
     */
    public function save(String $filename): String
    {
        $publicDirectory = \dirname(__DIR__) . '/../public';
        $excelFilepath =  $publicDirectory . '/' .$filename;

        $writer = new Xlsx($this->document);
        $writer->save($excelFilepath);

        return new Response($filename);

    }
}