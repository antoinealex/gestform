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
    // *****************************************   GET   ****************************************************
    // ******************************************************************************************************


    /*-------------------------      GET ALL OWN TRAININGS FOR CURRENT STUDENT     -----------------------------*/

    /**
     * @Route("/getMyTrainings", name="my_trainings_student", methods={"GET"})
     * @param Request $request
     * @param UserInterface $currentUser
     * @return Response
     */

    public function getMyTrainings(Request $request, UserInterface $currentUser) : Response
    {
        //Retrieve current teacher trainings list
        try {
            $trainingsList = $currentUser->getStudentTrainings();
        }
        catch (\Exception $e) {
            return new Response(
                json_encode(["success" => FALSE]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['Content-Type'=>'application/json']
            );
        }

        //Serialization
        $responseContent = [];

        foreach ($trainingsList as $training) {
            $responseContent[$training->getId()] = [
                "startDatetime"     =>  $training->getStartTraining()->format('Y-m-d H:i:s'),
                "endDatetime"       =>  $training->getEndTraining()->format('Y-m-d H:i:s'),
                "description"       =>  $training->getTrainingDescription(),
                "subject"           =>  $training->getSubject(),
                "teacher"         =>  [
                    "lastname"  =>  $training->getTeacher()->getLastname(),
                    "firstname" =>  $training->getTeacher()->getFirstname()
                ]
            ];
        }

        return new Response(
            json_encode($responseContent),
            Response::HTTP_OK,
            ['Content-Type'=>'application/json']
        );
    }


    /*---------------------------------      GET TRAINING BY ID     -------------------------------------*/

    /**
     * @Route("/getTrainingById", name="training_id", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function getTrainingById(Request $request) : Response
    {
        $trainingId = $request->query->get('id');
        $training =  $this->getDoctrine()->getRepository(Training::class)->findOneById($trainingId);

        //Return 404 if training can't be found
        if (!$training) {
            return new Response(
                json_encode(["error"=>"Training can't be found"]),
                Response::HTTP_NOT_FOUND,
                ['Content-Type'=>'application/json']
            );
        }

        //Serialize the data

        $resultat = json_encode(
          [
              "id"              =>  $training->getId(),
              "startTraining"   =>  $training->getStartTraining(),
              "endTraining"     =>  $training->getEndTraining(),
              "description"     =>  $training->getTrainingDescription(),
              "teacher"         =>  [
                                        "lastname"  =>  $training->getTeacher()->getLastname(),
                                        "firstname" =>  $training->getTeacher()->getFirstname()
                                    ],
              "subject"         =>  $training->getSubject()
          ]  
        );
        return new Response(
            $resultat,
            Response::HTTP_OK,
            ['Content-Type'=>'application/json']
        );
    }
    
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

        //Convert date to hours
        $hours = $currentStudent->getHours();
        $startTraining = $training->getStartTraining();
        $endTraining = $training->getEndTraining();
        $interval = $endTraining->diff($startTraining);
        $credit = ($interval->i / 60) + $interval->h;

        if (($hours - $credit) < 0) {
            return new Response(json_encode(["success"=>false]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ["Content-Type"=>'application/json']
            );
        } else {
            //Decrement hours
            $currentStudent->setHours($hours - $credit);
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
        }
        return new Response(json_encode(["success"=>true]),
            Response::HTTP_OK,
            ["Content-Type"=>'application/json']
        );
    }
}