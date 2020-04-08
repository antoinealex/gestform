<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Comments;
use App\Repository\CommentsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/comments", name="gestform_user")
 * 
 */

class CommentsController extends AbstractController
{
// *******************************************************************************************************
// *****************************************   GET   *****************************************************
// *******************************************************************************************************

    /*---------------------------------      GET ALL COMMENTS      -------------------------------------*/

    /**
     * @Route("/getAllComments", name="comments", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getAllComments(SerializerInterface $serializer) : JsonResponse
    {
        $subject = $this->getDoctrine()
            ->getRepository(Comments::class)->findAll();
        $resultat=[];

        foreach ($subject as $comments)
        {
            $resultat[$comments->getId()] = [
                "titleComment"  =>  $comments->getTitleComment(),
                "bodyComment"   =>  $comments->getBodyComment(),
                "dateComment"   =>  $comments->getDateComment()->format('Y-m-d H:i:s'),
                "user"          =>  [
                        "lastname"  =>  $comments->getUser()->getLastname(),
                        "firstname" =>  $comments->getUser()->getFirstname(),
                    ]
            ];
        }
        $resultat=json_encode($resultat);

        return new JsonResponse($resultat, 200, [], true);
    }

    /*---------------------------------      GET COMMENT BY ID      -------------------------------------*/

    /**
     * @Route("/getCommentsById", name="comments_id", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */

    public function getCommentsById(Request $request, SerializerInterface $serializer) : JsonResponse
    {
        $commentsId = $request->query->get('id');
        $comments =  $this->getDoctrine()->getRepository(Comments::class)->findCommentsById($commentsId);
        
        $resultat = $serializer->serialize(
            $comments,
            'json',
            [
                'groups'  => ['listComments']
            ]
        );
        return new JsonResponse($resultat, Response::HTTP_OK, [], true);
    }

// *******************************************************************************************************
// *****************************************   POST   ****************************************************
// *******************************************************************************************************

    /*---------------------------------      POST A COMMENT      -------------------------------------*/

    /**
     * @Route("/addComments", name="add_Comments", methods={"POST"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */

    public function addComments(Request $request): Response
    {
        // On prend l'id du teacherUser
        $user = $this->getDoctrine()->getRepository(User::class)->findOneById($request->request->get("user_id"));

        // On prend toutes les données envoyés en POST
        $title_comment =    $request->request->get("title_comment");
        $body_comment =     $request->request->get("body_comment");

        // On créé l'objet Training
        $em = $this->getDoctrine()->getManagerForClass(Comments::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $comment = new Comments();

        try {
            $comment    ->setUser($user)
                        ->setTitleComment($title_comment)
                        ->setBodyComment($body_comment)
                        ->setDateComment(new DateTime("now"));
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }

        // On persist l'object = on l'écris dans la BDD
        try {
            $em->persist($comment);
            $em->flush();
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => FALSE]));
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            return $response;
        }

        // On retourne un message de succes
        $response->setContent(json_encode(["success" => TRUE]));
        return $response;
    }

// *******************************************************************************************************
// *****************************************   PUT   *****************************************************
// *******************************************************************************************************

    /*---------------------------------      PUT A COMMENT    -------------------------------------*/

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

// *******************************************************************************************************
// *****************************************   DELETE   **************************************************
// *******************************************************************************************************

    /*---------------------------------      DELETE A COMMENT      -------------------------------------*/

    /**
     * @Route("/deleteComment", name="delete_Comment", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
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