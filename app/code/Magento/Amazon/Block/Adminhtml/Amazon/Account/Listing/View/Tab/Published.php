<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\View\Tab;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Text\ListText;

/**
 * Class Published
 */
class Published extends ListText implements TabInterface
{
    /** @var ListingManagementInterface $listingManagement */
    private $listingManagement;
    /** @var Http $request */
    private $request;
    /** @var AccountListingRepositoryInterface $accountListingRepository */
    protected $accountListingRepository;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ListingManagementInterface $listingManagement
     * @param Http $request
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ListingManagementInterface $listingManagement,
        Http $request,
        AccountListingRepositoryInterface $accountListingRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->listingManagement = $listingManagement;
        $this->request = $request;
        $this->accountListingRepository = $accountListingRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Ready To List - ' . $this->fetchRecordCount());
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Ready To List Listings');
    }

    /**
     * Get row count by condition
     *
     * @return int
     */
    private function fetchRecordCount()
    {
        /** @var int */
        $merchantId = $this->request->getParam('merchant_id');
        /** @var array */
        $conditions = [
            Definitions::READY_LIST_STATUS,
            Definitions::LIST_IN_PROGRESS_LIST_STATUS,
            Definitions::GENERAL_SEARCH_LIST_STATUS
        ];

        /** @var int */
        return $this->listingManagement->getCountByListStatus($conditions, $merchantId);
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        $merchantId = $this->request->getParam('merchant_id');
        $account = $this->accountListingRepository->getByMerchantId($merchantId);
        return !$account->getAutoList();
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
