<?php

namespace App\Controller;

use App\Repository\SubscriptionTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(SubscriptionTypeRepository $subscriptionTypeRepository): Response
    {
        return $this->json(
            ['message' => 'WELCOME to our subscription service', 'plans' => $subscriptionTypeRepository->findAll()],
            Response::HTTP_OK,
            [],
            [AbstractNormalizer::ATTRIBUTES => ['price', 'name', 'period', 'id', 'contents' => ['name']]]
        );
    }
}
