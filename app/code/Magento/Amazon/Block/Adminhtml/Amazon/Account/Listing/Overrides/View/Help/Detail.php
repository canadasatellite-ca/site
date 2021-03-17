<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Overrides\View\Help;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\ConfigManagementInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Block\Adminhtml\Amazon\General;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Ui\FrontendUrl;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Detail
 */
class Detail extends General
{
    protected $_template = 'Magento_Amazon::amazon/account/listing/overrides/detail.phtml';

    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;
    /** @var ConfigManagementInterface $configManagement */
    protected $configManagement;
    /** @var PriceCurrencyInterface $priceFormatter */
    protected $priceFormatter;

    /**
     * @param Context $context
     * @param AccountRepositoryInterface $accountRepository
     * @param ConfigManagementInterface $configManagement
     * @param ListingRepositoryInterface $listingRepository
     * @param PriceCurrencyInterface $priceFormatter
     * @param FrontendUrl $frontendUrl
     * @param array $data
     */
    public function __construct(
        Context $context,
        AccountRepositoryInterface $accountRepository,
        ConfigManagementInterface $configManagement,
        ListingRepositoryInterface $listingRepository,
        PriceCurrencyInterface $priceFormatter,
        FrontendUrl $frontendUrl,
        array $data = []
    ) {
        parent::__construct($context, $accountRepository, $configManagement, $frontendUrl, $data);
        $this->accountRepository = $accountRepository;
        $this->configManagement = $configManagement;
        $this->listingRepository = $listingRepository;
        $this->priceFormatter = $priceFormatter;
        $this->setData('use_container', true);
    }

    /**
     * Returns the listing override object
     *
     * @return bool | ListingInterface
     */
    public function getListing()
    {
        if (!$id = $this->getRequest()->getParam('id')) {
            return false;
        }

        try {
            /** @var ListingInterface */
            return $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            // no listing found
            return false;
        }
    }

    /**
     * Adds currency symbol to price output in template
     *
     * @param string $price
     * @return string
     */
    public function formatCurrency($price)
    {
        $merchantId = $this->getRequest()->getParam('merchant_id');
        $currency = null;

        try {
            /** @var AccountInterface */
            $account = $this->accountRepository->getByMerchantId($merchantId);
            $countryCode = $account->getCountryCode();
            $currency = Definitions::getCurrencyCode($countryCode, 'USD');
        } catch (NoSuchEntityException $e) {
            $currency = 'USD';
        }

        return $this->priceFormatter->format(
            $price,
            false,
            null,
            null,
            $currency
        );
    }
}
