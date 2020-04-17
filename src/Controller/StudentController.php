<?php

namespace App\Controller;

use App\Entity\Training;
use App\Entity\User;
use App\Entity\CalendarEvent;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CalendarEventRepository;

/**
 * @Route("/student", name="student_")
 * @IsGranted("ROLE_USER")
 */

class StudentController extends AbstractController
{
    // ******************************************************************************************************
    // *****************************************   PUT   ****************************************************
    // ******************************************************************************************************

    /*-------------------------------      SUBSCRIPTION UPDATE A TRAINING     -----------------------------*/

    /**
     * @Route("/subscribeTraining", name="subscribe_training", methods={"PUT"})
     * @param Request $request
     * @param UserInterface $currentStudent
     * @return Response
     */
    public function subscribeTraining(Request $request, UserInterface $currentStudent): Response
    {
        //Retrieve entity manager
        $em = $this->getDoctrine()->getManagerForClass(Training::class);
        //Retrieve request content
        $content = json_decode($request->getContent(), TRUE);

        //Retrieve training in DB
        try {
            $training = $em->getRepository(Training::class)->findOneById(
                $content["id"]
            );
        }
        catch (\Exception $e) {
            return new Response(
                json_encode(["success" => false]),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type'=>'application/json']
            );
        }

        //Check available places
        $nbFree = $training->getMaxStudent() - sizeof($training->getParticipants());

        if ($nbFree <= 0) {
            return new Response(json_encode(["success"=>false]),
            Response::HTTP_BAD_REQUEST,
            ["Content-Type"=>'application/json']
            );
        }

        //Add user subscription
        try {
            $training->addParticipant($currentStudent);
            $em->persist($training);
            $em->flush();
        } catch (\Exception $e) {
            return new Response(json_encode(["success"=>false]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ["Content-Type"=>'application/json']
            );
        }

        return new Response(json_encode(["success"=>true]),
            Response::HTTP_OK,
            ["Content-Type"=>'application/json']
        );
    }
}