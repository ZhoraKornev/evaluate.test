<?php

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginUserAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordValidator;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * LoginUserAuthenticator constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param LoggerInterface              $logger
     */
    public function __construct(UserPasswordEncoderInterface $encoder,LoggerInterface $logger) {
        $this->passwordValidator = $encoder;
        $this->logger = $logger;
    }
    public function supports(Request $request) {
        return 'login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request) {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password')
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        $user = $userProvider->loadUserByUsername($credentials['email']);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Username could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user) {
        return $this->passwordValidator->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $this->logger->warning('AUTH FAIL',
            ['code' => $exception->getCode(), 'message' => $exception->getMessage()]);
        return new JsonResponse([
            'message' => 'Incorrect data'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey) {
    }

    public function start(Request $request, AuthenticationException $authException = null) {
        return new JsonResponse([
            'message' => 'Authentication Required'
        ]);
    }

    public function supportsRememberMe() {
        return false;
    }
}
