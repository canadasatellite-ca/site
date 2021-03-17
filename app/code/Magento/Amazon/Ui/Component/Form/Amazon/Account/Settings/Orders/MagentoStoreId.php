<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Orders;

use Magento\Amazon\Api\ListingRuleRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class MagentoStoreId
 */
class MagentoStoreId implements OptionSourceInterface
{
    /** @var StoreRepositoryInterface $storeRepository */
    protected $storeRepository;

    /** @var ListingRuleRepositoryInterface */
    private $listingRuleRepository;

    /** @var Http */
    private $request;

    /**
     * @param StoreRepositoryInterface $storeRepository
     * @param ListingRuleRepositoryInterface $listingRuleRepository
     * @param Http $request
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        ListingRuleRepositoryInterface $listingRuleRepository,
        Http $request
    ) {
        $this->storeRepository = $storeRepository;
        $this->listingRuleRepository = $listingRuleRepository;
        $this->request = $request;
    }

    /**
     * Returns option array
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function toOptionArray()
    {
        /** @var StoreInterface[] */
        $stores = $this->storeRepository->getList();
        $data = [];
        $merchantId = $this->request->getParam('merchant_id');

        $listingRule = $this->listingRuleRepository->getByMerchantId($merchantId);

        foreach ($stores as $store) {
            if ($store->getWebsiteId() == $listingRule->getWebsiteId()) {
                $data[] = [
                    'value' => $store->getId(),
                    'label' => __($store->getName())
                ];
            }
        }

        return $data;
    }
}
