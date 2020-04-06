<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\CalendarEvent;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CalendarEventRepository;

/**
 * @Route("/student", name="student_")
 */

class StudentController extends AbstractController
{
	/* -----------*/
	/* ----GET----*/
	/* -----------*/

	/**
	 * @Route("/getUserStudent", name="userstudent", methods={"GET"})
	 * @return Response
	 */
    
	public function getUserStudent(Request $request) : Response
	{
		$userRole = $request->query->get('roles'); //récupère la requête
		$students =  $this->getDoctrine()->getRepository(User::class)->findOneById($userRole);
        //Serialization
        $responseContent = [];
        foreach($students as $student){
            $responseContent[$student->getId()] = [
                "user" => $student->getuser()->getId(),
                "role" => $student->getRoles()                
            ];
            if ($student->getuserInvited())  {
                $responseContent[$student->getId()]["invitation"] = $student->getuserInvited()->getId();
            }
        }

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
       
	}
}