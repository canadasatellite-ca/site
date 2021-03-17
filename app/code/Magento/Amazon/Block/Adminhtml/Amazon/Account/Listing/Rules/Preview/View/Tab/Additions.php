<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Rules\Preview\View\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Text\ListText;

/**
 * Class Additions
 */
class Additions extends ListText implements TabInterface
{
    /** @var DataPersistorInterface $dataPersistor */
    protected $dataPersistor;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataPersistor = $dataPersistor;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        /** @var */
        $ids = $this->dataPersistor->get('listing_additions');
        /** @var Collection */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('entity_id', ['in' => $ids]);
        $count = $collection->getSize();

        return __('New Listings - ') . $count;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('New Listings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
