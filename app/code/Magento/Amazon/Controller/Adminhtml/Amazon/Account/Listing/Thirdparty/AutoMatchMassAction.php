<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing\Thirdparty;

use Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing\AbstractAction;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory;
use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class EndListingMassAction
 */
class AutoMatchMassAction extends AbstractAction
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Save ended listing
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
        $listingIds = [];
        /** @var int */
        $count = 0;

        /** @var CollectionFactory */
        if (!$collection = $this->getFilteredCollection($merchantId)) {
            $this->messageManager->addErrorMessage(__('Please select items.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => $tab]
            );
        }

        // filter on third party listings
        $collection->addFieldToFilter('list_status', Definitions::THIRDPARTY_LIST_STATUS);

        foreach ($collection as $listing) {
            $listingIds[] = $listing->getId();
        }

        $count = $this->listingManagement->insertUnmatchedListing($merchantId, $listingIds);

        if ($count) {
            $response = 'Successfully found ' . $count;
            $response .= ' imported third party listings that matched a catalog product.  ';
            $response .= 'These listings have been moved to an active status.';
            $this->messageManager->addSuccessMessage(__($response));
        } else {
            $response = 'Unable to match any third party listings to catalog products.  ';
            $response .= 'Please check your listing settings to ensure you have proper search criteria.';
            $this->messageManager->addWarningMessage(__($response));
        }

        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => $tab]
        );
    }
}
