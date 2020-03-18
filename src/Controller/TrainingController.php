<?php

namespace App\Controller;

use App\Repository\TrainingRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;



class TrainingController extends AbstractController
{
    /**
     * @Route("/training", name="training", methods={"GET"})
     * 
     */
    public function getAllTraining(TrainingRepository $training, SerializerInterface $serializer)
    {
        $subject = $training->findAll();
        $resultat = $serializer->serialize(
            $subject,
            'json',
            [
                'groups'  => ['listTraining']
            ]
        );
        return new JsonResponse($resultat, 200, [], true);
    }

    /**
     * @Route("/training/{id}", name="trainingdetails", methods={"GET"})
     * 
     */
    public function getTrainingById(TrainingRepository $training, SerializerInterface $serializer)
    {
        $subject = $training->findAll();
        $resultat = $serializer->serialize(
            $subject,
            'json',
            [
                'groups'  => ['Trainingdetails']
            ]
        );
        return new JsonResponse($resultat, 200, [], true);
    }
}
