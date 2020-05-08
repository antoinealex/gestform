<?php

/**
 * This file is part of the GestForma package.
 * For more information, please read the LICENSE file at the root directory of the project.
 */

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Service\SendMail;
use App\EventListener\LoginListener;
use App\Template\ResetPasswordMailTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class SecurityController
 * @author Antoine ALEXANDRE <antoine@antoinealexandre.eu>
 * @Route("/security", name="security")
 * @package App\Controller
 */
class SecurityController extends AbstractController
{


    /**
     * @Route("/test")
     * @param SendMail $mailer
     * @param UserInterface $currentUser
     */
    public function test(SendMail $mailer, UserInterface $currentUser) {
        $mailer->setRecipient($currentUser);
        $mailer->sendResetPasswordMail();
        $mailer->send();
    }

    /**
     * @param Request $request
     * @param SendMail $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     * @Route("/forgotpassword", name="forgot_password", methods={"POST"})
     */
    public function forgotPasswordSendMail(Request $request, SendMail $mailer, TokenGeneratorInterface $tokenGenerator) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $exprirationDate = new DateTime('+48 hours');

        //On récupère l'email de la requête
        $email = $request->request->get("email");
        //On essaie de récupérer l'email en bdd qui est identique à la requête  
        try {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]); 
        } catch (\Exception $e) {
            return new Response(
                json_encode(["success" => FALSE]),
                Response::HTTP_FORBIDDEN,
                ['Content-Type'=>'application/json']
            );        
        }
        //On vérifie si l'email existe
        if ($user == null) {
            return new Response(
                json_encode(["error"=>"Invalid email"]),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type'=>'application/json']
            );
        }else {
            //On crée un token
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);
            $user->setResetTokenExpiration($exprirationDate);
            try{
                $em->persist($user);
                $em->flush();
            }catch (\Exception $e) {
                return new Response(
                    json_encode(["success" => FALSE]),
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ['Content-Type'=>'application/json']
                );
            };
            //On envoie le mail avec un lien vers la page de reset
            $mailer->setRecipient($user);
            $mailer->sendResetPasswordMail();
            $mailer->send();
        }
        return new Response(
            json_encode(["success" => TRUE]),
            Response::HTTP_OK,
            ['Content-Type'=>'application/json']
        );
    }

    /**
     * @param Request $request
     * @Route("/resetPassword", name="reset_password", methods={"POST"})
     * @return Response
     */
    public function resetPasswordWithToken(Request $request) : Response
    {
        //TODO retrieve token and new password in POST query. Retrieve


    }
}