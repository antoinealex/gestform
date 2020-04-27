<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\CalendarEvent;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/calendar", name="gestform_calendar")
 */

class CalendarController extends AbstractController
{

    // *******************************************************************************************************
    // *****************************************   GET   *****************************************************
    // *******************************************************************************************************

    /*---------------------------------      GET CURRENTUSER EVENT (TEACHER)     -------------------------------------*/

    /**
     * @Route("/getCurrentUserEvents", name="current_user_events", methods={"GET"})
     * @IsGranted("ROLE_TEACHER")
     * @param UserInterface $currentUser
     * @return Response
     */

	public function getCurrentUserEvents(UserInterface $currentUser) : Response
	{
        $userId = $currentUser->getId();
        $events = $this->getDoctrine()->getRepository(CalendarEvent::class)->findByUserID($userId);
        if (!$events) {
            return new Response(
                json_encode(["error"=>"User not found or no events to show"]),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type'=>'application/json']
            );
        }
        // Serialization
        $responseContent = [];
        foreach($events as $event){
            $responseContent[$event->getId()] = [
                "id"            => $event->getId(),
                "user"          => $event->getuser()->getId(),
                "startEvent"    => $event->getStartEvent()->format('Y-m-d H:i:s'),
                "endEvent"      => $event->getEndEvent()->format('Y-m-d H:i:s'),
                "status"        => $event->getStatus(),
                "description"   => $event->getEventDescription()
            ];
            if ($event->getuserInvited())  {
                $responseContent[$event->getId()]["invitation"] = $event->getuserInvited()->getId();
            }
        }

        // Response
        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	}

    /*---------------------------------      GET AN EVENT BY ID (TEACHER)     -------------------------------------*/

    /**
     * @Route("/getEventById", name="event", methods={"GET"})
     * @IsGranted("ROLE_TEACHER")
     * @param Request $request
     * @param UserInterface $currentUser
     * @return Response
     */

