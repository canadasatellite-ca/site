<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Update\View;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\ConfigManagementInterface;
use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Block\Adminhtml\Amazon\General;
use Magento\Amazon\Ui\FrontendUrl;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Help
 */
class Help extends General
{
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;
    /** @var ProductRepositoryInterface $productRepository */
    protected $productRepository;

    /**
     * @param Context $context
     * @param AccountRepositoryInterface $accountRepository
     * @param ConfigManagementInterface $configManagement
     * @param ListingRepositoryInterface $listingRepository
     * @param ProductRepositoryInterface $productRepository
     * @param FrontendUrl $frontendUrl
     * @param array $data
     */
    public function __construct(
        Context $context,
        AccountRepositoryInterface $accountRepository,
        ConfigManagementInterface $configManagement,
        ListingRepositoryInterface $listingRepository,
        ProductRepositoryInterface $productRepository,
        FrontendUrl $frontendUrl,
        array $data = []
    ) {
        parent::__construct($context, $accountRepository, $configManagement, $frontendUrl, $data);
        $this->listingRepository = $listingRepository;
        $this->productRepository = $productRepository;
        $this->setData('use_container', true);
    }

    /**
     * Returns product interface
     *
     * @return bool|ProductInterface
     */
    public function loadProductInterface()
    {
        /** @var int */
        $id = $this->getRequest()->getParam('id');

        try {

            /** @var ListingInterface */
            $listing = $this->listingRepository->getById($id);
            /** @var ProductRepositoryInterface */
            return $this->productRepository->getById($listing->getCatalogProductId());
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * Returns parent ASIN
     *
     * @return string
     */
    public function getParentAsin()
    {
        /** @var int */
        $id = $this->getRequest()->getParam('id');

        try {

            /** @var ListingInterface */
            $listing = $this->listingRepository->getById($id);

            return $listing->getAsin();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }
}
