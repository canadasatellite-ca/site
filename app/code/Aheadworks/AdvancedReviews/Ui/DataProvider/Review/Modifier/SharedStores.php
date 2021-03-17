<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class SharedStores
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier
 */
class SharedStores extends AbstractModifier
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->isSetId($data)) {
            $this->prepareSharedStores($data);
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

    /**
     * Prepare shared stores
     *
     * @param array $data
     * @return array
     */
    private function prepareSharedStores(&$data)
    {
        if (!isset($data[ReviewInterface::SHARED_STORE_IDS]) || !is_array($data[ReviewInterface::SHARED_STORE_IDS])) {
            $data[ReviewInterface::SHARED_STORE_IDS] = [];
        }

        array_push(
            $data[ReviewInterface::SHARED_STORE_IDS],
            $data[ReviewInterface::STORE_ID]
        );
        return $data;
    }
}
