<?php

namespace App\Controller;

use App\Security\JwtAuthenticator;
use DateTime;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/auth/login', name:'login', methods:['POST'])]
    public function login() {
        $payload = [
            "user" => $this->getUser()->getUsername(),
            "exp" => (new DateTime())->modify(JwtAuthenticator::DEFAULT_LIFETIME_JWT)->getTimestamp(),
        ];

        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');

        return $this->json([
            'token' => $jwt
        ], Response::HTTP_OK);
    }
}
