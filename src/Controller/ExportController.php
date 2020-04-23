<?php


namespace App\Controller;


use App\Entity\Training;
use App\Entity\User;
use App\Util\ExportInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ExportController
 * @package App\Controller
 * @Route("/export", name="export")
 */
class ExportController extends AbstractController
{
    /**
     * @Route("/getTrainingStudents/pdf", name="export_students_pdf", methods={"GET"})
     * @param ExportInterface $pdfExporter
     * @param UserInterface $currentUser
     * @param Request $request
     * @return Response
     */
    public function getTrainingStudentsPDF(ExportInterface $pdfExporter, UserInterface $currentUser, Request $request)
    {
        $training = $this->getDoctrine()->getRepository(Training::class)->findOneById($request->query->get("id"));

        $studentsList = $training->getParticipants();
        $exportContent = $this->get('serializer')->normalize(
            $studentsList,
            null,
            [
                'groups'=>'listUserSimple'
            ]
        );

        $tableHead = [
            "ID",
            "Email",
            "Nom",
            "Prénom"
        ];

        $title = "Liste des élèves du cours de ".$training->getSubject()." ayant lieu du ".$training->getStartTraining()->format("d/m/Y")." au ".$training->getEndTraining()->format("d/m/Y");
        $pdfExporter->setDocumentInformation('P', $title);
        $date = new \DateTime();
        $filename = "export_".$date->getTimestamp().".pdf";
        $pdfExporter->exportTable($exportContent, $tableHead);
        $pdfExporter->save($filename);
        return new Response(
            json_encode(["filename" => $filename]),
            Response::HTTP_OK,
            ["Content-Type"=>'application/json']
        );
    }
}