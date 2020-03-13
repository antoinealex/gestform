<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\CalendarEvent;
use App\Entity\User;

/**
 * @Route("/calendar", name="calendar_")
 */

class CalendarController extends AbstractController
{
	/* -----------*/
	/* ----GET----*/
	/* -----------*/

	/**
	 * @Route("/user_event", name="user", methods={"GET"})
	 */
	public function getUserEvents() : Response
	{

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