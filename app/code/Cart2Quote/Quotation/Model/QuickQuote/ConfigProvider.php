<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\QuickQuote;

/**
 * Class ConfigProvider
 *
 * @package Cart2Quote\Quotation\Model\QuickQuote
 */
class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    use \Cart2Quote\Features\Traits\Model\QuickQuote\ConfigProvider {
        getConfig as private traitGetConfig;
        isCustomerLoggedIn as private traitIsCustomerLoggedIn;
    }

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Cart2Quote\Quotation\Helper\Address
     */
    private $addressHelper;

    /**
     * ConfigProvider constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Cart2Quote\Quotation\Helper\Address $addressHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Http\Context $httpContext,
        \Cart2Quote\Quotation\Helper\Address $addressHelper
    ) {
        $this->formKey = $formKey;
        $this->httpContext = $httpContext;
        $this->storeManager = $storeManager;
        $this->addressHelper = $addressHelper;
    }

    /**
     * Get config
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
        return $this->traitGetConfig();
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     * @codeCoverageIgnore
     */
    private function isCustomerLoggedIn()
    {
        return $this->traitIsCustomerLoggedIn();
    }
}
