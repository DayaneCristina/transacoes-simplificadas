<?php

namespace App\Repositories\External;

use App\Exceptions\Business\NotificationFailedException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class NotifierRepository
{
    private PendingRequest $request;

    public function __construct()
    {
        $this->request = Http::baseUrl(config('services.notifier.base_uri'));
    }

    /**
     * Send notification to external service.
     *
     * @return void
     *
     * @throws ConnectionException When connection to the notifier service fails.
     * @throws NotificationFailedException When notification request fails.
     */
    public function notify(): void
    {
        $response = $this->request->post('notify');

        if ($response->failed()) {
            throw new NotificationFailedException();
        }

        if (in_array($response->status(), [Response::HTTP_OK, Response::HTTP_CREATED, Response::HTTP_ACCEPTED])) {
            return;
        }

        $body = json_decode($response->body());

        if ($body && $body->status === 'error') {
            throw new NotificationFailedException();
        }
    }
}
