<?php

namespace App\Controller;

use App\Entity\Training;
use App\Entity\User;
use App\Entity\CalendarEvent;
use App\Repository\CalendarEventRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/teacher", name="gestform_teacher")
 * @IsGranted("ROLE_TEACHER")
 */
class TeacherController extends AbstractController
{

    // *******************************************************************************************************
    // *****************************************   GET   *****************************************************
    // *******************************************************************************************************

    /*-------------------------      GET ALL TRAININGS FOR CURRENT TEACHER     -----------------------------*/

    /**
     * @Route("/getMyTrainings", name="my_trainings", methods={"GET"})
     * @param Request $request
     * @param UserInterface $currentUser
     * @return Response
     */

    public function getTeacherTrainings(Request $request, UserInterface $currentUser) : Response
    {
        //Retrieve current teacher trainings list
        try {
            $trainingsList = $currentUser->getTeacherTrainings();
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
                "startDatetime" =>  $training->getStartTraining()->format('Y-m-d H:i:s'),
                "endDatetime"   =>  $training->getEndTraining()->format('Y-m-d H:i:s'),
                "maxStudents"   =>  $training->getMaxStudent(),
                "description"   =>  $training->getTrainingDescription(),
                "studentsCount" =>  sizeof($training->getParticipants()),
                "subject"       => $training->getSubject()
            ];
        }

        return new Response(
            json_encode($responseContent),
            Response::HTTP_OK,
            ['Content-Type'=>'application/json']
        );
    }

    // *******************************************************************************************************
    // *****************************************   PUT   *****************************************************
    // *******************************************************************************************************

    /*---------------------------      UPDATE A TRAINING BY IT'S TEACHER     -------------------------------*/

    /**
     * @Route("/updateTraining", name="updateTraining", methods={"PUT"})
     * @param Request $request
     * @param UserInterface $currentUser
     * @return Response
     */

    public function updateTraining(Request $request, UserInterface $currentUser) : Response
    {
        //Get and decode Data from request body
        $requestParams  =   $request->getContent();
        $content        =   json_decode($requestParams, TRUE);

        //Fetch Data in local variables
        $trainingId             =   $content["id"];
        $teacherId              =   $currentUser->getId();
        $start_training         =   $content["startTtraining"];
        $end_training           =   $content["endTraining"];
        $max_student            =   $content["maxStudent"];
        $price_per_student      =   $content["pricePerStudent"];
        $training_description   =   $content["trainingDescription"];
        $subject                =   $content["subject"];

        //Get the event from DBAL
        $training = $this->getDoctrine()->getRepository(Training::class)->findOneByID($trainingId);

        //Get Entity Manager
        $em = $this->getDoctrine()->getManagerForClass(Training::class);

        //Check if current user is the author of the training
        if ($teacherId != $training->getTeacher()->getId()) {
            return new Response(
                json_encode(["success" => FALSE]),
                Response::HTTP_UNAUTHORIZED,
                ['Content-Type'=>'application/json']
            );
        }

        //Prepare HTTP Response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Update training object
        try {
            $training   ->setStartTraining(new DateTime($start_training))
                        ->setEndTraining(new DateTime($end_training))
                        ->setMaxStudent((int)$max_student)
                        ->setPricePerStudent($price_per_student)
                        ->setTrainingDescription($training_description)
                        ->setSubject($subject);
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        //Persistence
        try {
            $em->persist($training);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }

}
