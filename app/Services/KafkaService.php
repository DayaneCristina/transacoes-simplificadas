<?php

namespace App\Services;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Enqueue\RdKafka\RdKafkaConsumer;
use Enqueue\RdKafka\RdKafkaTopic;
use Interop\Queue\ConnectionFactory;
use Interop\Queue\Consumer;
use Interop\Queue\Context;
use Interop\Queue\Exception;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
use Interop\Queue\Producer;
use Interop\Queue\Topic;

class KafkaService
{
    private ConnectionFactory $connectionFactory;
    private Context $context;
    private Producer $producer;

    public function __construct()
    {
        $config = config('enqueue.connections.rdkafka');
        $this->connectionFactory = new RdKafkaConnectionFactory($config);
        $this->context = $this->connectionFactory->createContext();
        $this->producer = $this->context->createProducer();
    }

    /**
     * Produce a message to a Kafka topic.
     *
     * @param string $topic The Kafka topic to produce to.
     * @param array  $body  The message body to send.
     *
     * @return void
     *
     * @throws InvalidDestinationException When the destination topic is invalid.
     * @throws InvalidMessageException When the message format is invalid.
     * @throws Exception For general queue-related exceptions.
     */
    public function produce(string $topic, array $body): void
    {
        $message = $this->context->createMessage(json_encode($body));
        $this->producer->send($this->createTopic($topic), $message);
    }

    public function getKafkaConsumer(string $topic): Consumer|RdKafkaConsumer
    {
        return $this->context->createConsumer($this->createTopic($topic));
    }

    private function createTopic(string $topic): RdKafkaTopic|Topic
    {
        return $this->context->createTopic($topic);
    }
}
