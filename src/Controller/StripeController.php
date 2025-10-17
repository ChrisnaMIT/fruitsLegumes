<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class StripeController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $session = Session::create([
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Abonnement Premium - Fruits & Légumes',
                    ],
                    'unit_amount' => 499,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'https://localhost:4444/success',
            'cancel_url' => 'https://localhost:4444/premium',
        ]);

        return $this->json(['url' => $session->url]);
    }

    #[Route('/success', name: 'app_success')]
    public function success(EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        if ($user) {
            $user->setIsPremium(true);
            $manager->persist($user);
            $manager->flush();
        }

        $this->addFlash('success', '🎉 Félicitations ! Vous êtes maintenant membre Premium !');
        return $this->redirectToRoute('app_legumes');
    }
}

