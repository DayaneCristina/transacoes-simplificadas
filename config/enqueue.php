<?php

return [
    'default' => env('ENQUEUE_CONNECTION', 'rdkafka'),

    'connections' => [
        'rdkafka' => [
            'driver' => 'rdkafka',
            'global' => [
                'group.id' => env('KAFKA_GROUP_ID', 'laravel_group'),
                'metadata.broker.list' => env('KAFKA_BROKERS', 'kafka:9092'),
                'enable.auto.commit' => 'true',
            ],
            'topic' => [
                'auto.offset.reset' => 'earliest',
            ],
        ],
    ],
];
