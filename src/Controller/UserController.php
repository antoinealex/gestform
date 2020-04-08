<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="gestform_user")
 * 
 */

class UserController extends AbstractController
{

    /*---------------------------------     PASSWORD ENCODER      -----------------------------------------*/
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder=$passwordEncoder;
    }

    // *******************************************************************************************************
    // *****************************************   GET   *****************************************************
    // *******************************************************************************************************

    /*---------------------------------      GET ALL USERS (ADMIN)     -------------------------------------*/

    /**
     * @Route("/getAllUser", name="api_users_list", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function getAllUser(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $responseContent = [];
        foreach ($users as $user) {
            $responseContent[$user->getId()] = [
                'email'     => $user->getEmail(),
                'roles'     => $user->getRoles(),
                'lastname'  => $user->getLastname(),
                'firstname' => $user->getFirstname(),
                'phone'     => $user->getPhone(),
                'address'   => $user->getAddress(),
                'postcode'  => $user->getCity()
            ];
        }

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /*---------------------------------      GET USER BY ID (ADMIN)     -------------------------------------*/

    /**
     * @Route("/getUserByID", name="api_user_show_id", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function getUserByID(Request $request): Response
    {
        $userId = $request->query->get('id');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByID($userId);

        $responseContent = [
            'email'     => $user->getEmail(),
            'roles'     => $user->getRoles(),
            'lastname'  => $user->getLastname(),
            'firstname' => $user->getFirstname(),
            'phone'     => $user->getPhone(),
            'address'   => $user->getAddress(),
            'postcode'  => $user->getPostcode(),
            'city'      => $user->getCity()
        ];

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /*---------------------------------      GET CURRENT USER (USER)     -------------------------------------*/

    /**
     * @Route("/getCurrentUser", name="api_user_show", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function getCurrentUser(UserInterface $currentUser, Request $request): Response
    {
        $responseContent = [
            'email'     => $currentUser->getEmail(),
            'roles'     => $currentUser->getRoles(),
            'lastname'  => $currentUser->getLastname(),
            'firstname' => $currentUser->getFirstname(),
            'phone'     => $currentUser->getPhone(),
            'address'   => $currentUser->getAddress(),
            'postcode'  => $currentUser->getPostcode(),
            'city'      => $currentUser->getCity()
        ];

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    // *******************************************************************************************************
    // *****************************************   POST   ****************************************************
    // *******************************************************************************************************

    /*---------------------------------      POST A NEW USER (ADMIN)     -----------------------------------*/

    /**
     * @Route("/createUser", name="api_user_createUser", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function createUser(Request $request): Response
    {
        // On prend toutes les données envoyés en POST
        $email =        $request->request->get("email");
        $roles =        $request->request->get("roles");
        $password =     $request->request->get("password");
        $lastname =     $request->request->get("lastname");
        $firstname =    $request->request->get("firstname");
        $phone =        $request->request->get("phone");
        $address =      $request->request->get("address");
        $postcode =     $request->request->get("postcode");
        $city =         $request->request->get("city");

        // On créé l'objet Training
        $em = $this->getDoctrine()->getManagerForClass(User::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $user = new User();

        try {
            $user   ->setEmail($email)
                    ->setRoles([$roles])
                    ->setPassword($this->passwordEncoder->encodePassword($user,$password))
                    ->setLastname($lastname)
                    ->setFirstname($firstname)
                    ->setPhone($phone)
                    ->setAddress($address)
                    ->setPostcode($postcode)
                    ->setCity($city);
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        // On persist l'object = on l'écris dans la BDD
        try {
            $em->persist($user);
            $em->flush();
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        // On retourne un message de succes
        $response->setContent(json_encode(["success" => TRUE]));
        return $response;
    }

    // *******************************************************************************************************
    // *****************************************   PUT   *****************************************************
    // *******************************************************************************************************

    /*---------------------------------      PUT ANY USER (ADMIN)     --------------------------------------*/

    /**
     * @Route("/updateUser", name="update_user", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */

    public function updateUser(Request $request): Response
    {
        //On récupère les données dans le body de la requête
        $requestParams = $request->getContent();
        $content = json_decode($requestParams, TRUE);

        // On stocke les données temporairement dans des variables
        $userId =       $content["id"];
        $email  =       $content["email"];
        $roles =        $content["roles"];
        $password =     $content["password"];
        $lastname =     $content["lastname"];
        $firstname =    $content["firstname"];
        $phone =        $content["phone"];
        $address =      $content["address"];
        $postcode =     $content["postcode"];
        $city =         $content["city"];

        // On récupère l'utilisateur qui correspond à l'id donné dans la requête
        $user = $this->getDoctrine()->getRepository(User::class)->findOneById($userId);
        $em = $this->getDoctrine()->getManagerForClass(User::class);

        // On prèpare la réponse
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        // On modifie les données de l'utilisateur
        try {
            $user   ->setEmail($email)
                    ->setRoles([$roles])
                    ->setPassword($this->passwordEncoder->encodePassword($user,$password))
                    ->setLastname($lastname)
                    ->setFirstname($firstname)
                    ->setPhone($phone)
                    ->setAddress($address)
                    ->setPostcode($postcode)
                    ->setCity($city);
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        // On persiste l'objet modifié
        try {
            $em->persist($user);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }

    /*---------------------------------      PUT CURRENT USER (USER)     -------------------------------------*/

    /**
    * @Route("/updateCurrentUser", name="currentuser", methods={"PUT"})
    * @IsGranted("ROLE_USER")
    * @param UserInterface $currentUser
    * @return Response
    */

    public function updateCurrentUser(UserInterface $currentUser, Request $request): Response
    {

        // On récupère les données dans le body de la requête
        $requestParams = $request->getContent();
        $content = json_decode($requestParams, TRUE);

        // On stocke les données temporairement dans des variables
        $email  =       $content["email"];
        $lastname =     $content["lastname"];
        $firstname =    $content["firstname"];
        $phone =        $content["phone"];
        $address =      $content["address"];
        $postcode =     $content["postcode"];
        $city =         $content["city"];

        $em = $this->getDoctrine()->getManagerForClass(User::class);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        // On modifie les données de de l'utilisateur
        try {
            $currentUser    ->setEmail($email)
                            ->setLastname($lastname)
                            ->setFirstname($firstname)
                            ->setPhone($phone)
                            ->setAddress($address)
                            ->setPostcode($postcode)
                            ->setCity($city);
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        // On persiste l'objet modifié
        try {
            $em->persist($currentUser);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }

    // *******************************************************************************************************
    // *****************************************   DELETE   **************************************************
    // *******************************************************************************************************

    /*---------------------------------      DELETE USER BY ID (ADMIN)     ---------------------------------*/

    /**
     * @Route("/deleteUser", name="api_user_deleteUser", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */

    public function deleteUser(Request $request): Response
    {
        // On prepare la réponse à la requête
        $em = $this->getDoctrine()->getManagerForClass(User::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        // On récupère l'objet à supprimer dans la base de données
        $user = new User();
        try {
            $user = $em->getRepository(User::class)->findOneByID($request->request->get("id"));
        } catch (NonUniqueResultException $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        //On supprime l'objet User
        try {
            $em->remove($user);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }

}
