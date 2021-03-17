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
namespace Itoris\ReassignOrder\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_transportBuilder;
    const SCOPE_TYPE_STORES = 'stores';
    protected $emulation;
    protected $settings = [];
    protected $messageManager;
    protected $_storeManager;
    protected $_objectManager;
    protected $_scopeConfig;
    protected $email;
    protected $name;
    /** @var $design \Magento\Framework\View\DesignInterface  */
    protected $design;
    public function __construct(
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->design=$design;
        $this->messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_coreRegistry = $registry;
        $this->_backendConfig = $backendConfig;
        $this->_localeDate = $localeDate;
        $this->_transportBuilder=$transportBuilder;
        $this->_scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        parent::__construct($context);
    }
    public function isEnabled() {
        return (int)$this->_backendConfig->getValue('itoris_reassignorder/general/enabled') && !$this->isDisabledForStore()
        && count(explode('|', $this->_backendConfig->getValue('itoris_core/installed/Itoris_ReassignOrder'))) == 2;
    }

    public function isDisabledForStore(){
        return !(bool)$this->_scopeConfig->getValue('itoris_reassignorder/general/enabled', self::SCOPE_TYPE_STORES, $this->_storeManager->getStore()->getId());
    }
    /**
     * @return \Magento\Backend\App\ConfigInterface|mixed
     */
    public function getBackendConfig(){
        return $this->_backendConfig;
    }
    public function startEnvironmentEmulation($storeId) {
        $area = \Magento\Framework\App\Area::AREA_FRONTEND;
        /** @var  $emulation  \Magento\Store\Model\App\Emulation */
        $emulation = $this->_objectManager->create('Magento\Store\Model\App\Emulation');
        $emulation->startEnvironmentEmulation($storeId, $area);
        $this->emulation=$emulation;
        return $emulation;
    }
    public function getEmailTemplate($store){
        return  $this->_scopeConfig->getValue('itoris_reassignorder/general/email_template', self::SCOPE_TYPE_STORES, $store);
    }
    public function getAutoReassign(){
        return $store = $this->_scopeConfig->getValue('itoris_reassignorder/general/auto_reassign', self::SCOPE_TYPE_STORES, $this->_storeManager->getStore()->getId());
    }
    public function getNotifyCustomer(){
        return $store = $this->_scopeConfig->getValue('itoris_reassignorder/general/notify_customer', self::SCOPE_TYPE_STORES, $this->_storeManager->getStore()->getId());
    }
    public function getOverwriteCustomerName(){
        return $store = $this->_scopeConfig->getValue('itoris_reassignorder/general/overwrite_customer_name', self::SCOPE_TYPE_STORES, $this->_storeManager->getStore()->getId());
    }
    public function stopEnvironmentEmulation() {
        $this->emulation->stopEnvironmentEmulation();
        return $this;
    }
    public function getTemplateParams(\Magento\Sales\Model\Order $order, \Magento\Customer\Model\Customer $customer,  \Magento\Store\Model\Store $store,\Magento\Quote\Model\Quote $quote = null, $prevName = '')
    {
        $templateParams = array();
        if(is_null($quote)) {
            $quote = $order;
            $templateParams['created'] = $quote->getCreatedAt();
        }else {
            /** @var  $app \Magento\Framework\Locale\Resolver */
            $templateParams['created'] = $quote->getCreatedAt();
        }
        $templateParams['from_name'] = $prevName ? $prevName : $quote->getBillingAddress()->getName();
        $templateParams['order'] = $order->getIncrementId();
        $templateParams['customer'] = $customer;
        $templateParams['store'] = $store;
        return $templateParams;
    }
    public function setEmailTo($email){
        $this->email=$email;
    }
    public function sendEmail(\Magento\Customer\Model\Customer $customer, $templateId, $templateParams = array(), $store)
    {
        $state = $this->_objectManager->get('Magento\Framework\App\State');
        $sender['email'] = $this->_scopeConfig->getValue('trans_email/ident_general/email');
        $sender['name'] = $this->_scopeConfig->getValue('trans_email/ident_general/name');
        try {
            $registry = $this->_objectManager->get('Magento\Framework\Registry');
            if (!$registry->registry('reassign_order_email_sent')) {
                $this->getMessageManager()->addSuccessMessage(__('Email has been sent'));
                $registry->register('reassign_order_email_sent', 1);
            }
            $this->_sendEmailTemplate($templateId,$sender,$templateParams,$store->getId());
        }
        catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage(__('Failed to send email'));
        }
    }
    public function setName($name){
        $this->name=$name;
    }
    /** @return \Magento\Framework\Message\ManagerInterface */
    public function getMessageManager(){
        return $this->_objectManager->get('Magento\Framework\Message\ManagerInterface');
    }
    /**
     * Send corresponding email template
     *
     * @param string $template configuration path of email template
     * @param string $sender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @return $this
     */
    protected function _sendEmailTemplate($template, $sender, $templateParams = [], $storeId = null)
    {
        /** @var \Magento\Framework\Mail\TransportInterface $transport */
        $transport = $this->_transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            $templateParams
        )->setFrom(
            $sender
        )->addTo(
            $this->email,
            $this->name
        )->getTransport();


        $transport->sendMessage();
        return $this;
    }
    public function getStore(){
       return $this->_storeManager->getStore();
    }
}