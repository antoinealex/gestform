<?php

/**
 * This file is part of the GestForma package.
 * For more information, please read the LICENSE file at the root directory of the project.
 */

namespace App\Controller;

use App\Entity\User;
use App\EventListener\LoginListener;
use App\Service\SendMail;
use App\Template\ResetPasswordMailTemplate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
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
        //TODO Generate token and send it to the user
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