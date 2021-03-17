<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing;

use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Domain\Command\CommandDispatcher;
use Magento\Amazon\Domain\Command\RemoveProduct;
use Magento\Amazon\Domain\Command\RemoveProductFactory;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class EndListingMassAction
 */
class EndListingMassAction extends AbstractAction
{
    /**
     * @var CommandDispatcher
     */
    private $commandDispatcher;

    /**
     * @var RemoveProductFactory
     */
    private $removeProductFactory;

    /**
     * @param Action\Context $context
     * @param ListingRepositoryInterface $listingRepository
     * @param ListingManagementInterface $listingManagement
     * @param ResourceModel $resourceModel
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CommandDispatcher $commandDispatcher
     * @param RemoveProductFactory $removeProductFactory
     */
    public function __construct(
        Action\Context $context,
        ListingRepositoryInterface $listingRepository,
        ListingManagementInterface $listingManagement,
        ResourceModel $resourceModel,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CommandDispatcher $commandDispatcher,
        RemoveProductFactory $removeProductFactory,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl
    ) {
        parent::__construct(
            $context,
            $listingRepository,
            $listingManagement,
            $resourceModel,
            $filter,
            $collectionFactory,
            $frontendUrl
        );
        $this->commandDispatcher = $commandDispatcher;
        $this->removeProductFactory = $removeProductFactory;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Save ended listing
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @var string */
        $tab = $this->getRequest()->getParam('tab');
        /** @var array */
        $listingIds = [];

        /** @var ListingInterface[] */
        if (!$collection = $this->getFilteredCollection($merchantId)) {
            $this->messageManager
                ->addErrorMessage(__('Please select items.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                [
                    'merchant_id' => $merchantId,
                    'active_tab' => $tab
                ]
            );
        }

        /** @var ListingInterface $listing */
        foreach ($collection->getItems() as $listing) {
            if (!$merchantId) {
                $merchantId = $listing->getMerchantId();
            }

            $commandData = [
                'body' => [
                    'sku' => $listing->getSellerSku(),
                ],
                'identifier' => $listing->getSellerSku(),
            ];

            /** @var RemoveProduct $command */
            $command = $this->removeProductFactory->create($commandData);
            $this->commandDispatcher->dispatch($merchantId, $command);
            $listingIds[] = $listing->getId();
        }

        // process listing removals
        if (!empty($listingIds)) {
            $this->resourceModel->scheduleListStatusUpdate($listingIds, Definitions::TOBEENDED_LIST_STATUS);
        }

        /** @var string */
        $response = __('The selected listings are in the process of ending.  ') .
            __('They will remain unlisted until you re-publish the listings from the "Manually Ended Listings" table.');

        $this->messageManager
            ->addSuccessMessage($response);
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            [
                'merchant_id' => $merchantId,
                'active_tab' => $tab
            ]
        );
    }
}
