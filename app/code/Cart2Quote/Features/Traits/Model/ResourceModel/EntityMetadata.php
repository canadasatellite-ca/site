<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel;
/**
 * Trait EntityMetadata
 */
trait EntityMetadata
{
    /**
     * Returns list of entity fields that are applicable for persistence operations
     *
     * @param \Magento\Sales\Model\AbstractModel $entity
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getFields(\Magento\Sales\Model\AbstractModel $entity)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!isset($this->metadataInfo[get_class($entity)])) {
            $this->metadataInfo[get_class($entity)] =
                $entity->getResource()->getConnection()->describeTable(
                    $entity->getResource()->getMainTable()
                );
        }
        return $this->metadataInfo[get_class($entity)];
		}
	}
}
