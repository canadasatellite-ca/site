<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Common
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier
 */
class Common extends AbstractModifier
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
    public function modifyData(array $data)
    {
        if ($this->isSetId($data)) {
            $data['newReview'] = false;
        } else {
            $data['newReview'] = true;
            $data[ReviewInterface::SHARED_STORE_IDS] = [$this->storeManager->getDefaultStoreView()->getId()];
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
