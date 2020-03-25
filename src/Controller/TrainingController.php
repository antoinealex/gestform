<?php

namespace App\Controller;

use App\Repository\TrainingRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Training;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class TrainingController extends AbstractController
{
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


    /**
     * @Route("/training", name="training_create", methods={"POST"})
     */
    public function addTraining(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $data = $request->getContent();               //Obtenir le contenu de la requete
        $training = $serializer->deserialize($data,   // On instancie un nouveau cours "training" sans passer par new afin de gagner une ligne. Déserialise ce qu'il y a dans $data. 
        Training::class,                              // Déserialisation pour former un objet "training".  
        'json');                                      //Format du contenu à déserialiser = Json
        //$training est le resultat de la deserialisation de data
        $manager->persist($training);
        $manager->flush();

        return new JsonResponse("Le cours a été crée", Response::HTTP_CREATED, [ //Pour une création, on ne retourne pas de résultat, je retourn donc un resultat "null" ainsi que le code statut 201 signifiant que l'objet a bien été crée
            "location"=>"api/genres/" . $training->getId()      //ce nouvel objet sera joignable à cette adresse
        ], true);
    }

    /**
     * @Route("/training/{id}", name="training_update", methods={"PUT"})
     */
    public function updateNbStudents(Training $training, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $data=$request->getContent();
        $resultat = $serializer->deserialize($data, Training::class, 'json', ['object_to_populate'=>$training]);

        $manager->persist($training);
        $manager->flush();

        return new JsonResponse("Le cours a été modifié", Response::HTTP_OK, [], true); //Response::HTTP_ok équivaut à 200

    }

    /**
     * @Route("/training/{id}", name="training_delete", methods={"DELETE"})
     */
    public function deleteTraining(Training $training, EntityManagerInterface $manager)
    {
        $manager->remove($training);
        $manager->flush();

        return new JsonResponse("Le cours a été supprimé", Response::HTTP_OK, []); //On retire le "true" puisqu'on envoi rien qui est en json
    }

}
