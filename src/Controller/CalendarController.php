<?php

namespace App\Controller;

use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;

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
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function getEventById(Request $request
    ) : Response
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
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
	public function newUserEvent(Request $request) : Response
	{
	    //Get data from POST request
        $user = $this->getDoctrine()->getRepository(User::class)->findOneById($request->request->get("userID"));
        $startEvent = $request->request->get("startEvent");
        $endEvent = $request->request->get("endEvent");
        $status = $request->request->get("status");
        $description = $request->request->get("eventDescription");

        //Creation of objects
        $em = $this->getDoctrine()->getManagerForClass(CalendarEvent::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $event = new CalendarEvent();

        try {
            //Hydrate new event object
            $event->setuser($user);
            $event->setStartEvent(new DateTime($startEvent));
            $event->setEndEvent(new DateTime($endEvent));
            $event->setStatus($status);
            $event->setEventDescription($description);
        }
        catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        //Persistence
        try {
            $em->persist($event);
            $em->flush();
        }
        catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        //Return
        $response->setContent(json_encode(["success" => TRUE]));
        return $response;
	}

    /**
     * @Route("/new_user_appointment", name="new_user_apt", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function newUserAppointment(Request $request) : Response
	{
        //Get data from POST request
        $user = $this->getDoctrine()->getRepository(User::class)->findOneById($request->request->get("userID"));
        $startEvent = $request->request->get("startEvent");
        $endEvent = $request->request->get("endEvent");
        $status = $request->request->get("status");
        $description = $request->request->get("eventDescription");
        $invitation = $this->getDoctrine()->getRepository(User::class)->findOneById($request->request->get("idUserInvitation"));

        //Creation of objects
        $em = $this->getDoctrine()->getManagerForClass(CalendarEvent::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $event = new CalendarEvent();

        try {
            //Hydrate new event object
            $event->setuser($user);
            $event->setStartEvent(new DateTime($startEvent));
            $event->setEndEvent(new DateTime($endEvent));
            $event->setStatus($status);
            $event->setEventDescription($description);
            $event->setuserInvited($invitation);
        }
        catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        //Persistence
        try {
            $em->persist($event);
            $em->flush();
        }
        catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        //Return
        $response->setContent(json_encode(["success" => TRUE]));
        return $response;
	}

    /* -----------*/
    /* ----PUT----*/
    /* -----------*/

    /**
     * @Route("/update_event", name="update_event", methods={"PUT"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateEvent(Request $request): Response
    {
        //Get and decode Data from request body
        $requestParams = $request->getContent();
        $content = json_decode($requestParams, TRUE);

        //Fetch Data in local variables
        $eventId= $content["eventId"];
        $startEvent = $content["startEvent"];
        $endEvent = $content["endEvent"];
        $status = $content["status"];
        $description = $content["eventDescription"];
        if (isset($content["idUserInvitation"])) {
            $invitation = $this->getDoctrine()->getRepository(User::class)->findOneById($content["idUserInvitation"]);
        } else {
            $invitation = NULL;
        }

        //Get the event from DBAL
        $event = $this->getDoctrine()->getRepository(CalendarEvent::class)->findOneByID($eventId);

        //Get Entity Manager
        $em = $this->getDoctrine()->getManagerForClass(CalendarEvent::class);

        //Prepare HTTP Response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Update event object
        try {
            $event->setStartEvent(new DateTime($startEvent));
            $event->setEndEvent(new DateTime($endEvent));
            $event->setStatus($status);
            $event->setEventDescription($description);
            $event->setuserInvited($invitation);
        }
        catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        //Persistence
        try {
            $em->persist($event);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        }
        catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }

    /* ------------*/
    /* ---DELETE---*/
    /* ------------*/

    /**
     * @Route("/delete_event", name="delete_event", methods={"DELETE"})
     * @param Request $request
     * @return Response
     */
    public function deleteEvent(Request $request): Response
    {
        //Get Entity Manager and prepare response
        $em = $this->getDoctrine()->getManagerForClass(CalendarEvent::class);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Get event object to to delete
        $eventId = $request->query->get("eventId");
        try {
            $event = $em->getRepository(CalendarEvent::class)->findOneByID($eventId);
        } catch (NonUniqueResultException $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        //Remove object
        try {
            $em->remove($event);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }
}