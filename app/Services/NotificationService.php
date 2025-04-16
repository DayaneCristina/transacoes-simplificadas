<?php

namespace App\Services;

use App\Exceptions\Business\NotificationFailedException;
use App\Repositories\External\NotifierRepository;
use Illuminate\Http\Client\ConnectionException;

class NotificationService
{
    private NotifierRepository $repository;

    public function __construct(
        NotifierRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Send a notification to the external notifier service.
     *
     * @return void
     *
     * @throws NotificationFailedException When the notification fails to be sent.
     * @throws ConnectionException When there's a connection issue with the notifier service.
     */
    public function sendNotification(): void
    {
        $this->repository->notify();
    }
}
