<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel;

/**
 * Class EntityMetadata represents a list of entity fields that are applicable for persistence operations
 */
class EntityMetadata
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\EntityMetadata {
        getFields as private traitGetFields;
    }

    /**
     * @var array
     */
    protected $metadataInfo = [];

    /**
     * Returns list of entity fields that are applicable for persistence operations
     *
     * @param \Magento\Sales\Model\AbstractModel $entity
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFields(\Magento\Sales\Model\AbstractModel $entity)
    {
        return $this->traitGetFields($entity);
    }
}
