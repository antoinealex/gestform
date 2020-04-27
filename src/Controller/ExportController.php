<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ExcelExporter;
use App\Util\ExportInterface;
use DateTime;

use PhpOffice\PhpSpreadsheet\Writer\Exception;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/exports", name="exports")
 */

class ExportsController extends AbstractController
{

/*------------------------------------------------------------------------------------
---------------------------      EXPORT TO PDF     ----------------------------------
-------------------------------------------------------------------------------------*/

    /**
     * @Route("/getTrainingStudents/pdf", name="exports_to_PDF", methods={"GET"})
     * @param ExportInterface $pdfExporter
     * @return Response
     */
    public function testPDF(ExportInterface $pdfExporter)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $exportContent = [];

        foreach ($users as $user) {
            $exportContent[] = [
                $user->getId(),
                $user->getEmail(),
                $user->getLastname(),
                $user->getFirstname(),
                implode(" & ", $user->getRoles())
            ];
        }

        $tableHead = [
            "ID",
            "Last Name",
            "First Name",
            "Roles"
        ];
        $pdfExporter->setDocumentInformation('P', "test");
        $date = new \DateTime();
        $pdfExporter->exportTable($exportContent, $tableHead);
        return new Response(
            json_encode($pdfExporter->save("export_".$date->getTimestamp().".pdf")),
            Response::HTTP_OK,
            ["Content-Type"=>"application/json"]
        );
    }


/*------------------------------------------------------------------------------------
---------------------------      EXPORT TO EXCEL     ---------------------------------
-------------------------------------------------------------------------------------*/

    /**
     * @Route("/getTrainingStudents/excel", name="exports_to_excel", methods={"GET"})
     * @param ExcelExporter $excelExporter
     * @return Response
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportToExcel(ExcelExporter $excelExporter)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $tableContent = [];
        foreach ($users as $user) {
            $tableContent[] = [
                $user->getId(),
                $user->getEmail(),
                $user->getLastname(),
                $user->getFirstname()
            ];
        }

        $tableHead = [
            "ID",
            "Email",
            "Last Name",
            "First Name",
        ];

        $excelExporter->exportTable($tableContent, $tableHead);

        $filename = $excelExporter->createFile();

        $excelExporter->save($filename);

        return $response = new Response($filename);

    }
}
