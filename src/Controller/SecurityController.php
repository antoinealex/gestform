<?php

/**
 * This file is part of the GestForma package.
 * For more information, please read the LICENSE file at the root directory of the project.
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @author Antoine ALEXANDRE <antoine@antoinealexandre.eu>
 * @Route("/security", name="security")
 * @package App\Controller
 */
class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="login", methods={"GET"})
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils): Response
    {
        $username = $request->request->get("username");
        $password = $request->request->get("password");

        $lastError = $authUtils->getLastAuthenticationError();

        $em = $this->getDoctrine()->getManagerForClass(User::class);
        $user = $em->getRepository(User::class)->findOneBy(["email" => $username]);
    }
}