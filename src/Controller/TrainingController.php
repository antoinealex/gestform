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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/training", name="gestform_training")
 * 
 */    

class TrainingController extends AbstractController
{
// *******************************************************************************************************
// *****************************************   GET   *****************************************************
// *******************************************************************************************************

/*---------------------------------      GET ALL TRAINING    -------------------------------------*/

    /**
     * @Route("/getAllTraining", name="training", methods={"GET"})
     */
    public function getAllTraining(TrainingRepository $allTraining, SerializerInterface $serializer)
    {
        $subject = $allTraining->findAll(); 
        $resultat = $serializer->serialize( 
            $subject,                       
            'json',                         
            [
                'groups'  => ['listTraining']
            ]
        );
        return new JsonResponse($resultat, 200, [], true);
    }

/*---------------------------------      GET TRAINING BY ID     -------------------------------------*/

    /**
     * @Route("/getTrainingById", name="training_id", methods={"GET"})
     * @IsGranted("ROLE_TEACHER")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
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

// *******************************************************************************************************
// *****************************************   POST   ****************************************************
// *******************************************************************************************************

/*---------------------------------      POST A NEW TRAINING     -------------------------------------*/

    /**
     * @Route("/addTraining", name="add_training", methods={"POST"})
     * @IsGranted("ROLE_TEACHER")
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function addTraining(Request $request): Response
    {

        // On prend l'id du teacherUser
        $teacher = $this->getDoctrine()->getRepository(User::class)->findOneById((int)$request->request->get("teacher_id"));

        // On prend toutes les données envoyés en POST
        $start_training =       $request->request->get("start_training");
        $end_training =         $request->request->get("end_training");
        $max_student =          $request->request->get("max_student");
        $price_per_student =    $request->request->get("price_per_student");
        $training_description = $request->request->get("training_description");
        $subject =              $request->request->get("subject");

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
            }
            if (strtotime($end_training) >= strtotime($start_training)) {
                $training->setEndTraining(new DateTime($end_training));
            }else {
                $response->setContent(json_encode(["success" => FALSE]));
            }
            $training   ->setMaxStudent((int)$max_student)
                        ->setPricePerStudent($price_per_student)
                        ->setTrainingDescription($training_description)
                        ->setSubject($subject);
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        // On persist l'object = on l'écris dans la BDD
        try {
            $em->persist($training);
            $em->flush();
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        // On retourne un message de succes
        $response->setContent(json_encode(["success" => TRUE]));
        return $response;
    }

}