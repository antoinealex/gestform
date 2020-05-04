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

    /*---------------------------------      GET CURRENT USER (USER)     -------------------------------------*/

    /**
     * @Route("/getCurrentUser", name="api_user_show", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function getCurrentUser(UserInterface $currentUser, Request $request): Response
    {
        $responseContent = [
            'id'        => $currentUser->getId(),
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
    // *****************************************   PUT   *****************************************************
    // *******************************************************************************************************


    /*---------------------------------      PUT CURRENT USER (USER)     -------------------------------------*/

    /**
     * @Route("/updateCurrentUser", name="currentuser", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     * @param UserInterface $currentUser
     * @param Request $request
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

    /*---------------------------------      PUT NEW PASSWORD     -------------------------------------*/
    /**
     * @Route("/passwordUpdate", name="password_update", methods={"PUT"})
     * @param Request $request
     * @param UserInterface $currentUser
     * @return Response
     */
    public function updateCurrentUserPassword(Request $request, UserInterface $currentUser) : Response
    {
        //Retrieve content
        $requestContent = json_decode(
            $request->getContent(),
            TRUE
        );

        //Get an entity manager
        $em = $this->getDoctrine()->getManagerForClass(User::class);

        //Check if old password is valid
        if(!$this->passwordEncoder->isPasswordValid($currentUser, $requestContent["oldPassword"])) {
            return new Response(
                json_encode(["success" => FALSE]),
                Response::HTTP_UNAUTHORIZED,
                ["Content-Type" => "application/json"]
            );
        }

        try {
            $currentUser->setPassword($this->passwordEncoder->encodePassword($currentUser, $requestContent["newPassword"]));
            $em->persist($currentUser);
            $em->flush();
        } catch (\Exception $e) {
            return new Response(
                json_encode(["success"=>FALSE]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ["Content-Type"=>"application/json"]
            );
        }
        
        return new Response(
            json_encode(["success"=>TRUE]),
            Response::HTTP_OK,
            ["Content-Type"=>"application/json"]
        );
    }


}
