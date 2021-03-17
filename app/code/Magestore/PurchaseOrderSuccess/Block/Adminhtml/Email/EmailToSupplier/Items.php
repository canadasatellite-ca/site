<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class Items
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier
 */
class Items extends AbstractEmailToSupplier
{
    protected $_template = 'Magestore_PurchaseOrderSuccess::email/email_to_supplier/items.phtml';

    protected $productFactory;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter,
                                \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping $taxShippingService,
                                \Magento\Directory\Model\CountryFactory $countryFactory,
                                \Magento\Directory\Model\RegionFactory $regionFactory,
                                \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status $status,
                                \Magento\Directory\Model\CurrencyFactory $currencyFactory,
                                \Magento\Catalog\Model\ProductFactory $productFactory,
                                array $data)
    {
        $this->productFactory = $productFactory;
        parent::__construct($context,
            $registry,
            $priceFormatter,
            $taxShippingService,
            $countryFactory,
            $regionFactory,
            $status,
            $currencyFactory,
            $data);
    }


    public function checkIsShowQtyReceived()
    {
        $status = $this->getPurchaseOrderData('status');
        return in_array($status, [Status::STATUS_COMPLETED, Status::STATUS_CANCELED]);
    }

    public function getProductName($item)
    {
        $product = $this->productFactory->create();
        $productId = $item->getProductId();
        $name = $product->getResource()->getAttributeRawValue($productId,'vendor_description',0);
        if(!$name){
            $name = $item->getProductName();
        }
        return $name;
    }
}