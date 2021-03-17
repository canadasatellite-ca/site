<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Review\Listing\Column\SubmittedFrom;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;
use Magento\Store\Model\Store as StoreModel;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Escaper;
use Magento\Store\Model\System\Store as SystemStore;

/**
 * Class Options
 * @package Aheadworks\AdvancedReviews\Ui\Component\Review\Listing\Column
 */
class Options extends StoreOptions
{
    /**
     * @var bool
     */
    private $isNeedToAddAdminStore = true;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param SystemStore $systemStore
     * @param Escaper $escaper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        SystemStore $systemStore,
        Escaper $escaper,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($systemStore, $escaper);
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        parent::toOptionArray();

        if ($this->isNeedToAddAdminStore) {
            $this->addAdminStoreToOptions();
        }

        return $this->options;
    }

    /**
     * Add admin store name to options select
     */
    private function addAdminStoreToOptions()
    {
        $adminStoreName = $this->storeManager->getStore(StoreModel::DEFAULT_STORE_ID)->getName();

        array_unshift(
            $this->options,
            [
                'label' => $adminStoreName,
                'value' => StoreModel::DEFAULT_STORE_ID
            ]
        );
        $this->isNeedToAddAdminStore = false;
    }
}
