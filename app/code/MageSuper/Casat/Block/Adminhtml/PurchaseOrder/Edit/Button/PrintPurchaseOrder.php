<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageSuper\Casat\Block\Adminhtml\PurchaseOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;

/**
 * Class
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button
 */
class PrintPurchaseOrder extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    /**
     * {@inheritdoc}
     */
    function getButtonData()
    {
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        $purchaseOrderId = $purchaseOrder->getId();
        $type = $purchaseOrder->getType();
        if($purchaseOrderId){

            $url = $this->getUrl('casat/purchaseOrder/pdf', [
                    'purchase_id' => $purchaseOrderId, 'type' => $type]);
            $onClick = sprintf("setLocation('%s')", $url);

            return [
                'label' => __('Print'),
                'class' => 'save',
                'on_click' => $onClick
            ];
        }
        return [];
    }
}
