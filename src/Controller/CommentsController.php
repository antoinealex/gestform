<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Comments;
use App\Repository\CommentsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CommentsController extends AbstractController
{
    /* -----------*/
    /* ----GET----*/
    /* -----------*/


    /**
     * @Route("/comments", name="comments", methods={"GET"})
     * 
     */
    public function getAllComments(CommentsRepository $allComments, SerializerInterface $serializer)
    {
        $subject = $allComments->findAll(); //Récupérer tous les cours
        $resultat = $serializer->serialize( //Les transformer en format Json  
            $subject,                       //Il doit serialiser $subject
            'json',                         //Au format Json
            [
                'groups'  => ['listComments'] //Qui sont dans le groupe "listTraining"
            ]
        );
        return new JsonResponse($resultat, 200, [], true); //Retourne moi $resultat avec le code statut 200. "true" puisqu'il s'agit déjà de Json
    }

    /**
     * @Route("/commentsById", name="comments_id", methods={"GET"})
     */
    public function getCommentsById(Request $request, SerializerInterface $serializer)
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
        return new JsonResponse($resultat, Response::HTTP_OK, [], true); //Response::HTTP_ok équivaut à 200
    }


    /* ------------*/
    /* ----POST----*/
    /* ------------*/


    /**
     * @Route("/addComments", name="add_Comments", methods={"POST"})
     */
    public function addComments(Request $request): Response
    {
        // On prend l'id du teacherUser
        $user = $this->getDoctrine()->getRepository(User::class)->findOneById($request->request->get("user_id"));

        // On prend toutes les données envoyés en POST
        $title_comment = $request->request->get("title_comment");
        $body_comment = $request->request->get("body_comment");

        // On créé l'objet Training
        $em = $this->getDoctrine()->getManagerForClass(Comments::class);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $comment = new Comments();

        try {
            $comment->setUser($user);
            $comment->setTitleComment($title_comment);
            $comment->setBodyComment($body_comment);
            $comment->setDateComment(new DateTime("now"));
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => "erreur 1"]));
            return $response;
        }

        // On persist l'object = on l'écris dans la BDD
        try {
            $em->persist($comment);
            $em->flush();
        } catch (Exception $e) {
            $response->setContent(json_encode(["success" => "erreur 2"]));
            return $response;
        }

        // On retourne un message de succes
        $response->setContent(json_encode(["success" => TRUE]));
        return $response;

    }


    /* -----------*/
    /* ----PUT----*/
    /* -----------*/

    /**
     * @Route("/updateComment", name="update_comment", methods={"PUT"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateComment(Request $request): Response
    {
        //Get and decode Data from request body
        $requestParams = $request->getContent();
        $content = json_decode($requestParams, TRUE);

        //Fetch Data in local variables
        $commentId = $content["id"];
        $userId = $content["user_id"];
        $titleComment = $content["title_comment"];
        $bodyComment = $content["body_comment"];        


        //Get the event from DBAL
        $comment = $this->getDoctrine()->getRepository(Comments::class)->findCommentsById($commentId);

        //Get Entity Manager
        $em = $this->getDoctrine()->getManagerForClass(Comments::class);

        //Prepare HTTP Response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //Update event object
        try {
            $comment->setTitleComment($titleComment);
            $comment->setBodyComment($bodyComment);
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => "error 1"]));
        }

        //Persistence
        try {
            $em->persist($comment);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => "error 2"]));
        }
        return $response;
    }



    /* --------------*/
    /* ----DELETE----*/
    /* --------------*/

    /**
     * @Route("/deleteComment", name="delete_Comment", methods={"DELETE"})
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
            $Comment = $em->getRepository(Comments::class)->findCommentsById($CommentId);
        } catch (NonUniqueResultException $e) {
            $response->setContent(json_encode(["success" => "error 1"]));
        }

        //Remove object
        try {
            $em->remove($Comment);
            $em->flush();
            $response->setContent(json_encode(["success" => TRUE]));
        } catch (\Exception $e) {
            $response->setContent(json_encode(["success" => "error 2"]));
        }
        return $response;
    }

}
