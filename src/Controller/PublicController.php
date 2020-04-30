<?php


namespace App\Controller;


use App\Entity\Comments;
use App\Entity\Training;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class PublicController
 * @package App\Controller
 * @Route("/public", name="publicController")
 */
class PublicController extends AbstractController
{
    /*-------------TRAININGS----------------*/

    /**
     * Public API to retrieve all current & future trainings in a JSON encoded array.
     * @return Response
     * @Route("/getTrainings", name="getTrainings", methods={"GET"})
     */
    public function getTrainings() : Response {

        //Retrieve the trainings objects in persistence layer
        try {
            $trainings = $this->getDoctrine()->getRepository(Training::class)->findAll();
        } catch (\Exception $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ["Content-Type" =>  "application/json"]
            );
        }

        $responseContent = [];

        //Serialization of each objects
        foreach ($trainings as $training) {
            if($training->getStartTraining() >= new \DateTime("now")) {
                $responseContent[] = [
                    "id"            =>  $training->getId(),
                    "subject"       =>  $training->getSubject(),
                    "description"   =>  $training->getTrainingDescription(),
                    "start"         =>  $training->getStartTraining()->format("Y-m-d h:m"),
                    "end"           =>  $training->getEndTraining()->format("Y-m-d h:m"),
                ];
            }
        }

        //Encode the content and return the response
        $responseContent = json_encode($responseContent);

        return new Response(
            $responseContent,
            Response::HTTP_OK,
            ["Content-Type" =>  "application/json"]
        );
    }

    /**
     * Public API to retrieve one training given it's ID. Response format is a JSON encoded array.
     * @param Request $request
     * @return Response
     * @Route("/getTrainingById", name="getTrainingById", methods={"GET"})
     */
    public function getTrainingById(Request $request) : Response {

        //Retrieve the training object from persistence layer
        try {
            $training = $this->getDoctrine()->getRepository(Training::class)->findOneById($request->query->get('id'));
        } catch (NonUniqueResultException $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ["Content-Type" =>  "application/json"]
            );
        }

        //Check if the retrieved object is not null

        if (!$training) {
            return new Response(
                json_encode("This training was not found"),
                Response::HTTP_NOT_FOUND,
                ["Content-Type" =>  "application/json"]
            );
        }

        ////Serialization and encoding of the object
        $responseContent = json_encode([
            "id"            =>  $training->getId(),
            "subject"       =>  $training->getSubject(),
            "description"   =>  $training->getTrainingDescription(),
            "start"         =>  $training->getStartTraining()->format("Y-m-d h:m"),
            "end"           =>  $training->getEndTraining()->format("Y-m-d h:m"),
        ]);

        return new Response(
            $responseContent,
            Response::HTTP_OK,
            ["Content-Type" =>  "application/json"]
        );
    }

    /*-------------COMMENTS----------------*/

    /**
     * Public API to retrieve all the comments in a JSON encoded array.
     * @return Response
     * @Route("/getComments", name="getComments", methods={"GET"})
     */
    public function getComments() : Response {

        //Retrieve the comments in persistence layer
        try {
            $comments = $this->getDoctrine()->getRepository(Comments::class)->findAll();
        } catch (\Exception $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ["Content-Type" =>  "application/json"]
            );
        }

        $responseContent = [];

        //Serialization of comments object
        foreach ($comments as $comment) {
            $responseContent[] = [
                "id"    =>  $comment->getId(),
                "title" =>  $comment->getTitleComment(),
                "body"  =>  $comment->getBodyComment(),
                "date"  =>  $comment->getDateComment()->format("Y-m-d h:m"),
                "user"  =>  $comment->getUser()->getFirstname(),
            ];
        }

        //Encoding and returning the comments
        $responseContent = json_encode($responseContent);
        return new Response(
            $responseContent,
            Response::HTTP_OK,
            ["Content-Type" =>  "application/json"]
        );
    }

    /**
     * Public API to retrieve a single Comment, given it's ID is provided. Comment returned in an JSON encoded array.
     * @param Request $request
     * @return Response
     * @Route("/getCommentById", name="getCommentById", methods={"GET"})
     */
    public function getCommentById(Request $request) : Response {

        //Retrieve comment object from persistence layer
        try {
            $comment = $this->getDoctrine()->getRepository(Comments::class)->findCommentsById($request->query->get("id"));
        } catch (NonUniqueResultException $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ["Content-Type" =>  "application/json"]
            );
        }


        //Check if the comment is not null
        if (!$comment) {
            return new Response(
                json_encode("This comment was not found"),
                Response::HTTP_NOT_FOUND,
                ["Content-Type" =>  "application/json"]
            );
        }

        //Serialization and json encoding of the object
        $responseComment = json_encode([
            "id"    =>  $comment->getId(),
            "title" =>  $comment->getTitleComment(),
            "body"  =>  $comment->getBodyComment(),
            "date"  =>  $comment->getDateComment()->format("Y-m-d h:m"),
            "user"  =>  $comment->getUser()->getFirstname(),
        ]);

        return new Response(
            $responseComment,
            Response::HTTP_OK,
            ["Content-Type" =>  "application/json"]
        );
    }
}