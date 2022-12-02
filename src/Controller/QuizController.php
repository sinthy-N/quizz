<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Theme;
use App\Entity\User;

use App\Form\QuizType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    #[Route("/quiz/readAll", name: "quiz-readAll")]
    public function readAll(ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Quiz::class);
        $quizs = $repository->findAll();
        return $this->render("quiz/readAll.html.twig", [
            "quizs" => $quizs
        ]);
    }

    #[Route("/quiz/read-all-by-theme/{id}", name: "quiz-readAllByTheme")]
    public function readAllByTheme(ManagerRegistry $doctrine, Theme $theme, /* User $user */)
    {
        $repository = $doctrine->getRepository(Quiz::class);
        $quizs = $repository->findBy(
            ['theme' => $theme]
        );
        /* $quizs = $this->getUser(); */
        /* $quizs = $repository->find($id); */

        return $this->render("quiz/readAllByTheme.html.twig", [
            "quizs" => $quizs,
            "theme" => $theme,
        ]);
    }

    #[Route("/quiz/read/{id}", name: "quiz-read")]
    public function read(ManagerRegistry $doctrine, Quiz $quiz)
    {
        if ($quiz->getUser() != $this->getUser()) {  // $post->getUser() or another method that returns the user of your post
            return $this->json(['code' => 403, 'error' => 'c est votre post  !'], 403);
        }
        $repository = $doctrine->getRepository(Question::class);
        $questions = $repository->findBy(
            ['quiz' => $quiz]
        );
        return $this->render("quiz/read.html.twig", [
            "questions" => $questions,
            "quiz" => $quiz
        ]);
    }

    
    public function quizAction(Request $request)
    {
       dump($request);
    }


    #[Route("/quiz/create/{id}", name: "quiz_create")]
    public function create(Request $request, ManagerRegistry $doctrine, Theme $theme): Response
    {
        /*         $this->denyAccessUnlessGranted("ROLE_USER");
        $this->denyAccessUnlessGranted("ROLE_ADMIN"); */
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($quiz);
            $quiz->setTheme($theme);
            $quiz->setUser($this->getUser());
            $entityManager = $doctrine->getManager();

            $entityManager->persist($quiz);
            $entityManager->flush();
            return  $this->redirectToRoute("quiz-readAllByTheme", ['id' => $theme->getId()]);
        }
        return $this->render("quiz/create.html.twig", [
            "form" => $form->createView(),
            'theme_id' => $theme,
        ]);
    }



    #[Route("/quiz/update/{id}", name: "quiz-update")]
    public function edit(Request $request, ManagerRegistry $doctrine, Quiz $quiz): Response
    {
        /*         $this->denyAccessUnlessGranted("ROLE_USER");
        $this->denyAccessUnlessGranted("ROLE_ADMIN"); */
        $form = $this->createForm(QuizType::class, $quiz);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();
            return  $this->redirectToRoute("quiz-readAllByTheme", ['id' => $quiz->getId()]);
        }
        return $this->render("quiz/update.html.twig", [
            "form" => $form->createView()
        ]);
    }


    #[Route("/quiz/delete/{id}", name: "quiz-delete")]
    public function delete(ManagerRegistry $doctrine, Quiz $quiz,): Response
    {
        /*         $this->denyAccessUnlessGranted("ROLE_USER");
        $this->denyAccessUnlessGranted("ROLE_ADMIN"); */
        if ($quiz->getUser() != $this->getUser()) {  // $post->getUser() or another method that returns the user of your post
            return $this->json(['code' => 403, 'error' => 'c est votre post  !'], 403);
        }
        $entityManager = $doctrine->getManager();
        $entityManager->remove($quiz);
        $entityManager->flush();
        return $this->redirectToRoute("readAll");
    }
}
