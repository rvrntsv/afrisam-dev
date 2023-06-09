<?php

declare(strict_types=1);

namespace UI\Http;

use App\User\Domain\Exception\InvalidCredentialsException;
use App\User\Infrastructure\Auth\Auth;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class Session
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function get(): Auth
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new InvalidCredentialsException();
        }

        $user = $token->getUser();

        if (!$user instanceof Auth) {
            throw new InvalidCredentialsException();
        }

        return $user;
    }
}
