<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model;

use Magento\Amazon\Model\ResourceModel\LogProcessing;

/**
 * Class LogStateManagement
 */
class LogStateManagement
{

    /**
     * @var LogProcessing
     */
    private $logProcessing;

    public function __construct(LogProcessing $logProcessing)
    {
        $this->logProcessing = $logProcessing;
    }

    /**
     * @param array $logIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processing(array $logIds)
    {
        $this->logProcessing->addIds($logIds);
    }

    /**
     * Filters received log ids and return those which aren't processed yet
     *
     * @param array $logIds
     * @return int[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function filterProcessableLogs(array $logIds): array
    {
        $ids = array_combine(array_values($logIds), $logIds);
        $storedIds = $this->logProcessing->findByIds($logIds);
        return array_diff($ids, $storedIds);
    }

    /**
     * @param array $logIds
     * @throws \Exception
     */
    public function complete(array $logIds)
    {
        // @todo: it's a good place to make API call to remove processed logs
        $this->logProcessing->deleteByIds($logIds);
    }

    /**
     * @param \DateTimeInterface $before
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cleanLogs(\DateTimeInterface $before)
    {
        $this->logProcessing->deleteBeforeDate($before);
    }
}
