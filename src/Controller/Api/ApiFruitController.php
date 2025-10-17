<?php

namespace App\Controller\Api;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/fruits', name: 'api_fruits_')]
final class ApiFruitController extends AbstractController
{

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(FruitRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $fruits = $repo->findAll();
        $json = $serializer->serialize($fruits, 'json', ['groups' => 'fruit:read']);
        return new JsonResponse($json, 200, [], true);
    }


    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Fruit $fruit, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($fruit, 'json', ['groups' => 'fruit:read']);
        return new JsonResponse($json, 200, [], true);
    }


    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $json = $request->getContent();

        try {
            $fruit = $serializer->deserialize($json, Fruit::class, 'json');
            $em->persist($fruit);
            $em->flush();
            return new JsonResponse(['message' => 'Fruit créé avec succès'], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'JSON invalide: ' . $e->getMessage()], 400);
        }
    }


    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Fruit $fruit, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $fruit->setName($data['name']);
        if (isset($data['description'])) $fruit->setDescription($data['description']);

        $em->flush();
        return new JsonResponse(['message' => 'Fruit mis à jour avec succès']);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Fruit $fruit, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($fruit);
        $em->flush();
        return new JsonResponse(['message' => 'Fruit supprimé avec succès']);
    }
}
