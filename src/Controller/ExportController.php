<?php

namespace App\Controller;

use App\Entity\Training;
use App\Entity\User;
use App\Service\ExcelExporter;
use App\Util\ExportInterface;
use DateTime;

use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/exports", name="exports")
 * @IsGranted("ROLE_TEACHER")
 */

class ExportController extends AbstractController
{

/*------------------------------------------------------------------------------------
---------------------------      EXPORT TO PDF     ----------------------------------
-------------------------------------------------------------------------------------*/

    /**
     * @Route("/getTrainingStudents/pdf", name="exports_to_PDF", methods={"GET"})
     * @param ExportInterface $pdfExporter
     * @param UserInterface $currentUser
     * @param Request $request
     * @return Response
     */
    public function getTrainingStudentsPDF(ExportInterface $pdfExporter, UserInterface $currentUser, Request $request)
    {
        $trainingId = $request->query->get("id");
        $training = $this->getDoctrine()->getRepository(Training::class)->findOneById($trainingId);

        $notAuth = $currentUser->getRoles()[0] == "ROLE_TEACHER" AND $training->getTeacher() != $currentUser;
        if ($notAuth) {
            return new Response(
                json_encode(["success"=>FALSE]),
                Response::HTTP_UNAUTHORIZED,
                ["Content-Type"=>"application/json"]
            );
        }

        $participants = $training->getParticipants();

        foreach ($participants as $user) {
            $exportContent[] = [
                $user->getLastname(),
                $user->getFirstname(),
                $user->getEmail(),
                $user->getPhone(),
            ];
        }

        $tableHead = [
            "Nom",
            "Prénom",
            "Email",
            "Téléphone"
        ];

        $title = "Liste des élèves du cours de ".$training->getSubject()." ayant lieu du ".$training->getStartTraining()->format("d/m/Y")." au ".$training->getEndTraining()->format("d/m/Y");
        $pdfExporter->setDocumentInformation('P', $title);
        $date = new \DateTime();
        $filename = "export_".$date->getTimestamp().".pdf";
        $pdfExporter->exportTable($exportContent, $tableHead);
        $pdfExporter->save($filename);
        return new Response(
            json_encode(["filename" => "/tmp/".$filename]),
            Response::HTTP_OK,
            ["Content-Type"=>'application/json']
        );
    }


/*------------------------------------------------------------------------------------
---------------------------      EXPORT TO EXCEL     ---------------------------------
-------------------------------------------------------------------------------------*/

    /**
     * @Route("/getTrainingStudents/excel", name="exports_to_excel", methods={"GET"})
     * @param ExcelExporter $excelExporter
     * @param UserInterface $currentUser
     * @param Request $request
     * @return Response
     */
    public function exportToExcel(ExcelExporter $excelExporter, UserInterface $currentUser, Request $request)
    {
        $trainingId = $request->query->get("id");
        $training = $this->getDoctrine()->getRepository(Training::class)->findOneById($trainingId);

        $notAuth = $currentUser->getRoles()[0] == "ROLE_TEACHER" AND $training->getTeacher() != $currentUser;
        if ($notAuth) {
            return new Response(
                json_encode(["success"=>FALSE]),
                Response::HTTP_UNAUTHORIZED,
                ["Content-Type"=>"application/json"]
            );
        }

        $participants = $training->getParticipants();
        $tableContent = [];

        foreach ($participants as $user) {
            $tableContent[] = [
                $user->getLastname(),
                $user->getFirstname(),
                $user->getEmail(),
                $user->getPhone(),
            ];
        }

        $tableHead = [
            "Nom",
            "Prénom",
            "Email",
            "Téléphone"
        ];

        $excelExporter->exportTable($tableContent, $tableHead);

        $filename = $excelExporter->createFile();

        $excelExporter->save($filename);

        return new Response(
            json_encode(["filename" => "/tmp/".$filename]),
            Response::HTTP_OK,
            ["Content-Type"=>'application/json']
        );
    }
}
