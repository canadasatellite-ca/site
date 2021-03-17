<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid;

/**
 * Quotation quotes statuses option array
 */
class StatusesArray implements \Magento\Framework\Data\OptionSourceInterface
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Grid\StatusesArray {
        toOptionArray as private traitToOptionArray;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\CollectionFactory
     */
    protected $statusCollectionFactory;
    /**
     * @var array
     */
    protected $options;

    /**
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\CollectionFactory $statusCollectionFactory
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\CollectionFactory $statusCollectionFactory
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->traitToOptionArray();
    }
}
