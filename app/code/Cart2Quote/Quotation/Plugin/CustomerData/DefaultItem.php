<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\CustomerData;

use Magento\Framework\App\ObjectManager;

/**
 * Class DefaultItem
 *
 * @package Cart2Quote\Quotation\Plugin\CustomerData
 */
class DefaultItem
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * DefaultItem constructor.
     *
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Around get item data plugin
     *
     * @param \Magento\Checkout\CustomerData\DefaultItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return array
     */
    public function aroundGetItemData(
        \Magento\Checkout\CustomerData\DefaultItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        $result = $proceed($item);
        $imageHelper = $this->imageHelper->init(
            $this->getProductForThumbnail($item),
            'cart_page_product_thumbnail'
        );

        return array_merge(
            $result,
            [
                'quickquote_product_image' => [
                    'src' => $imageHelper->getUrl(),
                    'alt' => $imageHelper->getLabel(),
                    'width' => $imageHelper->getWidth(),
                    'height' => $imageHelper->getHeight(),
                ],
                'quote_configure_url' => $this->getQuoteConfigureUrl($item)
            ]
        );
    }

    /**
     * Get the product from the item to make a thubmnail
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    protected function getProductForThumbnail(\Magento\Quote\Model\Quote\Item $item)
    {
        if (class_exists(\Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface::class)) {
            $itemResolver = ObjectManager::getInstance()->get(
                \Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface::class
            );
            return $itemResolver->getFinalProduct($item);
        }

        return $item->getProduct();
    }

    /**
     * Get quote item configure url
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return string
     */
    protected function getQuoteConfigureUrl(\Magento\Quote\Model\Quote\Item $item)
    {
        return $this->urlBuilder->getUrl(
            'quotation/quote/configure',
            ['id' => $item->getId(), 'product_id' => $item->getProduct()->getId()]
        );
    }
}
