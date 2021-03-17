<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection;

/**
 * Class Factory
 *
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
 */
class Factory //implements Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Collection\Factory {
        create as private traitCreate;
    }

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create function
     *
     * @param string $className
     * @param array $data
     * @return AbstractCollection
     * @throws \InvalidArgumentException
     */
    public function create($className, array $data = [])
    {
        return $this->traitCreate($className, $data);
    }
}
