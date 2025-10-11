<?php

namespace App\Controller;

use App\Entity\Fruit;
use App\Form\FruitType;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FruitController extends AbstractController
{
    #[Route('/fruits', name: 'app_fruits')]
    public function index(FruitRepository $fruitRepository): Response
    {
        return $this->render('fruit/index.html.twig', [
            'fruits' => $fruitRepository->findAll(),
        ]);
    }


    #[Route('/fruit/create', name: 'app_fruit_create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $fruit = new Fruit();
        $form = $this->createForm(FruitType::class, $fruit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($fruit);
            $manager->flush();
            return $this->redirectToRoute('app_fruit_show', ['id' => $fruit->getId()]);
        }
        return $this->render('fruit/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/fruit/show/{id}', name: 'app_fruit_show')]
    public function show(Fruit $fruit): Response
    {
        return $this->render('fruit/show.html.twig', [
            'fruit' => $fruit,
        ]);
    }



    #[Route('/fruit/{id}/edit', name: 'app_fruit_edit')]
    public function edit(Fruit $fruit, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(FruitType::class, $fruit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($fruit);
            $manager->flush();
            return $this->redirectToRoute('app_fruit_show', ['id' => $fruit->getId()]);
        }
        return $this->render('fruit/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/fruit/{id}/delete', name: 'app_fruit_delete')]
    public function delete(Fruit $fruit, EntityManagerInterface $manager): Response
    {
        $manager->remove($fruit);
        $manager->flush();
        return $this->redirectToRoute('app_fruits');
    }



































}
