<?php

namespace App\Logging;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class SimpleCloudWatchHandler extends AbstractProcessingHandler
{
    protected $client;
    protected $logGroupName;
    protected $logStreamName;
    protected $sequenceToken;

    public function __construct(CloudWatchLogsClient $client, $logGroupName, $logStreamName, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->client = $client;
        $this->logGroupName = $logGroupName;
        $this->logStreamName = $logStreamName;

        $this->ensureLogStreamExists();
    }

    protected function ensureLogStreamExists()
    {
        try {
            $result = $this->client->describeLogStreams([
                'logGroupName' => $this->logGroupName,
                'logStreamNamePrefix' => $this->logStreamName,
            ]);

            if (!empty($result['logStreams'][0]['uploadSequenceToken'])) {
                $this->sequenceToken = $result['logStreams'][0]['uploadSequenceToken'];
            } else {
                $this->sequenceToken = null;
            }
        } catch (\Exception $e) {
            // Stream doesn't exist. Create it.
            $this->client->createLogStream([
                'logGroupName' => $this->logGroupName,
                'logStreamName' => $this->logStreamName,
            ]);
        }
    }

    protected function write(array $record): void
    {
        $data = [
            'logGroupName' => $this->logGroupName,
            'logStreamName' => $this->logStreamName,
            'logEvents' => [
                [
                    'timestamp' => round(microtime(true) * 1000),
                    'message' => $record['formatted'],
                ],
            ],
        ];

        if ($this->sequenceToken) {
            $data['sequenceToken'] = $this->sequenceToken;
        }

        $result = $this->client->putLogEvents($data);

        $this->sequenceToken = $result['nextSequenceToken'];
    }
}
