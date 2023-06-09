<?php

declare(strict_types=1);

namespace App\User\Application\Query\Auth\GetToken;

use App\Shared\Application\Query\QueryHandlerInterface;
use App\User\Infrastructure\Auth\AuthenticationProvider;
use App\User\Domain\Repository\GetUserCredentialsByEmailInterface;

final class GetTokenHandler implements QueryHandlerInterface
{
    public function __construct(private readonly GetUserCredentialsByEmailInterface $userCredentialsByEmail, private readonly AuthenticationProvider $authenticationProvider)
    {
    }

    public function __invoke(GetTokenQuery $query): string
    {
        [$uuid, $email, $hashedPassword] = $this->userCredentialsByEmail->getCredentialsByEmail($query->email);

        return $this->authenticationProvider->generateToken($uuid, $email, $hashedPassword);
    }
}
