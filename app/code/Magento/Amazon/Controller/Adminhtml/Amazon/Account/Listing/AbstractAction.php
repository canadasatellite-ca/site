<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing;

use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class AbstractAction
 */
abstract class AbstractAction extends Action
{
    /**
     * @var ListingRepositoryInterface
     */
    protected $listingRepository;

    /**
     * @var ListingManagementInterface
     */
    protected $listingManagement;

    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    protected $frontendUrl;

    /**
     * @param Action\Context $context
     * @param ListingRepositoryInterface $listingRepository
     * @param ListingManagementInterface $listingManagement
     * @param ResourceModel $resourceModel
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     */
    public function __construct(
        Action\Context $context,
        ListingRepositoryInterface $listingRepository,
        ListingManagementInterface $listingManagement,
        ResourceModel $resourceModel,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl
    ) {
        parent::__construct($context);
        $this->listingRepository = $listingRepository;
        $this->listingManagement = $listingManagement;
        $this->resourceModel = $resourceModel;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->frontendUrl = $frontendUrl;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Filters mass action collection based on user selections
     *
     * @param int $merchantId
     * @return bool|\Magento\Amazon\Model\ResourceModel\Amazon\Account\Listing\Collection
     */
    public function getFilteredCollection($merchantId)
    {
        /** @var CollectionFactory */
        $collection = $this->collectionFactory->create();
        /** @var array */
        $selected = $this->getRequest()->getParam('selected');
        /** @var array */
        $excluded = $this->getRequest()->getParam('excluded');
        /** @var array */
        $ids = [];

        try {
            /** @var UiComponentInterface */
            $component = $this->filter->getComponent();
        } catch (LocalizedException $e) {
            return false;
        }

        $this->filter->prepareComponent($component);

        $dataProvider = $component->getContext()->getDataProvider();
        $dataProvider->setLimit(0, false);

        foreach ($dataProvider->getSearchResult()->getItems() as $document) {
            $ids[] = $document->getId();
        }

        $collection->addFieldToFilter('id', ['in' => $ids]);
        $collection->addFieldToFilter('merchant_id', $merchantId);

        // no filter
        if ('false' === $excluded) {
            // no action
        } elseif (is_array($excluded) && !empty($excluded)) { // excluded filter
            $collection->addFieldToFilter('id', ['nin' => $excluded]);
        } elseif (is_array($selected) && !empty($selected)) { // selected filter
            $collection->addFieldToFilter('id', ['in' => $selected]);
        } else { // no filter received
            return false;
        }

        if (count($collection)) {
            return $collection;
        }
        return false;
    }
}
