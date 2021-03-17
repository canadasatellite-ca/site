<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Listing\Thirdparty\Create;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory;

/**
 * Class TaxClassId
 */
class TaxClassId implements OptionSourceInterface
{
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Creates the core tax classes
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        $data = [];
        /** @var CollectionFactory */
        $taxes = $this->collectionFactory->create();

        $taxes->setClassTypeFilter('PRODUCT');
        $taxes = $taxes->toOptionArray();

        $data[] = ['label' => __('None'), 'value' => '0'];

        foreach ($taxes as $tax) {
            $data[] = ['label' => __($tax['label']), 'value' => $tax['value']];
        }

        return $data;
    }
}
