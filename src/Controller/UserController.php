<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

// *******************************************************************************************************
// *****************************************   GET   *****************************************************
// *******************************************************************************************************

    /**
     * @Route("/users", name="api_users_list", methods={"GET"})
     */
    public function getAllUser(): Response
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findAll();

        $responseContent = [];
        foreach($users as $user)
        {
            $responseContent[$user->getId()] = [
                'email'=>$user->getEmail(),
                'roles'=>$user->getRoles(),
                'lastname'=>$user->getLastname(),
                'firstname'=>$user->getFirstname(),
                'phone'=>$user->getPhone(),
                'address'=>$user->getAddress(),
                'postcode'=>$user->getCity()
            ];
        }

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/user", name="api_user_show", methods={"GET"})
     */
    public function getGetUserByID(Request $request): Response
    {
        $userId = $request->query->get('id');
        $user=$this->getDoctrine()->getRepository(User::class)->findOneByID($userId);

        $responseContent = [
            'email'=>$user->getEmail(),
            'roles'=>$user->getRoles(),
            'lastname'=>$user->getLastname(),
            'firstname'=>$user->getFirstname(),
            'phone'=>$user->getPhone(),
            'address'=>$user->getAddress(),
            'postcode'=>$user->getCity()
        ];

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

// *******************************************************************************************************
// *****************************************   POST   ****************************************************
// *******************************************************************************************************

    /**
     * @Route("/user", name="api_user_createUser", methods={"POST"})
     */
    public function createUser(Request $request): Response
    {
        // On prend toutes les données envoyés en POST
        $email = $request->request->get("email");
        $roles = $request->request->get("roles");
        $password = $request->request->get("password");
        $lastname = $request->request->get("lastname");
        $firstname = $request->request->get("firstname");
        $phone = $request->request->get("phone");
        $address = $request->request->get("address");
        $postcode = $request->request->get("postcode");
        $city = $request->request->get("city");

        // On créé l'objet Training
        $em = $this->getDoctrine()->getManagerForClass(User::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $user = new User();

        try {
            $user   ->setEmail($email)
                    ->setRoles($roles)
                    ->setPassword($password)
                    ->setLastname($lastname)
                    ->setFirstname($firstname)
                    ->setPhone($phone)
                    ->setAddress($address)
                    ->setPostcode($postcode)
                    ->setCity($city);

        } catch (Exception $e) {
            $response->setContent(json_encode(["error" => FALSE]));
            return $response;
        }

        // On persist l'object = on l'écris dans la BDD
        try {
            $em->persist($user);
            $em->flush();
        } catch (Exception $e) {
            $response->setContent(json_encode(["error" => FALSE]));
            return $response;
        }

        // On retourne un message de succes
        $response->setContent(json_encode(["success" => TRUE]));
        return $response;
    }

// *******************************************************************************************************
// *****************************************   PUT   *****************************************************
// *******************************************************************************************************

    /**
     * @Route("/users/{id}", name="api_user_update", methods={"PUT"})
     */
    public function edit(User $user, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $data = $request->getContent();
        //$dataTab = $serializer->decode($data, 'json');
        // $nationalite = $repoNation->find($dataTab['nationalite']['id']);
        $serializer->deserialize($data, User::class, 'json', ['object_to_populate' => $user]);
        // $user->setLastname($data[]);
 
        $manager->persist($user);
        $manager->flush();
 
        return new JsonResponse("l'user a bien été modifié", Response::HTTP_OK, [], true);
    }

    // /**
    //  * @Route("/user", name="update_user", methods={"PUT"})
    //  * @param Request $request
    //  * @return Response
    //  * @throws \Doctrine\ORM\NonUniqueResultException
    //  */
    // public function updateUser(Request $request): Response
    // {
    //     //On récupère les données dans le body de la requête
    //     $requestParams = $request->getContent();
    //     $content = json_decode($requestParams, TRUE);

    //     //On stocke les données temporairement dans des variables
    //     $userId = $content["id"];
    //     $email  = $content["email"];
    //     $roles = $content["roles"];
    //     $password = $content["password"];
    //     $lastname = $content["lastname"];
    //     $firstname = $content["firstname"];
    //     $phone = $content["phone"];
    //     $address = $content["address"];
    //     $postcode = $content["postcode"];
    //     $city = $content["city"];

    //     // On récupère l'utilisateur qui correspond a l'id donné dans la requête
    //     $user = $this->getDoctrine()->getRepository(User::class)->findOneById($userId);
    //     $em = $this->getDoctrine()->getManagerForClass(User::class);

    //     //On prèpare la réponse
    //     $response = new Response();
    //     $response->headers->set('Content-Type', 'application/json');

    //     // On modifie les données de de l'utilisateur
    //     try {
    //         $user   ->setEmail($email)
    //                 ->setRoles($roles)
    //                 ->setPassword($password)
    //                 ->setLastname($lastname)
    //                 ->setFirstname($firstname)
    //                 ->setPhone($phone)
    //                 ->setAddress($address)
    //                 ->setPostcode($postcode)
    //                 ->setCity($city);
    //     } catch (Exception $e) {
    //         $response->setContent(json_encode(["success" => FALSE]));
    //     }

    //     // On persiste l'objet modifié
    //     try {
    //         $em->persist($user);
    //         $em->flush();
    //         $response->setContent(json_encode(["success" => TRUE]));
    //     } catch (Exception $e) {
    //         $response->setContent(json_encode(["success" => FALSE]));
    //     }
    //     return $response;
    // }

// *******************************************************************************************************
// *****************************************   DELETE   **************************************************
// *******************************************************************************************************

    /**
     * @Route("/user", name="api_user_deleteUser", methods={"DELETE"})
     */
    public function deleteUser(Request $request): Response
    {
        // On prepare la réponse à la requête
        $em = $this->getDoctrine()->getManagerForClass(User::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        
        // On récupère l'objet à supprimer dans la base de données
        $user=new User();
        try {
            $user=$em->getRepository(User::class)->findOneByID($request->request->get("id"));
        } catch (NonUniqueResultException $e) {
            $response->setContent(json_encode(["error" => FALSE]));
        }

        //On supprime l'objet User
        try {
            $em->remove($user);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (Exception $e) {
            $response->setContent(json_encode(["error" => FALSE]));
        }
        return $response;
    }
}
