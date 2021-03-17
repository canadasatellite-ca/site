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

namespace Itoris\ReassignOrder\Controller\Adminhtml\Itorisreassignorder;


class MassReassign extends \Magento\Backend\App\Action
{
    protected $_helperItorisData;
    public function execute()
    {
        $post = $this->getRequest()->getPost();
        $orderIds = $post['order_ids'];
        $email = $post['to_email'];
        $orderIds = explode(',',$orderIds);
        $customer =$this->_objectManager->get('Magento\Customer\Model\Customer');
        $res = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $con = $res->getConnection('write');

        foreach($orderIds as $id) {
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($id);
            $quote = $this->_objectManager->create('Magento\Quote\Model\Quote')->load($order->getQuoteId());
            $customer->setWebsiteId($order->getStore()->getWebsiteId());
            $customer->loadByEmail($email);
			$prevName = $quote->getCustomerFirstname().' '.$quote->getCustomerLastname();
			$prevEmail = $quote->getCustomerEmail();

            if(isset($post['settings']['overwrite_customer_name'])) {
                $quote->setCustomerFirstname($customer->getFirstname())
                    ->setCustomerLastname($customer->getLastname())
                    ->setCustomerIsGuest(false)
                    ->save();
                $order->setCustomerFirstname($customer->getFirstname())
                    ->setCustomerLastname($customer->getLastname())
                    ->setCustomerIsGuest(false)
                    ->addStatusHistoryComment(__(
                        'Order was manually reassigned to existing customer, from %1 to %2.
                        Name changed from %3 to %4', $prevEmail, $customer->getEmail(), $prevName, $customer->getName()))
                    ->save();
            } else {
                $order->addStatusHistoryComment(__(
                    'Order was manually reassigned to existing customer, from %1 to %2. No name changed on order.', $prevEmail, $customer->getEmail()));
            }
            $quote->setCustomerEmail($post['to_email'])
                ->setCustomerId($customer->getId())
                ->setCustomerGroupId((int)$customer->getGroupId())
                ->save();
            $order->setCustomerEmail($post['to_email'])
                ->setCustomerId($customer->getId())
                ->setCustomerGroupId((int)$customer->getGroupId())
                ->save();
                
            //updating downloadable links if any
            $con->query("update {$res->getTableName('downloadable_link_purchased')} set `customer_id`={$customer->getId()} where `order_id`={$id}");

        }
        
        $registry = $this->_objectManager->get('Magento\Framework\Registry');
        if (!$registry->registry('reassign_order_reassigned')) {
            $this->_objectManager->get('Magento\Framework\Message\ManagerInterface')->addSuccessMessage(__('Selected orders have been reassigned to %1', $customer->getName()));
            $registry->register('reassign_order_reassigned', 1);
        }

        if(isset($post['settings']['notify_customer'])) {
            foreach($orderIds as $id) {
                /** @var  $order \Magento\Sales\Model\Order */
                $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($id);
                $store = $order->getStore();
                $initialEnvironmentInfo = $this->getHelper()->startEnvironmentEmulation($store->getId());
                $websiteId = $store->getWebsiteId();
                $template = $this->getHelper()->getEmailTemplate($store->getId());
                $this->getHelper()->setEmailTo($post['to_email']);
                $this->getHelper()->setName($customer->getFirstname());
                $templateParams = $this->getHelper()->getTemplateParams($order, $customer, $store, null, $prevName);
                $this->getHelper()->sendEmail($customer, $template, $templateParams, $store);
                $this->getHelper()->stopEnvironmentEmulation($initialEnvironmentInfo);
            }
        }
        $this->_redirect('sales/order/index');
    }
    /** @return \Itoris\ReassignOrder\Helper\Data */
    public function getHelper() {
        if($this->_helperItorisData){
            return $this->_helperItorisData;
        }
        return $this->_helperItorisData=$this->_objectManager->create('Itoris\ReassignOrder\Helper\Data');
    }
}