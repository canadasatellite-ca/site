<?php


namespace MageSuper\CustomProductCategoryUrl\Observer\Catalog;

use Magento\Catalog\Model\Product;
use MageSuper\CustomProductCategoryUrl\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Event\ObserverInterface;

class ProductSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();

        if ($product->getData('volusion_url') && $product->getData('volusion_url')!==$product->getData('url_key')) {
            $product->setData('url_key_create_redirect',$product->getData('url_key'));
            $product->setData('url_key',$product->getData('volusion_url'));
            $product->setData('save_rewrites_history', true);
        }
    }
}
