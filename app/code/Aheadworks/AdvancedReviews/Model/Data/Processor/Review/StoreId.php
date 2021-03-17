<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class StoreId
 *
 * @package Aheadworks\AdvancedReviews\Model\Data\Processor\Review
 */
class StoreId implements ProcessorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (!isset($data[ReviewInterface::STORE_ID])) {
            $data[ReviewInterface::STORE_ID] = $this->getCurrentStoreId();
        }
        return $data;
    }

    /**
     * Retrieve current store id
     *
     * @return int
     * @throws NoSuchEntityException
     */
    private function getCurrentStoreId()
    {
        return $currentStoreId = $this->storeManager->getStore(true)->getId();
    }
}
