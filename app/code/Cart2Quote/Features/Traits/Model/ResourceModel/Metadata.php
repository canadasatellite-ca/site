<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel;
/**
 * Trait Metadata
 *
 * @package Cart2Quote\Quotation\Model\ResourceModel
 */
trait Metadata
{
    /**
     * Get mapper
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    private function getMapper()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->objectManager->get($this->resourceClassName);
		}
	}
    /**
     * Get new instance
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface
     */
    private function getNewInstance()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->objectManager->create($this->modelClassName);
		}
	}
}
