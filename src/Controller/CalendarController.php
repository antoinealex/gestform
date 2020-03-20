<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\CalendarEvent;
use App\Entity\User;

/**
 * @Route("/calendar", name="calendar_")
 */

class CalendarController extends AbstractController
{
    /**
     * Entity Manager for Calendar event
     */
    //private $calendarEm;

    /**
     * Entity Manager for user
     */
    //private $userEm;
/*
    public function __construct()
    {
        $this->calendarEm = $this->getDoctrine()->getRepository(CalendarEvent::class);
        $this->userEm = $this->getDoctrine()->getRepository(User::class);
    }*/

    /* -----------*/
	/* ----GET----*/
	/* -----------*/

    /**
     * @Route("/user_event", name="user", methods={"GET"})
     * @param Request $request
     * @return Response
     */
	public function getUserEvents(Request $request) : Response
	{
        $userId = $request->query->get('userID');
        $events =  $this->getDoctrine()->getRepository(CalendarEvent::class)->findByUserID($userId);
        //Serialization
        $responseContent = [];
        foreach($events as $event){
            $responseContent[$event->getId()] = [
                "user" => $event->getuser()->getId(),
                "startEvent" => $event->getStartEvent(),
                "endEvent" => $event->getEndEvent(),
                "status" => $event->getStatus(),
                "description" => $event->getEventDescription()
            ];
            if ($event->getuserInvited())  {
                $responseContent[$event->getId()]["invitation"] = $event->getuserInvited()->getId();
            }
        }

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	}

	/**
	 * @Route("/event", name="event", methods={"GET"})
	 */
	public function getEventById(Request $request) : Response
	{
        $eventId = $request->query->get('id');
        $event =  $this->getDoctrine()->getRepository(CalendarEvent::class)->findOneByID($eventId);
        //Serialization
        $responseContent = [
            "id" => $event->getId(),
            "user" => $event->getuser()->getId(),
            "startEvent" => $event->getStartEvent(),
            "endEvent" => $event->getEndEvent(),
            "status" => $event->getStatus(),
            "description" => $event->getEventDescription()
        ];
        if ($event->getuserInvited())  {
            $responseContent["invitation"] = $event->getuserInvited()->getId();
        }
        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	}

	/* -----------*/
	/* ----POST---*/
	/* -----------*/

	/**
	 * @Route("/new_user_event", name="new_user_ev", methods={"POST"})
	 */
	public function newUserEvent() : Response
	{

	}

	/**
	 * @Route("/new_user_appointment", name="new_user_apt", methods={"POST"})
	 */
	public function newUserAppointment() : Response
	{

	}

}