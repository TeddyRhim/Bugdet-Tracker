<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') ;
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');
        if (null === $authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        $apiToken = substr($authHeader, 7);
        return new SelfValidatingPassport(new UserBadge($apiToken, function($token) {
            $user = $this->em->getRepository(User::class)->findOneBy(['apiToken' => $token]);

            if (!$user) {
                throw new CustomUserMessageAuthenticationException('Invalid API token.');
            }

            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, string $firewallName): ?Response
    {
        return null; // continue la requête
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }
}
