<?php


namespace App\Controller;


use App\Entity\User;
use App\Kernel;
use App\Service\PDFExporter;
use Composer\DependencyResolver\Request;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    /**
     * @Route("/testpdf", name="test_exportPDF", methods={"GET"})
     * @param PDFExporter $exporter
     * @return Response
     */
    public function testPDF(PDFExporter $exporter)
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
        $exporter->setDocumentInformation('P', "test");
        $date = new \DateTime();
        $exporter->exportTable($exportContent, $tableHead);
        return new Response(
            json_encode($exporter->save("export_".$date->getTimestamp().".pdf")),
            Response::HTTP_OK,
            ["Content-Type"=>"application/json"]
        );
    }
}