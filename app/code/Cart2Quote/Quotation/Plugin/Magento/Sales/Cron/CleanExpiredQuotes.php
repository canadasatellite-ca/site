<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Sales\Cron;

use Magento\Framework\App\ObjectManager;

/**
 * Class CleanExpiredQuotes
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Sales\Cron
 */
class CleanExpiredQuotes
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Collection\ExpiredQuotesCollection
     */
    private $expiredQuotesCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * CleanExpiredQuotes constructor
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;

        //expiredQuotesCollection is added in Magento 2.3.4
        if (class_exists(\Magento\Sales\Model\ResourceModel\Collection\ExpiredQuotesCollection::class)) {
            $expiredQuotesCollection = ObjectManager::getInstance()->get(
                \Magento\Sales\Model\ResourceModel\Collection\ExpiredQuotesCollection::class
            );
            $this->expiredQuotesCollection = $expiredQuotesCollection;
        }
    }

    /**
     * Add additional filters to the clean expired quote cron
     *
     * @param \Magento\Sales\Cron\CleanExpiredQuotes $subject
     */
    public function beforeExecute(\Magento\Sales\Cron\CleanExpiredQuotes $subject)
    {
        $additionalFilter = [
            'linked_quotation_id' => ['null' => true],
            'is_quotation_quote' => ['eq' => 0]
        ];

        //pre Magento 2.3.4
        if (method_exists($subject, 'setExpireQuotesAdditionalFilterFields')) {
            $subject->setExpireQuotesAdditionalFilterFields($additionalFilter);
        }
    }

    /**
     * Clean expired quotes (cron process)
     *
     * @param \Magento\Sales\Cron\CleanExpiredQuotes $subject
     * @param callable $proceed
     */
    public function aroundExecute(
        \Magento\Sales\Cron\CleanExpiredQuotes $subject,
        callable $proceed
    ) {
        if (method_exists($subject, 'setExpireQuotesAdditionalFilterFields')) {
            //pre Magento 2.3.4
            $proceed();
        } else {
            //Magento 2.3.4 support
            $additionalFilter = [
                'linked_quotation_id' => ['null' => true],
                'is_quotation_quote' => ['eq' => 0]
            ];

            $stores = $this->storeManager->getStores(true);
            foreach ($stores as $store) {
                /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection $quotes */
                $quotes = $this->expiredQuotesCollection->getExpiredQuotes($store);

                //add extra fields
                foreach ($additionalFilter as $field => $condition) {
                    $quotes->addFieldToFilter($field, $condition);
                }

                $quotes->walk('delete');
            }
        }
    }
}
