<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\QuestionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    #[Route("/question/readAll", name: "question-readAll")]
    public function readAll(ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Question::class);
        $questions = $repository->findAll();
        return $this->render("question/readAll.html.twig", [
            "questions" => $questions
        ]);
    }
    
    #[Route("/question/read-all-by-quiz/{id}", name: "question-readAllByQuiz")]
    public function readAllByQuiz(ManagerRegistry $doctrine, Quiz $quiz)
    {
        if ($quiz->getUser() != $this->getUser()) {  // $post->getUser() or another method that returns the user of your post
            return $this->json(['code' => 403, 'error' => 'c est votre post  !'], 403);
        }
        $repository = $doctrine->getRepository(Question::class);
        $questions = $repository->findBy(
            ['quiz' => $quiz]
        );
        return $this->render("question/readAllByQuiz.html.twig", [
            "questions" => $questions,
            "quiz" => $quiz
        ]);
    }

    #[Route("/question/create/{id}", name: "question-create")]
    public function create(Request $request, ManagerRegistry $doctrine, Quiz $quiz): Response
    {
        /* $this->denyAccessUnlessGranted("ROLE_USER");
        $this->denyAccessUnlessGranted("ROLE_ADMIN"); */

        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($question);
            $question->setQuiz($quiz);
            $question->setUser($this->getUser());
            $entityManager = $doctrine->getManager();

            $entityManager->persist($question);
            $entityManager->flush();
            return  $this->redirectToRoute("question-readAllByQuiz", ['id' => $quiz->getId()]);
        }
        return $this->render("question/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/question/readQuestion/{id}", name: "question-read")]
    public function read(ManagerRegistry $doctrine, Quiz $quiz)
    {
        if ($quiz->getUser() != $this->getUser()) {  // $post->getUser() or another method that returns the user of your post
            return $this->json(['code' => 403, 'error' => 'c est votre post  !'], 403);
        }
        $repository = $doctrine->getRepository(Question::class);
        $questions = $repository->findBy(
            ['quiz' => $quiz]
        );
        return $this->render("question/readQuestion.html.twig", [
            "questions" => $questions,
            "quiz" => $quiz
        ]);
    }


    #[Route("/question/update/{id}", name: "question-update")]
    public function edit(Request $request, ManagerRegistry $doctrine, Question $question): Response
    {
        /* $this->denyAccessUnlessGranted("ROLE_USER");
        $this->denyAccessUnlessGranted("ROLE_ADMIN"); */
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();
        }
        return $this->render("question/update.html.twig", [
            "form" => $form->createView()
        ]);
    }


    #[Route("/question/delete/{id}", name: "question-delete")]
    public function delete(ManagerRegistry $doctrine, Question $question): Response
    {
        /* $this->denyAccessUnlessGranted("ROLE_USER");
        $this->denyAccessUnlessGranted("ROLE_ADMIN"); */
        $entityManager = $doctrine->getManager();
        $entityManager->remove($question);
        $entityManager->flush();
        return $this->redirectToRoute("readAll");
    }
}
