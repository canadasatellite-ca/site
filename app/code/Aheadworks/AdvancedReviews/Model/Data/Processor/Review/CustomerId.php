<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class CustomerId
 *
 * @package Aheadworks\AdvancedReviews\Model\Data\Processor\Review
 */
class CustomerId implements ProcessorInterface
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param CustomerSession $customerSession
     */
    public function __construct(
        CustomerSession $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (!isset($data[ReviewInterface::CUSTOMER_ID])) {
            $data[ReviewInterface::CUSTOMER_ID] = $this->getCurrentCustomerId();
        }
        return $data;
    }

    /**
     * Retrieve current customer id
     *
     * @return int|null
     */
    private function getCurrentCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }
}
