<?php


namespace App\Controller;


use App\Entity\User;
use App\Util\ExportInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    /**
     * @Route("/testpdf", name="test_exportPDF", methods={"GET"})
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
                $user->getLastname(),
                $user->getFirstname(),
                implode(" & ", $user->getRoles())
            ];
        }

        $tableHead = [
            "ID",
            "Lastname",
            "Firstname",
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
}