<?php

namespace App\Repositories\External;

use App\Exceptions\Business\ExternalAuthorizationFailedException;
use App\Exceptions\Business\ExternalAuthorizationNotAllowedException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AuthorizerRepository
{
    private PendingRequest $request;

    public function __construct()
    {
        $this->request = Http::baseUrl(config('services.authorizer.base_uri'));
    }

    /**
     * Authorize the transaction with external service.
     *
     * @return boolean True if authorization is successful, false otherwise.
     *
     * @throws ConnectionException When connection to the authorizer service fails.
     * @throws ExternalAuthorizationFailedException When authorization request fails.
     * @throws ExternalAuthorizationNotAllowedException When authorization is not allowed.
     */
    public function authorize(): bool
    {
        $response = $this->request->get('authorize');

        if ($response->failed()) {
            throw new ExternalAuthorizationFailedException();
        }

        $body = json_decode($response->body());

        if ($body->status === 'fail') {
            throw new ExternalAuthorizationFailedException();
        }

        if (!$body->data->authorization) {
            throw new ExternalAuthorizationNotAllowedException();
        }

        return $body->data->authorization;
    }
}
