<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AutoRelated
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AutoRelated\Observer;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mageplaza\AutoRelated\Helper\Data;

/**
 * Class AddBlock
 * @package Mageplaza\AutoRelated\Observer
 */
class AddBlock implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Mageplaza\AutoRelated\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Mageplaza\AutoRelated\Helper\Data $helperData
     */
    function __construct(
        Http $request,
        Data $helperData
    ) {
        $this->request    = $request;
        $this->helperData = $helperData;
    }

    /**
     * @param Observer $observer
     * @return $this|bool|void
     */
    function execute(Observer $observer)
    {
        if (!$this->helperData->isEnabled()) {
            return false;
        }

        $elementName    = $observer->getElementName();
        $output         = $observer->getTransport()->getOutput();
        $fullActionName = $this->request->getFullActionName();

        if ($fullActionName == 'cms_index_index') {
            return $this;
        }

        $event = $observer->getEvent();
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $event->getLayout();
        $js     = $layout->createBlock('Mageplaza\AutoRelated\Block\Product\ProductList\AutoRelated');
        if ($fullActionName == 'catalog_product_view' || $fullActionName == 'catalog_category_view' || $fullActionName == 'checkout_cart_index') {
            $types = [
                'content' => 'content',
                'related' => 'catalog.product.related',
                'upsell'  => 'product.info.upsell',
                'cross'   => 'checkout.cart.crosssell',
                'sidebar' => 'catalog.leftnav'
            ];

            $type = array_search($elementName, $types);
            if ($type !== false) {
                $output = "<div id=\"mageplaza-autorelated-block-before-{$type}\"></div>" . $output . "<div id=\"mageplaza-autorelated-block-after-{$type}\"></div>";
                if ($type == 'content') {
                    $output .= $js->toHtml();
                }

                $observer->getTransport()->setOutput($output);
            }
        } else if ($elementName == 'content' && ($fullActionName == 'cms_page_view' || $fullActionName == 'cms_index_index')) {
            $output .= $js->setIsCms(true)->toHtml();
            $observer->getTransport()->setOutput($output);
        }

        return $this;
    }
}
