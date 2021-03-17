<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing\Alias;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\Data\ListingInterfaceFactory;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Domain\Command\CommandDispatcher;
use Magento\Amazon\Domain\Command\RemoveProduct;
use Magento\Amazon\Domain\Command\RemoveProductFactory;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Save
 */
class Save extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;
    /** @var ListingInterfaceFactory $listingFactory */
    protected $listingFactory;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;

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
    private $frontendUrl;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ListingRepositoryInterface $listingRepository
     * @param ListingInterfaceFactory $listingFactory
     * @param ResourceModel $resourceModel
     * @param CommandDispatcher $commandDispatcher
     * @param RemoveProductFactory $removeProductFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ListingRepositoryInterface $listingRepository,
        ListingInterfaceFactory $listingFactory,
        ResourceModel $resourceModel,
        CommandDispatcher $commandDispatcher,
        RemoveProductFactory $removeProductFactory,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->listingRepository = $listingRepository;
        $this->listingFactory = $listingFactory;
        $this->resourceModel = $resourceModel;
        $this->commandDispatcher = $commandDispatcher;
        $this->removeProductFactory = $removeProductFactory;
        $this->frontendUrl = $frontendUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Creates a new Amazon alias SKU from existing listing
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int */
        $id = $this->getRequest()->getParam('id');
        /** @var string */
        $successMessage = 'The new Alias SKU has been saved and is in the process of listing to Amazon.';
        /** @var string */
        $errorMessage = 'The new Alias Seller SKU could not be saved.  ';
        /** @var string */
        $errorMessage .= 'Please check to ensure the assigned Seller SKU is not already in use and try again.';

        try {
            /** @var ListingInterface */
            $listing = $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Could not load the listing data. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var int */
        $merchantId = $listing->getMerchantId();
        /** @var string */
        $sku = $this->getRequest()->getParam('new_sku');
        /** @var string */
        $asin = $this->getRequest()->getParam('asin');
        /** @var string */
        $tab = $this->getRequest()->getParam('tab');

        /** @var ListingInterface */
        $newListing = $this->listingFactory->create();

        $data = $listing->getData();
        unset($data['id']);
        unset($data['created_on']);
        unset($data['asin']);
        unset($data['seller_sku']);
        unset($data['list_status']);

        $newListing->setData($data);
        $newListing->setAsin($asin);
        $newListing->setSellerSku($sku);
        $newListing->setProductIdType(1);
        $newListing->setProductId($asin);
        $newListing->setListStatus(Definitions::VALIDATE_ASIN_LIST_STATUS);

        if ($listing->getListStatus() == Definitions::THIRDPARTY_LIST_STATUS) {
            $newListing->setListStatus(Definitions::THIRDPARTY_LIST_STATUS);
        }

        try {
            $this->listingRepository->save($newListing);
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__($errorMessage));
            return $resultRedirect->setPath('channel/amazon/account_listing_alias_index', ['id' => $id]);
        }

        if ($this->getRequest()->getParam('remove_flag')) {
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
        }

        $this->messageManager->addSuccessMessage(__($successMessage));
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => $tab]
        );
    }
}
