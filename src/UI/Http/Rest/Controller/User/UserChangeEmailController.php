<?php

declare(strict_types=1);

namespace UI\Http\Rest\Controller\User;

use App\Shared\Application\Command\CommandBusInterface;
use App\User\Application\Command\ChangeEmail\ChangeEmailCommand;
use App\User\Domain\Exception\ForbiddenException;
use UI\Http\Rest\Controller\CommandController;
use UI\Http\Session;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

final class UserChangeEmailController extends CommandController
{
    public function __construct(private readonly Session $session, CommandBusInterface $commandBus)
    {
        parent::__construct($commandBus);
    }

    /**
     *
     * @OA\Response(
     *     response=201,
     *     description="Email changed"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad request"
     * )
     * @OA\Response(
     *     response=409,
     *     description="Conflict"
     * )
     *
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="email", type="string"),
     *     )
     * )
     *
     * @OA\Parameter(
     *     name="uuid",
     *     in="path",
     *     @OA\Schema(type="string")
     * )
     *
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     *
     * @throws AssertionFailedException
     * @throws Throwable
     */
    #[Route(path: '/users/{uuid}/email', name: 'user_change_email', methods: ['POST'])]
    public function __invoke(string $uuid, Request $request): JsonResponse
    {
        $this->validateUuid($uuid);

        $email = (string) $request->request->get('email');

        Assertion::notEmpty($email, "Email can\'t be empty");

        $command = new ChangeEmailCommand($uuid, $email);

        $this->handle($command);

        return new JsonResponse();
    }

    private function validateUuid(string $uuid): void
    {
        if (!$this->session->get()->uuid()->equals(Uuid::fromString($uuid))) {
            throw new ForbiddenException();
        }
    }
}
