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

/**
 * @Route("/teacher", name="gestform_teacher")
 * 
 */
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
     * @Route("/getStudents", name= "teacher.students", methods={"GET"})
     *
     * @return Response
     */

    public function getStudents(Request $request)
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
     * @Route("/getTeacherCalandar", name= "teacher.calendar", methods={"GET"})
     *
     * @return Response
     */

    public function getTeacherCalandar(Request $request)
    {
        $userId = $request->query->get('userId');
        $teachercalendar = $this->calendareventrepository->findCalendarByUserId($userId);
        $teacherdatas = [];
        foreach ($teachercalendar as $value) {
            $teacherdatas[$value->getId()] = [
                'user' => $value->getuser()->getId(),
                'userinvited' => $value->getuserInvited()->getId(),
                'startevent' => $value->getStartEvent(),
                'endevent' => $value->getEndEvent(),
                'status' => $value->getStatus(),
                'eventdescription' => $value->getEventDescription()
            ];
        }
        $response = new Response(json_encode($teacherdatas));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
