<?php

namespace App\Logging;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;

class CloudWatchLoggerFactory
{
    public function __invoke(array $config)
    {
        $client = new CloudWatchLogsClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $group = env('CLOUDWATCH_LOG_GROUP');
        $stream = env('CLOUDWATCH_LOG_STREAM');

        return new Logger('cloudwatch', [
            new \App\Logging\SimpleCloudWatchHandler($client, $group, $stream),
        ]);
    }
}
