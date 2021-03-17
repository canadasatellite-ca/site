<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\System\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Adminhtml quotation add custom field controller
 */
class AddCustomField extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * AddCustomField constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Cart2Quote\Quotation\Helper\Data $dataHelper
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Cart2Quote\Quotation\Helper\Data $dataHelper,
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_resourceConfig = $resourceConfig;
        $this->_scopeConfig = $scopeConfig;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->_resultJsonFactory->create();

        //TODO retrieve selected scope
        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $scopeCode = 0;

        $customFields = $this->_dataHelper->getCustomFieldsConfig($scopeType, $scopeCode);
        $index = 0;
        foreach ($customFields as $key => $customField) {
            if ($index !== $key) {
                break;
            }
            $index++;
        }
        $type = $this->getRequest()->getParam('type', 'text');
        $this->addType($type, $index, $scopeType, $scopeCode);
        return $result->setData(['success' => true]);
    }

    /**
     * Add custom field type to scope
     *
     * @param string $type
     * @param string $id
     * @param string $scopeType
     * @param null|string $scopeCode
     */
    protected function addType($type, $id, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        switch ($type) {
            case 'text':
                $this->_resourceConfig->saveConfig(
                    'cart2quote_quote_form_settings/quote_form_custom_field_' . $id . '/type',
                    'text',
                    $scopeType,
                    $scopeCode
                );
                $this->_resourceConfig->saveConfig(
                    'cart2quote_quote_form_settings/quote_form_custom_field_' . $id . '/label',
                    'text',
                    $scopeType,
                    $scopeCode
                );
                break;
            case 'textarea':
                $this->_resourceConfig->saveConfig(
                    'cart2quote_quote_form_settings/quote_form_custom_field_' . $id . '/type',
                    'textarea',
                    $scopeType,
                    $scopeCode
                );
                $this->_resourceConfig->saveConfig(
                    'cart2quote_quote_form_settings/quote_form_custom_field_' . $id . '/label',
                    'text',
                    $scopeType,
                    $scopeCode
                );
                break;
        }
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        //$this->_authorization->isAllowed('Cart2Quote_Quotation::add_custom_field');
        return true;
    }
}
