<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Thirdparty\Manual\View\Help;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\ConfigManagementInterface;
use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Block\Adminhtml\Amazon\General;
use Magento\Amazon\Ui\FrontendUrl;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Manual
 */
class Manual extends General
{
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;

    /**
     * @param Context $context
     * @param AccountRepositoryInterface $accountRepository
     * @param ConfigManagementInterface $configManagement
     * @param ListingRepositoryInterface $listingRepository
     * @param FrontendUrl $frontendUrl
     * @param array $data
     */
    public function __construct(
        Context $context,
        AccountRepositoryInterface $accountRepository,
        ConfigManagementInterface $configManagement,
        ListingRepositoryInterface $listingRepository,
        FrontendUrl $frontendUrl,
        array $data = []
    ) {
        parent::__construct($context, $accountRepository, $configManagement, $frontendUrl, $data);
        $this->listingRepository = $listingRepository;
        $this->setData('use_container', true);
    }

    /**
     * Returns the current Amazon ASIN
     *
     * @return string
     */
    public function getAmazonAsin()
    {
        /** @var int */
        $id = $this->getRequest()->getParam('id');

        try {
            /** @var ListingInterface */
            $listing = $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            // no listing found
            return null;
        }

        return $listing->getAsin();
    }

    /**
     * Returns the current Amazon Name
     *
     * @return string
     */
    public function getAmazonName()
    {
        /** @var int */
        $id = $this->getRequest()->getParam('id');

        try {
            /** @var ListingInterface */
            $listing = $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            // no listing found
            return null;
        }

        return $listing->getName();
    }
}
