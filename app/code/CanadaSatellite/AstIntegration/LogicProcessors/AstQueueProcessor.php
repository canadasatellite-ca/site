<?php

namespace CanadaSatellite\AstIntegration\LogicProcessors;

class AstQueueProcessor {
    /** @var \CanadaSatellite\AstIntegration\AstManagement\AstManager */
    private $astManager;

    /** @var \CanadaSatellite\AstIntegration\Logger\Logger */
    private $logger;

    /** @var \CanadaSatellite\DynamicsIntegration\Rest\RestApi */
    private $dynamicsApi;

    public function __construct(
        \CanadaSatellite\AstIntegration\AstManagement\AstManager $astManager,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi        $dynamicsApi,
        \CanadaSatellite\AstIntegration\Logger\Logger            $logger
    ) {
        $this->astManager = $astManager;
        $this->dynamicsApi = $dynamicsApi;
        $this->logger = $logger;
    }

    /**
     * @param \CanadaSatellite\SimpleAmqp\Api\AmqpClient $client
     * @param object $message
     * @return object|null
     */
    private function processQueueMessage($client, $message) {
        $item = json_decode($message->body);

        $this->logger->info("[AstQueueProcessor] DataId = $item->dataId | Begin");
        if ($item->nextTime > time()) {
            $this->logger->info("[AstQueueProcessor] DataId = $item->dataId | Wait");
            return $item;
        }

        $this->logger->info("[AstQueueProcessor] DataId = $item->dataId | Process");

        $resp = $this->astManager->getActionStatus($item->dataId);
        if ($resp->Error_Code !== 0 || $resp->Return_Objects[0]->Status === 'Failed') {
            $client->ack($message);
            $this->logger->info("[AstQueueProcessor] AST activation error: $resp->Message. SIM = $item->simNumber. DataId = $item->dataId");
            return null;
        }

        $retObj = $resp->Return_Objects[0];
        switch ($retObj->Status) {
            case 'Waiting':
            case 'Queued':
                $item->nextTime = time() + rand(10, 20);
                return $item;
            case 'Succeeded':
                $client->ack($message);
                try {
                    $item->finalize($retObj->MSISDN, $this->astManager, $this->dynamicsApi);
                } catch (\Exception $e) {
                    $this->logger->info('[AstQueueProcessor] Failed to finalize. Item: ' . $message->body);
                }
                return null;
            default:
                $this->logger->info("[AstQueueProcessor] AST API returned unknown status $retObj->Status. SIM = $item->simNumber. DataId = $item->dataId");
                return $item;
        }
    }

    /**
     * @param \CanadaSatellite\SimpleAmqp\Api\AmqpClient $client
     */
    public function consume($client) {
        $addItems = [];
        $addMessages = [];

        do {
            $message = $client->get();
            if ($message === null) {
                break;
            }

            try {
                $item = $this->processQueueMessage($client, $message);
                if ($item !== null) {
                    array_push($addItems, $item);
                    array_push($addMessages, $message);
                }
            } catch (\Exception $e) {
                $this->logger->info('[AstQueueProcessor] Failed to process message: ' . $e->getMessage());
                $this->logger->info('[AstQueueProcessor] Body: ' . $message->body);
                $this->logger->info('[AstQueueProcessor] Stack trace: ' . $e->getTraceAsString());
            }
        } while (true);

        for ($i = 0; $i < count($addItems); $i++) {
            $client->publish($addItems[$i]);
            $client->ack($addMessages[$i]);
        }
    }
}