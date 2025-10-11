<?php

namespace App\Controller;

use App\Entity\Legume;
use App\Form\LegumeType;
use App\Repository\LegumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegumeController extends AbstractController
{
    #[Route('/legumes', name: 'app_legumes')]
    public function index(LegumeRepository $legumeRepository): Response
    {
        return $this->render('legume/index.html.twig', [
            'legumes' => $legumeRepository->findAll(),
        ]);
    }

    #[Route('/legume/create', name: 'app_legume_create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $legume = new Legume();
        $form = $this->createForm(LegumeType::class, $legume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($legume);
            $manager->flush();

            return $this->redirectToRoute('app_legume_show', ['id' => $legume->getId()]);
        }

        return $this->render('legume/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/legume/show/{id}', name: 'app_legume_show')]
    public function show(Legume $legume): Response
    {
        return $this->render('legume/show.html.twig', [
            'legume' => $legume,
        ]);
    }

    #[Route('/legume/{id}/edit', name: 'app_legume_edit')]
    public function edit(Legume $legume, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(LegumeType::class, $legume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($legume);
            $manager->flush();

            return $this->redirectToRoute('app_legume_show', ['id' => $legume->getId()]);
        }

        return $this->render('legume/edit.html.twig', [
            'form' => $form->createView(),
            'legume' => $legume,
        ]);
    }

    #[Route('/legume/{id}/delete', name: 'app_legume_delete')]
    public function delete(Legume $legume, EntityManagerInterface $manager): Response
    {
        $manager->remove($legume);
        $manager->flush();

        return $this->redirectToRoute('app_legumes');
    }
}

