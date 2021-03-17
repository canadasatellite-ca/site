<?php

namespace Magento\Amazon\Model\Cleanup;

class Logs
{
    /**
     * @var int
     */
    private $minutes;
    /**
     * @var \Magento\Amazon\Model\ResourceModel\LogProcessing
     */
    private $logProcessing;

    public function __construct(\Magento\Amazon\Model\ResourceModel\LogProcessing $logProcessing, int $minutes)
    {
        $this->logProcessing = $logProcessing;
        $this->minutes = $minutes;
    }

    public function totalCount(): int
    {
        return $this->logProcessing->countLogsOlderThan($this->minutes);
    }

    public function delete(): void
    {
        $this->logProcessing->deleteLogsOlderThan($this->minutes);
    }
}
