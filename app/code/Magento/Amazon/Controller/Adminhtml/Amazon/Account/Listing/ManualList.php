<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;

class ManualList extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Saves Amazon manual relist action
     *
     * @return Redirect $resultRedirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var string */
        $tab = $this->getRequest()->getParam('tab');

        /** @var int */
        if (!$id = $this->getRequest()->getParam('id')) {
            $this->messageManager
                ->addErrorMessage(__('An error occured while processing the request. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        try {
            /** @var \Magento\Amazon\Api\Data\ListingInterface */
            $listing = $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $errorMessage = 'Failed to schedule the listings to be listed.  ';
            $errorMessage .= 'Listings must be in a "Ready To List" status. Please try again.';
            $this->messageManager
                ->addErrorMessage(__($errorMessage));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var int */
        $merchantId = $listing->getMerchantId();

        // must be ready to list
        if ($listing->getListStatus() != Definitions::READY_LIST_STATUS) {
            $errorMessage = 'Failed to schedule the listings to be listed.  ';
            $errorMessage .= 'Listings must be in a "Ready To List" status. Please try again.';
            $this->messageManager
                ->addErrorMessage(__($errorMessage));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => $tab]
            );
        }

        $this->resourceModel->dispatchUpdateListingCommand((int)$merchantId, $listing->getData());

        // process listing removals
        $this->processListingInsertions([$id]);

        $this->messageManager->addSuccessMessage(
            __('Successfully scheduled selected product to be listed on Amazon.')
        );
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => $tab]
        );
    }

    /**
     * Updates listing position to "to be listed" and
     * schedules API action to insert listing
     *
     * @param array $ids
     * @return void
     */
    private function processListingInsertions(array $ids)
    {
        $this->resourceModel->scheduleListStatusUpdate($ids, Definitions::LIST_IN_PROGRESS_LIST_STATUS);
    }
}