	public function getEventById(Request $request, UserInterface $currentUser) : Response
	{
        $eventId = $request->query->get('id');
        try {
            $event =  $this->getDoctrine()->getRepository(CalendarEvent::class)->findOneByID($eventId);
        } catch (NonUniqueResultException $e) {
            return new Response(
                json_encode(["error"=>$e->getMessage()]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['Content-Type'=>'application/json']
            );
        }

        if (!$event) {
            return new Response(
                json_encode(["error"=>"Event not found"]),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type'=>'application/json']
            );
        } elseif ($event->getuser() != $currentUser) {
            return new Response(
                json_encode("VOUS N'ETES PAS LE PROPRIETAIRE DE CET EVENEMENT"),
                Response::HTTP_FORBIDDEN,
                ["Content-Type"=>"application/json"]
            );
        }

        // Serialization
        $responseContent = [
            "id"            => $event->getId(),
            "user"          => $event->getuser()->getId(),
            "startEvent"    => $event->getStartEvent()->format('Y-m-d H:i:s'),
            "endEvent"      => $event->getEndEvent()->format('Y-m-d H:i:s'),
            "status"        => $event->getStatus(),
            "description"   => $event->getEventDescription()
        ];
        if ($event->getuserInvited())  {
            $responseContent["invitation"] = $event->getuserInvited()->getId();
        }

        // Response
        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	}

	// *******************************************************************************************************
    // *****************************************   POST   *****************************************************
    // *******************************************************************************************************

    /*-----------------------------------      POST A NEW EVENT     ----------------------------------------*/

    /**
     * @Route("/newUserEvent", name="new_user_ev", methods={"POST"})
     * @IsGranted("ROLE_TEACHER")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */

	public function newUserEvent(Request $request, UserInterface $user) : Response
	{
	    //Get data from POST request

        $startEvent =   $request->request->get("startEvent");
        $endEvent =     $request->request->get("endEvent");
        $status =       $request->request->get("status");
        $description =  $request->request->get("eventDescription");

        //Check fields completion
        if (!$startEvent OR !$endEvent OR ($startEvent > $endEvent)) {
            return new Response(
                json_encode(["error"=>"Fields missing or incoherent"]),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type'=>'application/json']
            );
        }

        //Creation of objects
        $em = $this->getDoctrine()->getManagerForClass(CalendarEvent::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $event = new CalendarEvent();

        try {
            //Hydrate new event object
            $event  ->setuser($user)
                    ->setStartEvent(new DateTime($startEvent))
                    ->setEndEvent(new DateTime($endEvent))
                    ->setStatus($status)
                    ->setEventDescription($description);
        }
        catch (Exception $e) {
            $response   ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                        ->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        //Persistence
        try {
            $em->persist($event);
            $em->flush();
        }
        catch (Exception $e) {
            $response   ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                        ->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        //Return
        $response   ->setStatusCode(Response::HTTP_OK)
                    ->setContent(json_encode(["success" => TRUE]));
        return $response;
	}

    /*---------------------------------      POST A NEW APPOINTEMENT     -------------------------------------*/

    /**
     * @Route("/newUserAppointment", name="new_user_apt", methods={"POST"})
     * @IsGranted("ROLE_TEACHER")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */

	public function newUserAppointment(Request $request, UserInterface $user) : Response
	{
        //Get data from POST request

        $startEvent =   $request->request->get("startEvent");
        $endEvent =     $request->request->get("endEvent");
        $status =       $request->request->get("status");
        $description =  $request->request->get("eventDescription");
        try {
            $invitation =   $this->getDoctrine()->getRepository(User::class)->findOneById($request->request->get("idUserInvitation"));
        } catch (NonUniqueResultException $e) {
            return new Response(
                json_encode(["error"=>$e->getMessage()]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['Content-Type'=>'application/json']
            );
        }


        //Check fields completion
        if (!$startEvent OR !$endEvent OR !$invitation OR ($startEvent > $endEvent)) {
            return new Response(
                json_encode(["error"=>"Fields missing or incoherent"]),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type'=>'application/json']
            );
        }

        //Creation of objects
        $em = $this->getDoctrine()->getManagerForClass(CalendarEvent::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $event = new CalendarEvent();

        try {
            //Hydrate new event object
            $event  ->setuser($user)
                    ->setStartEvent(new DateTime($startEvent))
                    ->setEndEvent(new DateTime($endEvent))
                    ->setStatus($status)
                    ->setEventDescription($description)
                    ->setuserInvited($invitation);
        }
        catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        //Persistence
        try {
            $em->persist($event);
            $em->flush();
        }
        catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        //Return
        $response->setContent(json_encode(["success" => TRUE]));
        return $response;
	}

    // *******************************************************************************************************
    // *****************************************   PUT   *****************************************************
    // *******************************************************************************************************



    /*---------------------------------      PUT OWN EVENT (USER)     -------------------------------------*/

    /**
     * @Route("/updateCurrentUserEvent", name="update_current_user_event", methods={"PUT"})
     * @IsGranted("ROLE_USER")
     * @param UserInterface $currentUser
     * @param Request $request
     * @return Response
     */

    public function updateCurrentUserEvent(UserInterface $currentUser, Request $request): Response
    {
        //Get and decode Data from request body
        $requestParams =    $request->getContent();
        $content =          json_decode($requestParams, TRUE);

        //Fetch Data in local variables
        $eventId=       $content["eventId"];
        $startEvent =   $content["startEvent"];
        $endEvent =     $content["endEvent"];
        $status =       $content["status"];
        $description =  $content["eventDescription"];

        if (isset($content["idUserInvitation"])) {
            try {
                $invitation = $this->getDoctrine()->getRepository(User::class)->findOneById($content["idUserInvitation"]);
            } catch (NonUniqueResultException $e) {
                return new Response(
                    json_encode(["error"=>"Fields missing or incoherent"]),
                    Response::HTTP_BAD_REQUEST,
                    ['Content-Type'=>'application/json']
                );
            }

        } else {
            $invitation = NULL;
        }

        //Get the event from DBAL
        $event = $this->getDoctrine()->getRepository(CalendarEvent::class)->findOneByID($eventId);
        $eventOwner = $event->getuser();

        //Get Entity Manager
        $em = $this->getDoctrine()->getManagerForClass(CalendarEvent::class);

        //Prepare HTTP Response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if($currentUser == $eventOwner){
            //Update event object
            try {
                $event  ->setStartEvent(new DateTime($startEvent))
                        ->setEndEvent(new DateTime($endEvent))
                        ->setStatus($status)
                        ->setEventDescription($description)
                        ->setuserInvited($invitation);
            }
            catch (Exception $e) {
                $response->setContent(json_encode(["success" => FALSE]));
            }

            //Check dates coherence
            if ($startEvent > $endEvent) {
                return new Response(
                    json_encode(["error"=>"Dates are incoherent"]),
                    Response::HTTP_BAD_REQUEST,
                    ['Content-Type'=>'application/json']
                );
            }

            //Persistence
            try {
                $em->persist($event);
                $em->flush();
                $response->setContent(json_encode(["success" => TRUE]));
            }
            catch (Exception $e) {
                $response->setContent(json_encode(["success" => FALSE]));
            }
            return $response;
        }else{
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }
        
    }

    // *******************************************************************************************************
    // *****************************************   DELETE   **************************************************
    // *******************************************************************************************************

    /*---------------------------------      DELETE OWN EVENT (USER)     -------------------------------------*/

    /**
     * @Route("/deleteCurrentUserEvent", name="delete_current_user_event", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     */
    
    public function deleteCurrentUserEvent(UserInterface $currentUser, Request $request): Response
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

        $eventOwner = $event->getuser();

        if($currentUser == $eventOwner){
            //Remove object
            try {
                $em->remove($event);
                $em->flush();
                $response->setContent(json_encode(["success" => TRUE]));
            } catch (Exception $e) {
                $response->setContent(json_encode(["success" => FALSE]));
            }
            return $response;
        }else{
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }
    }
}