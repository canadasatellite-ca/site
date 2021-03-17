<?php

namespace Magento\Amazon\Logger\Processor;

use Magento\Amazon\Model\ApiClient\ResponseValidationException;
use Monolog\Processor\ProcessorInterface;

class LogHttpResponseForApiExceptions implements ProcessorInterface
{
    /**
     * @var \Magento\Amazon\Logger\DebugLogging
     */
    private $debugLogging;

    public function __construct(\Magento\Amazon\Logger\DebugLogging $debugLogging)
    {
        $this->debugLogging = $debugLogging;
    }

    public function __invoke(array $records)
    {
        $exception = $records['context']['exception'] ?? null;
        if ($exception instanceof ResponseValidationException) {
            $response = $exception->getResponse();
            $responseData = [
                'headers' => $response->getHeaders(),
                'statusCode' => $response->getStatusCode(),
            ];
            if ($this->debugLogging->isEnabled()) {
                $responseData['body'] = base64_encode($response->getBody());
            }
            $records['extra']['response'] = $responseData;
        }
        return $records;
    }
}
