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
class SaveOrder implements ObserverInterface
{
    protected $_objectManager;
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_objectManager= \Magento\Framework\App\ObjectManager::getInstance();
        /** @var Itoris_ReassignOrder_Helper_Data $helper */
        $helper = $this->_objectManager->create('Itoris\ReassignOrder\Helper\Data');
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        if(!$customerSession->isLoggedIn()) {
            if ($helper->isEnabled() && $helper->getAutoReassign()) {
                $email = $observer->getQuote()->getCustomerEmail();
                $customer = $this->_objectManager->get('Magento\Customer\Model\Customer');
                $customer->setWebsiteId($helper->getStore()->getWebsiteId());
                $customer->loadByEmail($email);
                if (!$customer->getId()) {
                    $resource = $this->_objectManager->create('Magento\Framework\App\ResourceConnection');
                    $connection = $resource->getConnection();
                    $table = $resource->getTableName('customer_entity');
                    $id = (int)$connection->fetchOne("select `entity_id` from {$table} where `email`='{$email}'");
                    if ($id > 0) $customer->load($id);
                }
                if ($customer->getId()) {
                    $quote = $observer->getQuote();
                    $order = $observer->getOrder();
                    $quote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_CUSTOMER)
                        ->setCustomerGroupId((int)$customer->getGroupId());
                    $order->setCustomerGroupId((int)$customer->getGroupId());
                    if ($helper->getOverwriteCustomerName() && $customer->getName()) {
                        $quote->setCustomerFirstname($customer->getFirstname())
                            ->setCustomerLastname($customer->getLastname())
                            ->setCustomerIsGuest(false)
                            ->setCustomerId($customer->getId());
                        $order->setCustomerFirstname($customer->getFirstname())
                            ->setCustomerLastname($customer->getLastname())
                            ->setCustomerIsGuest(false)
                            ->setCustomerId($customer->getId());

                        $order->addStatusHistoryComment(__('Guest name %1 was changed to customer\'s name %2', $quote->getBillingAddress()->getName(), $customer->getName()));

                    }
                    $quote->setCustomerId($customer->getId());
                    $order->setCustomerId($customer->getId());
                    if ($helper->getNotifyCustomer()) {
                        $store = $helper->getStore();
                        $templateParams = $helper->getTemplateParams($order, $customer, $store, $quote);
                        $helper->setEmailTo($email);
                        $helper->setName($customer->getFirstname());
                        $templateId = $helper->getEmailTemplate($helper->getStore()->getId());
                        $helper->sendEmail($customer, $templateId, $templateParams, $store);
                    }
                    $order->addStatusHistoryComment(__('Guest\'s order automatically assigned to existing customer by email'));
                    $order->save();
                }
            }
        }

    }
}