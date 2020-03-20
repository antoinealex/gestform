<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/api/admin/{roles}", name="api_admin_getGetUserByRole", methods={"GET"})
     */

    public function getGetUserByRole(UserRepository $repo, SerializerInterface $serializer){
        $students = $repo->findAll();
        $resultat = $serializer->serialize(
            $students,
            'json',
            [
                'groups' =>['listUserFull']
            ]
        );

        return new JsonResponse($resultat,200,[],true);
    }

    /**
     * @Route("/api/admin/{id}", name="api_admin_getGetUserByID", methods={"GET"})
     */
    public function getGetUserByID(User $user, SerializerInterface $serializer)
    {
        $resultat = $serializer->serialize(
            $user,
            'json', 
            [
                'groups' =>['listUserSimple']
            ]
        );

        return new JsonResponse($resultat,Response::HTTP_OK,[],true);
    }

    /**
     * @Route("/api/admin", name="api_admin_createUser", methods={"POST"})
     */
    public function createUser(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer){
        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json');

        $manager->persist($user);
        $manager->flush();

        return new JsonResponse(
            "L'utilisateur a bien été crée",
            Response::HTTP_CREATED,
            ["location" =>$this->generateUrl(
                'api_admin_getGetUserByID',
                ["id"=>$user->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )],
            true);
    }

    /**
     * @Route("/api/genres/{id}", name="api_genres_delete", methods={"DELETE"})
     */
    public function delete(User $user, EntityManagerInterface $manager)
    {
        $manager->remove($user);
        $manager->flush();
        return new JsonResponse("le genre a bien été supprimé",Response::HTTP_OK,[]);
    }
}
