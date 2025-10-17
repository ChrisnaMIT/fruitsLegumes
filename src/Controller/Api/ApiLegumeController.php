<?php

namespace App\Controller\Api;

use App\Entity\Legume;
use App\Repository\LegumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/legumes', name: 'api_legumes_')]
final class ApiLegumeController extends AbstractController
{

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(LegumeRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $legumes = $repo->findAll();
        $json = $serializer->serialize($legumes, 'json', ['groups' => 'legume:read']);
        return new JsonResponse($json, 200, [], true);
    }


    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Legume $legume, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($legume, 'json', ['groups' => 'legume:read']);
        return new JsonResponse($json, 200, [], true);
    }


    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        try {
            $legume = $serializer->deserialize($request->getContent(), Legume::class, 'json');
            $em->persist($legume);
            $em->flush();
            return new JsonResponse(['message' => 'Légume créé avec succès'], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'JSON invalide : ' . $e->getMessage()], 400);
        }
    }


    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Legume $legume, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $legume->setName($data['name']);
        if (isset($data['description'])) $legume->setDescription($data['description']);

        $em->flush();
        return new JsonResponse(['message' => 'Légume mis à jour avec succès']);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Legume $legume, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($legume);
        $em->flush();
        return new JsonResponse(['message' => 'Légume supprimé avec succès']);
    }
}
