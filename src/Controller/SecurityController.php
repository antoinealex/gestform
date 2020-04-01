<?php

/**
 * This file is part of the GestForma package.
 * For more information, please read the LICENSE file at the root directory of the project.
 */

namespace App\Controller;

use App\Entity\User;
use App\EventListener\LoginListener;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class SecurityController
 * @author Antoine ALEXANDRE <antoine@antoinealexandre.eu>
 * @Route("/security", name="security")
 * @package App\Controller
 */
class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @param UserPasswordEncoderInterface $encoder
     * @param EventDispatcher $eventDispatcher
     * @return Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils, UserPasswordEncoderInterface $encoder): Response
    {
        $username = $request->request->get("username");
        $password = $request->request->get("password");

        //Retrive a security encoder
        //$factory = $this->get("security.encoder_factory");

        //Retrieve user to authenticate
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(["email"=>$username]);

        //Prepare response object
        $response = new Response();
        $response->headers->set('Content-type', 'application/json');

        //If user doesn't exist
        if (!$user) {
            $response->setContent(json_encode(["success" => FALSE, "error"=>"Username doesn't exist"]));
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }

        //Check password and set token
        $salt = $user->getSalt();
        if(!$encoder->isPasswordValid($user, $password)) {
            $response->setContent(json_encode(["success" => FALSE, "error"=>"Password isn't valid"]));
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $response;
        }

        //To that point, credentials are valid. Setting the session follows
        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

        //Set successful response
        $response->setContent(json_encode(["success" => TRUE]));
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}