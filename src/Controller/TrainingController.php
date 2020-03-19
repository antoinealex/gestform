<?php

namespace App\Controller;

use App\Repository\TrainingRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
//use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Training;
use Symfony\Component\HttpFoundation\Response;

class TrainingController extends AbstractController
{
    /**
     * @Route("/training", name="training", methods={"GET"})
     * 
     */
    public function getAllTraining(TrainingRepository $training, SerializerInterface $serializer)
    {
        $subject = $training->findAll(); //Récupérer tous les cours
        $resultat = $serializer->serialize( //Les transformer en format Json  
            $subject, //Il doit serialiser $subject
            'json', //Au format Json
            [
                'groups'  => ['listTraining'] //Qui sont dans le groupe "listTraining"
            ]
        );
        return new JsonResponse($resultat, 200, [], true); //Retourne moi $resultat avec le code statut 200. "true" puisqu'il s'agit déjà de Json
    }

    /**
     * @Route("/training/{id}", name="training_details", methods={"GET"})
     * 
     */
    public function getTrainingById(Training $training, SerializerInterface $serializer)
    {
        $subject = $training->findAll();
        $resultat = $serializer->serialize(
            $subject,
            'json',
            [
                'groups'  => ['Trainingdetails']
            ]
        );
        return new JsonResponse($resultat, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/training", name="training_create", methods={"POST"})
     */
    public function addTraining(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $data = $request->getContent(); //Obtenir le contenu de la requete
        $training = $serializer->deserialize($data, // On instancie un nouveau cours "training" sans passer par new afin de gagner une ligne. Déserialise ce qu'il y a dans data. 
        Training::class, // Déserialisation pour former un objet de type "training".  
        'json'); //Format du contenu à déserialiser = Json
        //$training est le resultat de la deserialisation de data
        $manager->persist($training);
        $manager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED, [ //Pour une création, on ne retourne pas de résultat, je retourn donc un resultat "null" ainsi que le code statut 201 signifiant que l'objet a bien été crée
            "location"=>"api/genres/" . $training->getId() //ce nouvel objet sera joignable à cette adresse
        ], true);
    }

}
