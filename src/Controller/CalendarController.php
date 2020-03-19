<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

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
    private $calendarEm;

    /**
     * Entity Manager for user
     */
    private $userEm;

    public function __construct()
    {
        $this->calendarEm = $this->getDoctrine()->getRepository(CalendarEvent::class);
        $this->userEm = $this->getDoctrine()->getRepository(User::class);
    }
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
        $user = $this->userEm->findOneById($userId);
        $events = $user->getOwnCalendarEvents();
        foreach ($events as $event) {
            $eventsArray[] = $event->fetchArray();
        }

        return json_encode($eventsArray);
	}

	/**
	 * @Route("/event", name="event", methods={"GET"})
	 */
	public function getEventById() : Response
	{

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