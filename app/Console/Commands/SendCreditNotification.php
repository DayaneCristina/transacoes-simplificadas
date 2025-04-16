<?php

namespace App\Console\Commands;

use App\Services\KafkaService;
use App\Services\NotificationService;
use Exception;
use Illuminate\Console\Command;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
use Interop\Queue\Message;
use Illuminate\Support\Facades\Cache;

class SendCreditNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listener:send-credit-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to notification topic';

    private string $queue = 'account_notify_transaction';
    private string $dlt = 'account_notify_transaction_dlt';
    private KafkaService $kafkaService;
    private NotificationService $notificationService;

    public function __construct(
        KafkaService $kafkaService,
        NotificationService $notificationService
    ) {
        $this->kafkaService = $kafkaService;
        $this->notificationService = $notificationService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $consumer = $this->kafkaService->getKafkaConsumer($this->queue);

        while (true) {
            $message = $consumer->receive();

            if ($message) {
                $this->processMessage($message);
                $consumer->acknowledge($message);
            }
        }
    }

    /**
     * Process messages.
     *
     * @param Message $message The Kafka message to be processed.
     *
     * @return void
     * @throws InvalidDestinationException When the destination is invalid.
     * @throws InvalidMessageException When the message is invalid.
     * @throws \Interop\Queue\Exception For general queue-related exceptions.
     */
    private function processMessage(Message $message): void
    {
        try {
            $body = json_decode($message->getBody(), true);

            $lock = Cache::lock('message_' . $body['correlation_id'] . '_processing', 30);

            if ($lock->get()) {

                $this->notificationService->sendNotification();

                $lock->release();
            }
        } catch (Exception $e) {
            $this->kafkaService->produce(
                $this->dlt,
                [
                    'event'          => 'NOTIFICATION_ERROR',
                    'error'          => $e->getMessage(),
                    'trace'          => $e->getTrace(),
                    'transaction_id' => $body['transaction_id'],
                    'correlation_id' => $body['correlation_id'],
                ]
            );
        }
    }
}
