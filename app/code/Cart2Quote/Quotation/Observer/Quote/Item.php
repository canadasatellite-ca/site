<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Observer\Quote;

/**
 * Class Item
 *
 * @package Cart2Quote\Quotation\Observer\Quote
 */
class Item implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    private $session;

    /**
     * Item constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Session $session
     */
    public function __construct(\Cart2Quote\Quotation\Model\Session $session)
    {
        $this->session = $session;
    }

    /**
     * Execute
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getItem();
        $product = $observer->getProduct();

        if (!$item && $product) {
            $item = $this->session->getQuote()->getItemByProduct($product);
        }

        $quoteProductData = $this->session->getData(\Cart2Quote\Quotation\Model\Session::QUOTATION_PRODUCT_DATA);
        if ($item && is_array($quoteProductData)) {
            foreach ($quoteProductData as $fieldName => &$productData) {
                foreach ($productData as $id => $value) {
                    if ($id == $item->getId()) {
                        $productData[$id] = $item->getData($fieldName);
                    }
                }
            }
            $this->session->addProductData($quoteProductData);
        }
    }
}
