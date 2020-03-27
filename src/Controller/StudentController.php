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
	 * @Route("/userstudent", name="userstudent", methods={"GET"})
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

	/**
	 * @Route("/allstudent", name="allstudent", methods={"GET"})
	 */
	public function getAllStudent(UserRepository $allStudent, SerializerInterface $serializer) : Response
	{
		$result = $allStudent->findAll(); //Récupérer les étudiants
        $liststudent = $serializer->serialize( //Les transformer en format Json  
            $result, //serialiser $result
            'json', //Au format Json
            [
                'liste etudiant'  => ['listStudent'] //Qui sont dans le groupe "listStudent"
            ]
        );
        return new JsonResponse($liststudent);// retour au format json
	}

	/**
	 * @Route("/studcalendar", name="studcalendar", methods={"GET"})
	 */
	public function getStudentCalendar  (Request $request) : Response
	{
		$eventId = $request->query->get('id');
        $studentcalendar =  $this->getDoctrine()->getRepository(CalendarEvent::class)->findOneByID($eventId);
        //Serialization
        $responseContent = [
            "id" => $studentcalendar->getId(),
			"user" => $studentcalendar->getuser()->getId(),
			//"role" => $studentcalendar->getRoles(),
            "startEvent" => $studentcalendar->getStartEvent(),
            "endEvent" => $studentcalendar->getEndEvent(),
            "description" => $studentcalendar->getEventDescription()
        ];
     
        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	}
	/* -----------*/
	/* ----POST---*/
	/* -----------*/

	/**
	 * @Route("/User", name="newstudent", methods={"POST"})
	 */
	/*public function newStudent(Request $request ) : Response
	{
		if ($user->hasRole('STUDENT')) //vérifie si le user est un étudiant
		{
            $id = $request->query->get('id');
            $email = $request->query->get('email');
            $roles = $request->query->get('roles');
            $password = $request->query->get('password');
            $lastname = $request->query->get('lastname');
            $firstname = $request->query->get('firstname');
            $phone = $request->query->get('phone');
            $address = $request->query->get('adress');
            $postcode = $request->query->get('postcode');
            $city = $request->query->get('city');
        }
	}*/

	/**
	 * @Route("/comments", name="newcomment", methods={"POST"})
	 */
	
}