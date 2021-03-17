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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class EndListing
 */
class EndListing extends AbstractAction
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
     * @var \Magento\Amazon\Ui\FrontendUrl
     */

    /**
     * @param Action\Context $context
     * @param ListingRepositoryInterface $listingRepository
     * @param ListingManagementInterface $listingManagement
     * @param ResourceModel $resourceModel
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CommandDispatcher $commandDispatcher
     * @param RemoveProductFactory $removeProductFactory
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
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
     * Save ended listing
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var string */
        $tab = $this->getRequest()->getParam('tab');

        /** @var array */
        if (!$id = $this->getRequest()->getParam('id')) {
            $this->messageManager
                ->addErrorMessage(__('An error occurred while processing the request. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        try {
            /** @var ListingInterface */
            $listing = $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager
                ->addErrorMessage(__('An error occurred while processing the request. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var int */
        $merchantId = $listing->getMerchantId();
        $commandData = [
            'body' => [
                'sku' => $listing->getSellerSku(),
            ],
            'identifier' => $listing->getSellerSku(),
        ];
        /** @var RemoveProduct $command */
        $command = $this->removeProductFactory->create($commandData);
        $this->commandDispatcher->dispatch($merchantId, $command);
        $this->resourceModel->scheduleListStatusUpdate([$listing->getId()], Definitions::TOBEENDED_LIST_STATUS);

        /** @var string */
        $response = __('The selected listings are in the process of ending.  ') .
            __('They will remain unlisted until you re-publish the listings from the "Manually Ended Listings" table.');

        $this->messageManager->addSuccessMessage($response);
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            [
                'merchant_id' => $merchantId,
                'active_tab' => $tab
            ]
        );
    }
}
