<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace MageSuper\Casat\Plugin\Sales\Model\Order;

/**
 * Class Item
 * @package Aitoc\OrdersExportImport\Plugin\Sales\Model\Order\
 */
class Comment
{

    public function aroundExecute(\Magento\Sales\Controller\Adminhtml\Order\AddComment $subject, \Closure $proceed)
    {
        $params = $subject->getRequest()->getParams();
        $orderId = $params['order_id'];
        $comment = $params['history']['comment'];

        $collection = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('MageSuper\Casat\Model\ResourceModel\OrderComment\Collection');

        $collection->addFieldToFilter('order_id', $orderId);
        if ($collection->count()) {
            foreach ($collection as $item) {
                $item->setData('comment', $comment)->save();
            }
        } else {
            $item = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('MageSuper\Casat\Model\OrderComment');
            $item->setData('order_id', $orderId)->setData('comment', $comment)->save();
        }

        return $proceed();
    }

}
