<?php

namespace App\Security;

use App\Repository\UserRepository;
use Exception;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtAuthenticator extends AbstractGuardAuthenticator
{
    public const DEFAULT_LIFETIME_JWT = '+50 minutes';
    private UserRepository $users;
    private ContainerBagInterface $params;

    public function __construct(UserRepository $userRepository, ContainerBagInterface $params) {
        $this->users = $userRepository;
        $this->params = $params;
    }

    public function start(Request $request, AuthenticationException $authException = null) {
        $data = [
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request) {
        return $request->headers->has('Authorization');
    }

    public function getCredentials(Request $request) {
        return $request->headers->get('Authorization');
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        try {
            $jwt = (array) JWT::decode(
                $credentials,
                $this->params->get('jwt_secret'),
                ['HS256']
            );

            return $this->users->findOneBy([
                'email' => $jwt['user'],
            ]);
        } catch (Exception $exception) {
            throw new AuthenticationException($exception->getMessage());
        }
    }

    public function checkCredentials($credentials, UserInterface $user) {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        return new JsonResponse([
            'message' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey) {

    }

    public function supportsRememberMe() {
        return false;
    }
}
