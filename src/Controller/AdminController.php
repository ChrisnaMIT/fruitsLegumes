<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository): Response
    {
       if(!$this->isGranted('ROLE_ADMIN')) {
           $this->denyAccessUnlessGranted('ROLE_ADMIN');
       }
       return $this->render('admin/index.html.twig', [
           'users' => $userRepository->findAll(),
       ]);
    }


    #[Route('/admin/promote/{id}', name: 'app_promote_admin')]
    public function promote(User $user, EntityManagerInterface $manager): Response
    {
         if(!$this->isGranted('ROLE_ADMIN')) {
             $this->addFlash('error', 'accès refusé');
             return $this->redirectToRoute('app_fruits');
         }

         if(!in_array('ROLE_ADMIN', $user->getRoles())) {
             $user->setRoles(['ROLE_ADMIN']);
             $manager->persist($user);
             $manager->flush();
         }
         return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/demote/{id}', name: 'app_demote_admin')]
    public function demote(User $user, EntityManagerInterface $manager): Response
    {
        if(!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'accès refusé');
            return $this->redirectToRoute('app_fruits');
        }

        if(!in_array('ROLE_ADMIN', $user->getRoles())) {
            $user->setRoles(['']);
            $manager->persist($user);
            $manager->flush();
        }
        return $this->redirectToRoute('app_admin');
    }






}
