<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class PublishMassAction
 */
class PublishMassAction extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Generates the listing relist page
     *
     * @return \Magento\Framework\Controller\Result\Redirect
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
        $ids = [];

        /** @var ListingInterface[] */
        if (!$collection = $this->getFilteredCollection($merchantId)) {
            $this->messageManager->addErrorMessage(__('Please select items.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => $tab]
            );
        }

        /** @var \Magento\Amazon\Api\Data\ListingInterface $listing */
        foreach ($collection->getItems() as $listing) {
            if ($listing->getListStatus() != Definitions::READY_LIST_STATUS) {
                continue;
            }

            $this->resourceModel->dispatchUpdateListingCommand((int)$merchantId, $listing->getData());

            $ids[] = $listing->getId();
        }

        if (!empty($ids)) {
            $this->processListingInsertions($ids);
            $this->messageManager->addSuccessMessage(
                __('Successfully scheduled ' . count($ids) . ' product(s) to be listed on Amazon.')
            );
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => $tab]
            );
        }

        $errorMessage = 'Failed to schedule the listings to be listed.  ';
        $errorMessage .= 'Listings must be in a "Ready To List" status. Please try again.';

        $this->messageManager->addErrorMessage(__($errorMessage));
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => $tab]
        );
    }

    /*
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
