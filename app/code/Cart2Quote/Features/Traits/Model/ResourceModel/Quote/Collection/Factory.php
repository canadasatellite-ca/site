<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Collection;
/**
 * Trait Factory
 *
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
 */
trait Factory
{
    /**
     * Create function
     *
     * @param string $className
     * @param array $data
     * @return AbstractCollection
     * @throws \InvalidArgumentException
     */
    private function create($className, array $data = [])
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$instance = $this->_objectManager->create($className, $data);
        if (!$instance instanceof AbstractCollection) {
            $className = \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection\AbstractCollection::class;
            $message = __('does not implement %s', $className);
            throw new \InvalidArgumentException($className . ' ' . $message);
        }
        return $instance;
		}
	}
}
