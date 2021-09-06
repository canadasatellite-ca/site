<?php

namespace CanadaSatellite\AstIntegration\LogicProcessors;

class AstQueueProcessor {
    /** @var AstQueueItem[] */
    private $queue;

    /** @var \CanadaSatellite\AstIntegration\AstManagement\AstManager */
    private $astManager;

    /** @var \CanadaSatellite\AstIntegration\Logger\Logger */
    private $logger;

    /** @var \CanadaSatellite\DynamicsIntegration\Rest\RestApi */
    private $dynamicsApi;

    public function __construct() {
        $this->queue = [];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->astManager = $objectManager->get('CanadaSatellite\AstIntegration\AstManagement\AstManager');
        $this->dynamicsApi = $objectManager->get('CanadaSatellite\DynamicsIntegration\Rest\RestApi');
        $this->logger = $objectManager->get('CanadaSatellite\AstIntegration\Logger\Logger');
    }

    /**
     * @param AstQueueItem $item
     */
    public function push($item) {
        $item->nextTime = time() + 60;
        array_push($this->queue, $item);
        $this->logger->info("[AstQueueProcessor] Pushed: " . var_export($item, true));
    }

    public function consume() {
        $limit = 0;
        $i = 0;
        while ($i < count($this->queue)) {
            $item = $this->queue[$i++];
            $this->logger->info("[AstQueueProcessor] DataId = $item->dataId | Begin");
            if ($item->nextTime > time()) {
                $this->logger->info("[AstQueueProcessor] DataId = $item->dataId | Wait");
                continue;
            }

            if(++$limit >= 10) {
                $this->logger->info("[AstQueueProcessor] Request limit reached");
                break;
            }

            $this->logger->info("[AstQueueProcessor] DataId = $item->dataId | Process");

            $resp = $this->astManager->getActionStatus($item->dataId);
            if ($resp->Error_Code !== 0 || $resp->Return_Objects[0]->Status === 'Failed') {
                $i--;
                array_splice($this->queue, $i, 1);
                $this->logger->info("[AstQueueProcessor] AST activation error: $resp->Message. SIM = $item->simNumber. DataId = $item->dataId");
            }

            $retObj = $resp->Return_Objects[0];
            switch ($retObj->Status) {
                case 'Waiting':
                case 'Queued':
                    $item->nextTime = time() + rand(10, 20);
                    break;
                case 'Succeeded':
                    $i--;
                    array_splice($this->queue, $i, 1);

                    $item->finalize($retObj->MSISDN, $this->astManager, $this->dynamicsApi);
                    break;
                default:
                    $this->logger->info("[AstQueueProcessor] AST API returned unknown status $retObj->Status. SIM = $item->simNumber. DataId = $item->dataId");
                    break;
            }
        }
    }
}