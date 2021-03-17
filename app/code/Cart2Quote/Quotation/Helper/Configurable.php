<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

use Magento\Framework\App\ObjectManager;

/**
 * Class Data
 *
 * Helper class for getting options
 *
 * @api
 */
class Configurable extends \Magento\ConfigurableProduct\Helper\Data
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
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Customer\Model\Session|null $customerSession
     * @param \Magento\Catalog\Model\Product\Image\UrlBuilder|null $urlBuilder
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Customer\Model\Session $customerSession = null,
        $urlBuilder = null
    ) {
        //fallback so that we support < 2.3.0
        if (!class_exists(\Magento\Catalog\Model\Product\Image\UrlBuilder::class)) {
            //Magento 2.2.x
            parent::__construct(
                $imageHelper
            );
        } else {
            if ($urlBuilder === null) {
                $urlBuilder = ObjectManager::getInstance()->get(\Magento\Catalog\Model\Product\Image\UrlBuilder::class);
            }

            //Magento 2.3.0 and newer
            parent::__construct(
                $imageHelper,
                $urlBuilder
            );
        }

        $this->quotationHelper = $quotationHelper;
        $this->customerSession = $customerSession ?: ObjectManager::getInstance()->get(\Magento\Customer\Model\Session::class);
    }

    /**
     * Check if button can be shown
     *
     * @param $product
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function showButton($product)
    {
        return $this->quotationHelper->showButtonOnProductView(
            $product,
            $this->customerSession->getCustomerGroupId()
        );
    }

    /**
     * Get Options for Configurable Product Options
     *
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = [];
        $allowAttributes = $this->getAllowAttributes($currentProduct);

        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            foreach ($allowAttributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if ($product->isSalable() || $this->showButton($product)) {
                    $options[$productAttributeId][$attributeValue][] = $productId;
                }
                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }
        return $options;
    }
}
