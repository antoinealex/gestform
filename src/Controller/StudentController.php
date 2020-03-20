<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\CalendarEvent;
use App\Entity\Comments;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;



/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */

class StudentController extends AbstractController
{
	/* -----------*/
	/* ----GET----*/
	/* -----------*/

	/**
	 * @Route("/student", name="student", methods={"GET"})
	 */
	public function getUserStudent() : Response
	{

	}

	/**
	 * @Route("/student", name="allstudent", methods={"GET"})
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
	 * @Route("/student", name="studentcalendar", methods={"GET"})
	 */
	public function getStudentCalendar  () : Response
	{

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
	 * @Route("/comment", name="newcomment", methods={"POST"})
	 */
	public function newComment(Request $request, SerializerInterface $serializer) : Response
	{
		// récupérer les composants d'un commentaire
		$result = $request->Comments();

		//instancier un nouveau commentaire
		$comment = $serializer->deserialize($result,
        Comments::class,                               
		'json');  
		
		return new JsonResponse($comment);
	}

}