<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Plugin\Shipping\Model;

use Magento\Sales\Model\Order\Shipment;
use Magento\Quote\Model\Quote\Address\RateCollectorInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Shipping
{
    public function aroundCollectRates(
        \Magento\Shipping\Model\Shipping $shipping,
        \Closure $closure,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ) {
        $result = $closure($request);
        $rates = $result->getResult()->getAllRates();

        $packaging_fee = 0;
        foreach ($request->getAllItems() as $item){
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $item->getProduct();
            $packaging = (float)$product->getResource()
                ->getAttributeRawValue(
                    $product->getEntityId(),
                    'fixed_shipping_packaging_cost',
                    $request->getStoreId()
                );
            if ($packaging) {
                $packaging_fee +=($packaging*$item->getQty());
            }
        }
        if ($packaging_fee) {
            foreach ($rates as $rate) {
                $m = $rate->getData('carrier') . '_' . $rate->getData('method');
                if (!in_array(
                    $m,
                    [
                        'freeshippingcustom_freeshippingcustom',
                        'customshipprice_customshipprice',
                        'instorepickup_instorepickup',
                        'quotation_quotation'
                    ]
                )) {
                    if ($m == 'freeshipping_freeshipping') {
                        $rate->setPrice('0.01');
                    } else {
                        $rate->setPrice($rate->getPrice() + $packaging_fee);
                    }
                }
            }
        }
        return $result;
    }
}
