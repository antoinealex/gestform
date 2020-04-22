<?php


namespace App\Service;


use App\Util\ExportInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use TCPDF;

/**
 * Class PDFExporter
 * @package App\Service
 */
class PDFExporter implements ExportInterface
{
    private $document;
    private $assetsPath;
    private $rootPath;

    /**
     * PDFExporter constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->document = new TCPDF();
        $this->assetsPath = $kernel->getProjectDir().'/src/template/assets/';
        $this->rootPath =  $kernel->getProjectDir();
    }

    /**
     * Set the general information of the exported document
     * @param String $orientation
     * @param String $title
     */
    public function setDocumentInformation(String $orientation = 'P', String $title = '') {

        $this->document->setPageOrientation($orientation);
        $this->document->SetCreator("GestForm Web App");
        $this->document->SetAuthor('GestForm PDF Service');
        $this->document->SetTitle($title);
        $this->document->SetHeaderData($this->assetsPath.'logo.png', 30, $title, "Gestform");
        $this->document->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->document->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $this->document->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->document->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->document->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->document->SetFooterMargin(PDF_MARGIN_FOOTER);

    }

    /**
     * Save the created PDF Document in a directory
     * @param String $filename the name of the PDF file to create
     * @return String
     */
    public function save(String $filename) : String {
        $this->document->Output($this->rootPath."/public/tmp/".$filename, "F");
        return $this->rootPath."/public/tmp/".$filename;
    }

    public function exportTable(Array $tableContent, Array $tableHead = [null]) {
        $this->document->AddPage();
        $this->document->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $tbl = '
        <table cellspacing="0" cellpadding="1" border="1">';

        if ($tableHead != [null]) {
            $tbl .= '<thead><tr>';

            foreach ($tableHead as $cell) {
                $tbl .= '<th>'.$cell.'</th>';
            }
            $tbl.= '</tr></thead>';
        }

        $tbl .= '<tbody>';

        foreach ($tableContent as $row) {
            $tbl .= '<tr>';
            foreach ($row as $cell) {
                $tbl .= '<td>'.$cell.'</td>';
            }
            $tbl .= '</tr>';
        }

        $tbl .='</tbody></table>';

        $this->document->writeHTML($tbl);
    }
}