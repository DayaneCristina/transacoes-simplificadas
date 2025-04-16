<?php

namespace App\Services;

use App\Exceptions\Business\ExternalAuthorizationFailedException;
use App\Exceptions\Business\ExternalAuthorizationNotAllowedException;
use App\Repositories\External\AuthorizerRepository;
use Illuminate\Http\Client\ConnectionException;

class AuthorizerService
{
    private AuthorizerRepository $repository;

    public function __construct(AuthorizerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Authorize the transaction with external service.
     *
     * @return boolean True if authorization is successful, false otherwise.
     *
     * @throws ExternalAuthorizationFailedException When authorization request fails.
     * @throws ExternalAuthorizationNotAllowedException When authorization is not allowed.
     * @throws ConnectionException When connection to the authorizer service fails.
     */
    public function authorize(): bool
    {
        return $this->repository->authorize();
    }
}
