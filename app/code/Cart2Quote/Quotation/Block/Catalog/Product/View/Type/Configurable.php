<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Catalog\Product\View\Type;

use Magento\Framework\App\ObjectManager;

/**
 * @api
 */
class Configurable extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    private $quotationHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Configurable constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Cart2Quote\Quotation\Helper\Configurable $helper
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param array $data
     * @param \Magento\Framework\Locale\Format|null $localeFormat
     * @param \Magento\Customer\Model\Session|null $customerSession
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices|null $variationPrices
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Cart2Quote\Quotation\Helper\Configurable $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $data = [],
        \Magento\Framework\Locale\Format $localeFormat = null,
        \Magento\Customer\Model\Session $customerSession = null,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices $variationPrices = null
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $helper,
            $catalogProduct,
            $currentCustomer,
            $priceCurrency,
            $configurableAttributeData,
            $data,
            $localeFormat,
            $customerSession,
            $variationPrices
        );

        $this->quotationHelper = $quotationHelper;
        $this->customerSession = $customerSession ?: ObjectManager::getInstance()->get(\Magento\Customer\Model\Session::class);
    }

    /**
     * Check if button can be shown
     *
     * @return bool
     */
    public function showButton()
    {
        return $this->quotationHelper->showButtonOnProductView(
            $this->getProduct(),
            $this->customerSession->getCustomerGroupId()
        );
    }

    /**
     * Get additional config
     *
     * @return array
     */
    protected function _getAdditionalConfig()
    {
        $config = parent::_getAdditionalConfig();
        $config['dynamic_add_buttons'] = $this->quotationHelper->isDynamicAddButtonsEnabled();
        foreach ($this->getAllowProducts() as $product) {
            $config['is_saleable'][$product->getId()] = $product->isSaleable();
            $config['is_quotable'][$product->getId()] = $this->quotationHelper->showButtonOnProductView(
                $product,
                $this->customerSession->getCustomerGroupId()
            );
        }

        return $config;
    }
}
