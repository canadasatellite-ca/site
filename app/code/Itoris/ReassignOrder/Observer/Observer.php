<?php

/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_REASSIGN_ORDER
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */
namespace Itoris\ReassignOrder\Observer;
use Magento\Framework\Event\ObserverInterface;
class Observer implements ObserverInterface
{
    protected $_objectManager;
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objectManager =$this->_objectManager= \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('Itoris\ReassignOrder\Helper\Data');
        $backendHelper =$objectManager->create('Magento\Backend\Helper\Data');
            $block = $observer->getBlock();
            if ($block->getType() == 'Magento\Backend\Block\Widget\Grid\Massaction' && $block->getRequest()->getControllerName() == 'sales_order') {
                $block->addItem('assign_order', array(
                    'label'        => __('Assign to Customer'),
                    'url'          => $backendHelper->getUrl('itorisreassignorder/itorisreassignorder/massReassign'),
                ));
            }
    }
}