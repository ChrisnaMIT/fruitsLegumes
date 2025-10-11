<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;



final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET','POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, SerializerInterface $serializer, UserRepository $userRepository): Response {
        // Détecte si c'est un appel JSON (Postman / SPA)
        $contentType = $request->headers->get('Content-Type', '');
        $isJson = str_contains($contentType, 'application/json') || $request->getContentTypeFormat() === 'json';

        // ---------- BRANCHE API JSON ----------
        if ($isJson) {
            if (!$request->isMethod('POST')) {
                return new JsonResponse(['error' => 'Method not allowed'], 405);
            }

            $raw = trim((string) $request->getContent());
            if ($raw === '') {
                return $this->json(['error' => 'Empty body'], 400);
            }

            try {
                // TA PARTIE gardée (avec garde-fous)
                $user = $serializer->deserialize($raw, User::class, 'json');
            } catch (NotEncodableValueException $e) {
                return $this->json(['error' => 'Invalid JSON'], 400);
            }

            // Vérif email déjà pris
            $emailTaken = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if ($emailTaken) {
                return $this->json(['message' => 'Email deja pris en compte'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Hash du mot de passe envoyé dans le JSON (champ "password")
            $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json($user, Response::HTTP_CREATED, [], ['groups' => ['Registration']]);
        }

        // ---------- BRANCHE FORMULAIRE WEB ----------
        // GET: affiche le form ; POST: traite le form
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérif email déjà pris
            if ($userRepository->findOneBy(['email' => $user->getEmail()])) {
                $this->addFlash('error', 'Email déjà pris.');
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            // `plainPassword` vient du formulaire (champ unmapped)
            $hashed = $userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($hashed);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }











#[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
