<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Training;
use App\Repository\UserRepository;
use App\Repository\TrainingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
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
        $start_training = $request->request->get("start_training");
        $end_training = $request->request->get("end_training");
        $max_student = $request->request->get("max_student");
        $price_per_student = $request->request->get("price_per_student");
        $training_description = $request->request->get("training_description");
        $subject = $request->request->get("subject");

        // On créé l'objet Training
        $em = $this->getDoctrine()->getManagerForClass(Training::class);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $training = new Training();
        
        try {
            $training->setTeacher($teacher);
            if (strtotime($start_training) >= strtotime("now")) {
                $training->setStartTraining(new DateTime($start_training));
            }else {
                $response->setContent(json_encode(["success" => FALSE]));
                exit();
            }
            if (strtotime($end_training) >= strtotime($start_training)) {
                $training->setEndTraining(new DateTime($end_training));
            }else {
                $response->setContent(json_encode(["success" => FALSE]));
                exit();
            }
            $training->setMaxStudent((int)$max_student);
            $training->setPricePerStudent($price_per_student);
            $training->setTrainingDescription($training_description);
            $training->setSubject($subject);
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        // On persist l'object = on l'écris dans la BDD
        try {
            $em->persist($training);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        // On retourne un message de succes
        return $response;
    }



    /**
     * @Route("/addRegistration", name="add_registration", methods={"POST"})
     */    
    public function addRegistration(Request $request)
    {
        //Préparation de la réponse
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Récupération de la requête
        $trainingId = $request->request->get("training_id");
        $userId = $request->request->get("user_id");

        //Récupération du training et du user
        $training =  $this->getDoctrine()->getRepository(Training::class)->findOneById($trainingId);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneById($userId);

        //Ajout du participant
        try {
            $training->addParticipant($user);
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        //Persistance
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($training);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
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

        //Fetch Data in local variables
        $trainingId = $content["id"];
        $teacherId = $content["teacher_id"];
        $start_training = $content["startTtraining"];
        $end_training = $content["endTraining"];
        $max_student = $content["maxStudent"];
        $price_per_student = $content["pricePerStudent"];
        $training_description = $content["trainingDescription"];
        $subject = $content["subject"];

        //Get the event from DBAL
        $training = $this->getDoctrine()->getRepository(User::class)->findOneByID($trainingId);

        //Get Entity Manager
        $em = $this->getDoctrine()->getManagerForClass(Training::class);

        //Prepare HTTP Response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Update training object
        try {
            $training->setTeacher($this->getDoctrine()->getRepository(User::class)->findOneByID($teacherId));
            $training->setStartTraining(new DateTime($start_training));
            $training->setEndTraining(new DateTime($end_training));
            $training->setMaxStudent((int)$max_student);
            $training->setPricePerStudent($price_per_student);
            $training->setTrainingDescription($training_description);
            $training->setSubject($subject);
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

    
    /**
     * @Route("/unsubscribeStudent", name="unsubscribe_student", methods={"DELETE"})
     */    
    public function unsubscribeStudent(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $trainingId = $request->request->get("training_id");
        $userId = $request->request->get("user_id");

        $training =  $this->getDoctrine()->getRepository(Training::class)->findOneById($trainingId);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneById($userId);

        try {
            $training->removeParticipant($user);
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($training);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        return $response;
    }

}