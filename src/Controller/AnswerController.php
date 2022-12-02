<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\AnswerType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnswerController extends AbstractController
{
    #[Route("/answer/readAll", name: "answer-readAll")]
    public function readAll(ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Answer::class);
        $answers = $repository->findAll();
        return $this->render("answer/readAll.html.twig", [
            "answers" => $answers
        ]);
    }
    
    #[Route("/answer/read-all-by-question/{id}", name: "answer-readAllByQuestion")]
    public function readAllByQuestion(ManagerRegistry $doctrine, Question $question)
    {
        $repository = $doctrine->getRepository(Answer::class);
        $answers = $repository->findBy(
            ['question' => $question]
        );
        return $this->render("answer/readAllByQuestion.html.twig", [
            "answers" => $answers,
            "question" => $question
        ]);
    }

    #[Route("/answer/create/{id}", name: "answer-create")]
    public function create(Request $request, ManagerRegistry $doctrine, Question $question): Response
    {
        /* $this->denyAccessUnlessGranted("ROLE_USER");
        $this->denyAccessUnlessGranted("ROLE_ADMIN"); */
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($answer);
            $answer->setQuestion($question);
            $answer->setUser($this->getUser());
            $entityManager = $doctrine->getManager();

            $entityManager->persist($answer);
            $entityManager->flush();
            return  $this->redirectToRoute("answer-readAllByQuestion", ['id' => $question->getId()]);
        }
        return $this->render("answer/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/read/{id}", name: "read_question")]
    public function readQuestion(ManagerRegistry $doctrine, Question $question)
    {
        $answerRepository = $doctrine->getRepository(Answer::class);
        $answers = $answerRepository->findBy([
            "question" => $question
        ]);
        return $this->render("question/read.html.twig", [
            "question" => $question,
            "answer" => $answers,
        ]);
    }

    #[Route("/answer/readAnswer/{id}", name: "answer-read")]
    public function read(ManagerRegistry $doctrine, Question $question)
    {
        $repository = $doctrine->getRepository(Answer::class);
        $answers = $repository->findBy(
            ['question' => $question]
        );
        return $this->render("answer/readAnswer.html.twig", [
            "answers" => $answers,
            "question" => $question
        ]);
    }

    #[Route("/answer/update/{id}", name: "answer-update")]
    public function edit(Request $request, ManagerRegistry $doctrine, Answer $answer): Response
    {
        /* $this->denyAccessUnlessGranted("ROLE_USER");
        $this->denyAccessUnlessGranted("ROLE_ADMIN"); */
        $form = $this->createForm(AnswerType::class, $answer);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();
        }
        return $this->render("answer/update.html.twig", [
            "form" => $form->createView()
        ]);
    }


    #[Route("/answer/delete/{id}", name: "answer-delete")]
    public function delete(ManagerRegistry $doctrine, Answer $answer): Response
    {
        /* $this->denyAccessUnlessGranted("ROLE_USER");
        $this->denyAccessUnlessGranted("ROLE_ADMIN"); */
        $entityManager = $doctrine->getManager();
        $entityManager->remove($answer);
        $entityManager->flush();
        return $this->redirectToRoute("readAll");
    }

}
