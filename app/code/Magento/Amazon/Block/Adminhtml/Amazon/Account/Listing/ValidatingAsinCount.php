<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing;

use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class ValidatingAsinCount
 */
class ValidatingAsinCount extends Template
{
    /** @var ListingManagementInterface $listingManagement */
    private $listingManagement;

    /**
     * @param Context $context
     * @param ListingManagementInterface $listingManagement ,
     * @param array $data
     */
    public function __construct(
        Context $context,
        ListingManagementInterface $listingManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->listingManagement = $listingManagement;
        $this->setData('use_container', true);
    }

    /**
     * Get count of listings in VALIDATE_ASIN_LIST_STATUS
     *
     * @return int
     */
    public function getListingCountInValidatingAsin()
    {
        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @var array */
        $conditions = [
            Definitions::VALIDATE_ASIN_LIST_STATUS
        ];

        /** @var int */
        $count = $this->listingManagement->getCountByListStatus($conditions, $merchantId);
        return $this->escapeHtml($count);
    }
}
