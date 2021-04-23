<?php
namespace MageSuper\Casat\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductIsSalableCheck implements ObserverInterface
{
    protected $_catalogProduct = null;
    function __construct(
        \Magento\Catalog\Helper\Product $catalogProduct
    )
    {
        $this->_catalogProduct = $catalogProduct;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_catalogProduct->setSkipSaleableCheck(true);
        $salable = $observer->getData('salable');
        $salable->setData('is_salable',true);
    }
}
