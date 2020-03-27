<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\CalendarEvent;
use App\Repository\CalendarEventRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class TeacherController extends AbstractController
{
    /**
     * @var UserRepository
     * @var CalendarEventRepository
     */
    private $userrepository;
    private $calendareventrepository;


    public function __construct(UserRepository $userrepository, CalendarEventRepository $calendareventrepository, EntityManagerInterface $em)
    {
        $this->userrepository = $userrepository;
        $this->calendareventrepository = $calendareventrepository;
        $this->em = $em;
    }

    /**
     * @Route("/teacher_details", name= "teacher", methods={"GET"})
     * @return Response
     * @param Request
     */
    public function GetUserTeacher(Request $request): Response
    {
        $id = $request->query->get('id');
        $teacher = $this->userrepository->findTeacher($id);
        $teacherdatas = [];
        $teacherdatas[$teacher->getId()] = [
            'firstname' => $teacher->getFirstname(),
            'lastname' => $teacher->getLastname(),
            'role' => $teacher->getRoles()
        ];
        $response = new Response(json_encode($teacherdatas));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/teacher_students", name= "teacher.students", methods={"GET"})
     *
     * @return Respose
     */
    public function GetStudents(Request $request)
    {
        $userId = $request->query->get('userId');
        $teacherstudent = $this->calendareventrepository->findCalendarByUserId($userId);
        $studentdatas = [];
        foreach ($teacherstudent as $value) {
            $studentdatas[$value->getuserInvited()->getId()] = [
                'firstname' => $value->getuserInvited()->getFirstname(),
                'lastname' => $value->getuserInvited()->getLastname()

            ];
        }
        $response = new Response(json_encode($studentdatas));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/teacher_calendar", name= "teacher.calendar", methods={"GET"})
     *
     * @return Response
     */
    public function GetTeacherCalandar(Request $request)
    {
        $userId = $request->query->get('userId');
        $teachercalendar = $this->calendareventrepository->findCalendarByUserId($userId);
        $teacherdatas = [];
        foreach ($teachercalendar as $value) {
            $teacherdatas[$value->getId()] = [
                'user' => $value->getuser()->getId(),
                'startevent' => $value->getStartEvent(),
                'endevent' => $value->getEndEvent(),
                'eventdescription' => $value->getEventDescription()
            ];
        }
        $response = new Response(json_encode($teacherdatas));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/teacher_new", name= "NewTeacher", methods={"POST"})
     *
     * @param Request
     * @return Response
     *
     */
    public function NewTeacher(Request $request): Response
    {
        $newteacher = new User();
        $newteacher->setEmail($request->request->get('email'))
            ->setRoles([$request->request->get('role')])
            ->setPassword($request->request->get('password'))
            ->setLastname($request->request->get('lastname'))
            ->setFirstname($request->request->get('firstname'))
            ->setPhone($request->request->get('phone'))
            ->setAddress($request->request->get('address'))
            ->setPostcode($request->request->get('postcode'))
            ->setCity($request->request->get('city'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($newteacher);
        $em->flush();

        $response = new Response(json_encode('Bien crée avec succès!'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }



    /**
     * Undocumented function
     *
     * @return void
     */
    public function NewComments()
    {
    }

    /**
     * Used to update teacher
     * 
     *@Route("/teacher_update", name= "UpdateTeacher", methods={"PUT"})
     * @param Request $request
     * @return Response
     */
    public function UpdateTeacher(Request $request)
    {
        $teacherParams = $request->getContent();
        $content = json_decode($teacherParams, TRUE);

        $teacher = $this->userrepository->findTeacher($content["id"]);

        $email = $content["email"];
        $role = $content["role"];
        $password = $content["password"];
        $lastname = $content["lastname"];
        $firstname = $content["firstname"];
        $phone = $content["phone"];
        $address = $content["address"];
        $postcode = $content["postcode"];
        $city = $content["city"];

        $teacher->setEmail($email)
            ->setRoles([$role])
            ->setPassword($password)
            ->setLastname($lastname)
            ->setFirstname($firstname)
            ->setPhone($phone)
            ->setAddress($address)
            ->setPostcode($postcode)
            ->setCity($city);

        $em = $this->getDoctrine()->getManager();
        $em->persist($teacher);
        $em->flush();

        $response = new Response(json_encode('Bien modifié avec succès!'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Used to delete a Teacher
     * 
     * @Route("/teacher_delete", name= "DeleteTeacher", methods={"DELETE"})
     * @param Request $request
     * @return Response
     */
    public function DeleteTeacher(Request $request)
    {
        $id = $request->query->get('id');
        $teacher = $this->userrepository->findTeacher($id);
        $this->em->remove($teacher);
        $this->em->flush();

        $response = new Response(json_encode('Suprimé avec succès!'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
