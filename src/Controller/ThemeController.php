<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Form\ThemeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    #[Route("/", name: "index")]
    public function index(ManagerRegistry $doctrine)
    {
        $repository = $doctrine->getRepository(Theme::class);
        $themes = $repository->findAll();
        return $this->render("theme/index.html.twig", [
            "themes" => $themes
        ]);
    }

    #[Route("/theme/create", name: "create")]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        //$this->denyAccessUnlessGranted("ROLE_ADMIN");
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($theme);
            $entityManager = $doctrine->getManager();

            $entityManager->persist($theme);
            $entityManager->flush();
        }
        return  $this->redirectToRoute("index", ['id' => $theme->getId()]);
        return $this->render("theme/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/theme/read/{id}", name: "theme-read")]
    public function read(ManagerRegistry $doctrine, int $id): Response
    {
        $repository = $doctrine->getRepository(ThemeType::class);
        $theme = $repository->find($id);
        return $this->render("theme/read.html.twig", [
            "theme" => $theme
        ]);
    }

}
