<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Training;
use App\Repository\UserRepository;
use App\Repository\TrainingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrainingController extends AbstractController
{
    /* -----------*/
    /* ----GET----*/
    /* -----------*/


    /**
     * @Route("/training", name="training", methods={"GET"})
     * 
     */
    public function getAllTraining(TrainingRepository $allTraining, SerializerInterface $serializer)
    {
        $subject = $allTraining->findAll(); //Récupérer tous les cours
        $resultat = $serializer->serialize( //Les transformer en format Json  
            $subject,                       //Il doit serialiser $subject
            'json',                         //Au format Json
            [
                'groups'  => ['listTraining'] //Qui sont dans le groupe "listTraining"
            ]
        );
        return new JsonResponse($resultat, 200, [], true); //Retourne moi $resultat avec le code statut 200. "true" puisqu'il s'agit déjà de Json
    }

    /**
     * @Route("/trainingById", name="training_id", methods={"GET"})
     */
    public function getTrainingById(Request $request, SerializerInterface $serializer)
    {
        $trainingId = $request->query->get('id');
        $training =  $this->getDoctrine()->getRepository(Training::class)->findOneById($trainingId);
        
        $resultat = $serializer->serialize(
            $training,
            'json',
            [
                'groups'  => ['TrainingDetails']
            ]
        );
        return new JsonResponse($resultat, Response::HTTP_OK, [], true); //Response::HTTP_ok équivaut à 200
    }



/*
    public function getStudentTraining()
    {

    }
*/

    /* ------------*/
    /* ----POST----*/
    /* ------------*/


    /**
     * @Route("/addTraining", name="add_training", methods={"POST"})
     */
    public function addTraining(Request $request): Response
    {
        // On prend l'id du teacherUser
        $teacher = $this->getDoctrine()->getRepository(User::class)->findOneById($request->request->get("teacher_id"));

        // On prend toutes les données envoyés en POST
        $start_training =$request->request->get("start_training");
        $end_training =$request->request->get("end_training");
        $max_student =$request->request->get("max_student");
        $price_per_student =$request->request->get("price_per_student");
        $training_description =$request->request->get("training_description");
        $subject =$request->request->get("subject");

        // On créé l'objet Training
        $em = $this->getDoctrine()->getManagerForClass(Training::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $training = new Training();

        try {
            $training->setTeacher($teacher);
            $training->setStartTraining(new DateTime($start_training));
            $training->setEndTraining(new DateTime($end_training));
            $training->setMaxStudent((int)$max_student);
            $training->setPricePerStudent($price_per_student);
            $training->setTrainingDescription($training_description);
            $training->setSubject($subject);
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => "erreur 1"]));
            return $response;
        }

        // On persist l'object = on l'écris dans la BDD
        try {
            $em->persist($training);
            $em->flush();
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => "erreur 2"]));
            return $response;
        }

        // On retourne un message de succes
        $response->setContent(json_encode(["success" => TRUE]));
        return $response;

    }

    /* -----------*/
    /* ----PUT----*/
    /* -----------*/

    /**
     * @Route("/updateTraining", name="update_training", methods={"PUT"})
     */
    public function updateTraining(Request $request): Response
    {
        //Get and decode Data from request body
        $requestParams = $request->getContent();
        $content = json_decode($requestParams, TRUE);

        $trainingId = $request->query->get('id');

        //Fetch Data in local variables
        $teacherId = $content["teacher_id"];
        $start_training = $content["startTtraining"];
        $end_training = $content["endTraining"];
        $max_student = $content["maxStudent"];
        $price_per_student = $content["pricePerStudent"];
        $training_description = $content["trainingDescription"];
        $subject = $content["subject"];


        //Get the event from DBAL
        $training = $this->getDoctrine()->getRepository(Training::class)->findOneByID($trainingId);

        //Get Entity Manager
        $em = $this->getDoctrine()->getManagerForClass(Training::class);

        //Prepare HTTP Response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Update training object
        try {
            $training->setTeacher($teacherId);
            $training->setStartTraining(new DateTime($start_training));
            $training->setEndTraining(new DateTime($end_training));
            $training->setMaxStudent((int)$max_student);
            $training->setPricePerStudent($price_per_student);
            $training->setTrainingDescription($training_description);
            $training->setSubject($subject);
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => "error 1"]));
        }

        //Persistence
        try {
            $em->persist($training);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => "$e"]));
        }
        return $response;

    }


    /* --------------*/
    /* ----DELETE----*/
    /* --------------*/

    /**
     * @Route("/deleteTraining", name="delete_training", methods={"DELETE"})
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
            $response->setContent(json_encode(["success" => "error 1"]));
        }

        //Remove object
        try {
            $em->remove($training);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => "error 2"]));
        }
        return $response;
    }


}