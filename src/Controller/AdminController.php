<?php

namespace App\Controller;

use App\Entity\Training;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
  * Class AdminController
 * @package App\Controller
 * @Route("/admin", name="adminController")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    // ******************************************************************************************************
    // *****************************************   PUT   ****************************************************
    // ******************************************************************************************************

    /*---------------------------------      UPDATE A TRAINING     -------------------------------*/

    /**
     * @Route("/updateTraining", name="update_training", methods={"PUT"})
     * @IsGranted("ROLE_TEACHER")
     */
    public function updateTraining(Request $request): Response
    {
        //Get and decode Data from request body
        $requestParams =    $request->getContent();
        $content       =    json_decode($requestParams, TRUE);

        //Fetch Data in local variables
        $trainingId           = $content["id"];
        $teacherId            = $content["teacher_id"];
        $start_training       = $content["startTraining"];
        $end_training         = $content["endTraining"];
        $max_student          = $content["maxStudent"];
        $price_per_student    = $content["pricePerStudent"];
        $training_description = $content["trainingDescription"];
        $subject              = $content["subject"];

        //Get the event from DBAL
        $training = $this->getDoctrine()->getRepository(Training::class)->findOneByID($trainingId);

        //Get Entity Manager
        $em = $this->getDoctrine()->getManagerForClass(Training::class);

        //Prepare HTTP Response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Update training object
        try {
            $training   ->setTeacher($this->getDoctrine()->getRepository(User::class)->findOneByID($teacherId))
                ->setStartTraining(new DateTime($start_training))
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

    // ******************************************************************************************************
    // *****************************************   DELETE   *************************************************
    // ******************************************************************************************************

    /*---------------------------------      DELETE TRAINING      ------------------------------*/

    /**
     * @Route("/deleteTraining", name="delete_training", methods={"DELETE"})
     * @IsGranted("ROLE_TEACHER")
     * @param Request $request
     * @return Response
     */
    public function deleteTraining(Request $request): Response
    {
        //Get Entity Manager and prepare response
        $em = $this->getDoctrine()->getManagerForClass(Training::class);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Get training object to delete
        $trainingId = $request->query->get("id");

        try {
            $training = $em->getRepository(Training::class)->findOneByID($trainingId);
        } catch (NonUniqueResultException $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        //Remove object
        try {
            $em->remove($training);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }
}