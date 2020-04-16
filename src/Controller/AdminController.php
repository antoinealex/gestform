<?php

namespace App\Controller;

use App\Entity\CalendarEvent;
use App\Entity\Comments;
use App\Entity\Training;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
  * Class AdminController
 * @package App\Controller
 * @Route("/admin", name="adminController")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{

    // *******************************************************************************************************
    // *****************************************   GET   *****************************************************
    // *******************************************************************************************************

    /*---------------------------------      GET ALL USERS (ADMIN)     -------------------------------------*/

    /**
     * @Route("/getAllUser", name="api_users_list", methods={"GET"})
     */
    public function getAllUser(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $responseContent = [];
        foreach ($users as $user) {
            $responseContent[$user->getId()] = [
                'email'     => $user->getEmail(),
                'roles'     => $user->getRoles(),
                'lastname'  => $user->getLastname(),
                'firstname' => $user->getFirstname(),
                'phone'     => $user->getPhone(),
                'address'   => $user->getAddress(),
                'postcode'  => $user->getCity()
            ];
        }

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /*---------------------------------      GET USER BY ID (ADMIN)     -------------------------------------*/

    /**
     * @Route("/getUserByID", name="api_user_show_id", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function getUserByID(Request $request): Response
    {
        $userId = $request->query->get('id');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneByID($userId);

        $responseContent = [
            'email'     => $user->getEmail(),
            'roles'     => $user->getRoles(),
            'lastname'  => $user->getLastname(),
            'firstname' => $user->getFirstname(),
            'phone'     => $user->getPhone(),
            'address'   => $user->getAddress(),
            'postcode'  => $user->getPostcode(),
            'city'      => $user->getCity()
        ];

        $response = new Response(json_encode($responseContent));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /*---------------------------------      GET ANY EVENT BY ID     -------------------------------------*/

    /**
     * @Route("/getAnyEventById", name="any_event", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */

    public function getAnyEventById(Request $request) : Response
    {
        $eventId = $request->query->get('id');
        $event =  $this->getDoctrine()->getRepository(CalendarEvent::class)->findOneByID($eventId);
        if (!$event) {
            return new Response(
                json_encode(["error"=>"Event not found"]),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type'=>'application/json']
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

    /*---------------------------------      GET ALL EVENTS BY USER (ADMIN)     -------------------------------------*/

    /**
     * @Route("/getUserEvents", name="user", methods={"GET"})
     * @param Request $request
     * @return Response
     */

    public function getUserEvents(Request $request) : Response
    {
        $userId = $request->query->get('id');
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

    // *******************************************************************************************************
    // *****************************************   POST   ****************************************************
    // *******************************************************************************************************

    /*---------------------------------      POST A NEW USER (ADMIN)     -----------------------------------*/

    /**
     * @Route("/createUser", name="api_user_createUser", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        // On prend toutes les données envoyés en POST
        $email =        $request->request->get("email");
        $roles =        $request->request->get("roles");
        $password =     $request->request->get("password");
        $lastname =     $request->request->get("lastname");
        $firstname =    $request->request->get("firstname");
        $phone =        $request->request->get("phone");
        $address =      $request->request->get("address");
        $postcode =     $request->request->get("postcode");
        $city =         $request->request->get("city");

        // On créé l'objet Training
        $em = $this->getDoctrine()->getManagerForClass(User::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $user = new User();

        try {
            $user   ->setEmail($email)
                ->setRoles([$roles])
                ->setPassword($encoder->encodePassword($user,$password))
                ->setLastname($lastname)
                ->setFirstname($firstname)
                ->setPhone($phone)
                ->setAddress($address)
                ->setPostcode($postcode)
                ->setCity($city);
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        // On persist l'object = on l'écris dans la BDD
        try {
            $em->persist($user);
            $em->flush();
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            return $response;
        }

        // On retourne un message de succes
        $response->setContent(json_encode(["success" => TRUE]));
        return $response;
    }

    // ******************************************************************************************************
    // *****************************************   PUT   ****************************************************
    // ******************************************************************************************************

    /*---------------------------------      PUT ANY USER (ADMIN)     --------------------------------------*/

    /**
     * @Route("/updateUser", name="update_user", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */

    public function updateUser(Request $request): Response
    {
        //On récupère les données dans le body de la requête
        $requestParams = $request->getContent();
        $content = json_decode($requestParams, TRUE);

        // On stocke les données temporairement dans des variables
        $userId =       $content["id"];
        $email  =       $content["email"];
        $roles =        $content["roles"];
        $lastname =     $content["lastname"];
        $firstname =    $content["firstname"];
        $phone =        $content["phone"];
        $address =      $content["address"];
        $postcode =     $content["postcode"];
        $city =         $content["city"];

        // On récupère l'utilisateur qui correspond à l'id donné dans la requête
        $user = $this->getDoctrine()->getRepository(User::class)->findOneById($userId);
        $em = $this->getDoctrine()->getManagerForClass(User::class);

        // On prèpare la réponse
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        // On modifie les données de l'utilisateur
        try {
            $user   ->setEmail($email)
                ->setRoles([$roles])
                ->setLastname($lastname)
                ->setFirstname($firstname)
                ->setPhone($phone)
                ->setAddress($address)
                ->setPostcode($postcode)
                ->setCity($city);
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        // On persiste l'objet modifié
        try {
            $em->persist($user);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }

    /*---------------------------------      PUT NEW PASSWORD     -------------------------------------*/
    /**
     * @Route("/changeUserPassword", name="password_admin", methods={"PUT"})
     * @param Request $request
     * @return Response
     */
    public function updateCurrentUserPassword(Request $request, UserPasswordEncoderInterface $encoder) : Response
    {
        //Retrieve content
        $requestContent = json_decode(
            $request->getContent(),
            TRUE
        );

        //Get an entity manager
        $em = $this->getDoctrine()->getManagerForClass(User::class);

        //Retrieve user
        $user = $em->getRepository(User::Class)->find($requestContent["id"]);


        try {
            $user->setPassword($encoder->encodePassword($user, $requestContent["password"]));
            $em->persist($user);
            $em->flush();
        } catch (\Exception $e) {
            return new Response(
                json_encode(["success"=>FALSE,
                            "error" => $e->getMessage()
                    ]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ["Content-Type"=>"application/json"]
            );
        }

        return new Response(
            json_encode(["success"=>TRUE]),
            Response::HTTP_OK,
            ["Content-Type"=>"application/json"]
        );
    }

    /*---------------------------------      UPDATE A TRAINING     -------------------------------*/

    /**
     * @Route("/updateTraining", name="update_training", methods={"PUT"})
     * @IsGranted("ROLE_TEACHER")
     * @param Request $request
     * @return Response
     */
    public function updateTraining(Request $request): Response
    {
        //Get and decode Data from request body
        $requestParams =    $request->getContent();
        $content       =    json_decode($requestParams, TRUE);

        //Fetch Data in local variables
        $trainingId           = $content["id"];
        $teacherId            = $content["teacher_id"];
        $start_training       = $content["startTraining"];
        $end_training         = $content["endTraining"];
        $max_student          = $content["maxStudent"];
        $price_per_student    = $content["pricePerStudent"];
        $training_description = $content["trainingDescription"];
        $subject              = $content["subject"];

        //Get the event from DBAL
        $training = $this->getDoctrine()->getRepository(Training::class)->findOneByID($trainingId);

        //Get Entity Manager
        $em = $this->getDoctrine()->getManagerForClass(Training::class);

        //Prepare HTTP Response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Update training object
        try {
            $training   ->setTeacher($this->getDoctrine()->getRepository(User::class)->findOneByID($teacherId))
                ->setStartTraining(new DateTime($start_training))
                ->setEndTraining(new DateTime($end_training))
                ->setMaxStudent((int)$max_student)
                ->setPricePerStudent($price_per_student)
                ->setTrainingDescription($training_description)
                ->setSubject($subject);
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        //Persistence
        try {
            $em->persist($training);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;

    }

    // ******************************************************************************************************
    // *****************************************   DELETE   *************************************************
    // ******************************************************************************************************


    /*---------------------------------      DELETE USER BY ID (ADMIN)     ---------------------------------*/

    /**
     * @Route("/deleteUser", name="api_user_deleteUser", methods={"DELETE"})
     * @param Request $request
     * @return Response
     */

    public function deleteUser(Request $request): Response
    {
        // On prepare la réponse à la requête
        $em = $this->getDoctrine()->getManagerForClass(User::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        // On récupère l'objet à supprimer dans la base de données
        $user = new User();
        try {
            $user = $em->getRepository(User::class)->findOneByID($request->query->get("id"));
        } catch (NonUniqueResultException $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        //On supprime l'objet User
        try {
            $em->remove($user);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }

    /*---------------------------------      DELETE TRAINING      ------------------------------*/

    /**
     * @Route("/deleteTraining", name="delete_training", methods={"DELETE"})
     * @IsGranted("ROLE_TEACHER")
     * @param Request $request
     * @return Response
     */
    public function deleteTraining(Request $request): Response
    {
        //Get Entity Manager and prepare response
        $em = $this->getDoctrine()->getManagerForClass(Training::class);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Get training object to delete
        $trainingId = $request->query->get("id");

        try {
            $training = $em->getRepository(Training::class)->findOneByID($trainingId);
        } catch (NonUniqueResultException $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }

        //Remove object
        try {
            $em->remove($training);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }

    /*---------------------------------      PUT ANY COMMENT (ADMIN)   -------------------------------------*/

    /**
     * @Route("/updateComment", name="update_comment", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */

    public function updateComment(Request $request): Response
    {
        //Get and decode Data from request body
        $requestParams =    $request->getContent();
        $content =          json_decode($requestParams, TRUE);

        //Fetch Data in local variables
        $commentId =    $content["comment_id"];
        $titleComment = $content["title_comment"];
        $bodyComment =  $content["body_comment"];


        //Get the event from DBAL
        $comment = $this->getDoctrine()->getRepository(Comments::class)->findCommentsById($commentId);

        //Get Entity Manager
        $em = $this->getDoctrine()->getManagerForClass(Comments::class);

        //Prepare HTTP Response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Update event object
        try {
            $comment->setTitleComment($titleComment)
                ->setBodyComment($bodyComment);
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        //Persistence
        try {
            $em->persist($comment);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /*---------------------------------      DELETE ANY COMMENT      -------------------------------------*/

    /**
     * @Route("/deleteComment", name="delete_Comment", methods={"DELETE"})
     * @param Request $request
     * @return Response
     */

    public function deleteComment(Request $request): Response
    {
        //Get Entity Manager and prepare response
        $em = $this->getDoctrine()->getManagerForClass(Comments::class);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Get training object to delete
        $CommentId = $request->query->get("id");

        try {
            $comment = $em->getRepository(Comments::class)->findCommentsById($CommentId);
        } catch (NonUniqueResultException $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        //Remove object
        try {
            $em->remove($comment);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
        }
        return $response;
    }
}