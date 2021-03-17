<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Product\Renderer\Listing;

/**
 * Class Configurable
 *
 * @package Cart2Quote\Quotation\Block\Product\Renderer\Listing
 */
class Configurable extends \Magento\Swatches\Block\Product\Renderer\Listing\Configurable
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    private $quotationHelper;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Configurable constructor.
     *
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\ConfigurableProduct\Helper\Data $helper
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Swatches\Helper\Media $swatchMediaHelper
     * @param array $data
     * @param null|\Magento\Swatches\Model\SwatchAttributesProvider $swatchAttributesProvider
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $swatchMediaHelper,
        array $data = [],
        $swatchAttributesProvider = null
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
            $swatchHelper,
            $swatchMediaHelper,
            $data,
            $swatchAttributesProvider
        );

        $this->quotationHelper = $quotationHelper;
        $this->customerSession = $customerSession;
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

    /**
     * Get allow products based on the selected products
     *
     * @return \Magento\Catalog\Model\Product[]|mixed
     */
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = [];
            $allProducts = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);
            foreach ($allProducts as $product) {
                $products[] = $product;
            }
            $this->setAllowProducts($products);
        }

        return $this->getData('allow_products');
    }

    /**
     * Produce and return block's html output
     *
     * This method should not be overridden. You can override _toHtml() method in descendants if needed.
     *
     * C2Q: We still chose to override this method, as preferences in di.xml stays active when a module output is
     * disabled, and some of those block class overwrites caused issues when the module was output disabled.
     *
     * We removed the check on `advanced/modules_disable_output/cart2quote_quotation` due to:
     * `<preference for="Magento\Swatches\Block\Product\Renderer\Listing\Configurable"
     *      type="Cart2Quote\Quotation\Block\Product\Renderer\Listing\Configurable"/>` in di.xml
     *
     * @return string
     */
    public function toHtml()
    {
        //added from \Magento\Swatches\Block\Product\Renderer\Configurable::toHtml
        $this->setTemplate(
            $this->getRendererTemplate()
        );

        //added from \Magento\Framework\View\Element\AbstractBlock::toHtml
        $this->_eventManager->dispatch('view_block_abstract_to_html_before', ['block' => $this]);

        $html = $this->_loadCache();
        $html = $this->_afterToHtml($html);

        /** @var \Magento\Framework\DataObject */
        $transportObject = new \Magento\Framework\DataObject(
            [
                'html' => $html,
            ]
        );
        $this->_eventManager->dispatch(
            'view_block_abstract_to_html_after',
            [
                'block' => $this,
                'transport' => $transportObject
            ]
        );
        $html = $transportObject->getHtml();

        return $html;
    }
}
