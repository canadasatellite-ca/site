<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Pricing\Rules\Create;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class View
 */
class View extends Container
{
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;

    /**
     * @param Context $context
     * @param AccountRepositoryInterface $accountRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        AccountRepositoryInterface $accountRepository,
        array $data = []
    ) {
        $this->accountRepository = $accountRepository;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_amazon_account_pricing_rules_create';
        $this->_mode = 'view';
        $this->_blockGroup = 'Magento_Amazon';
        $this->setData('id', 'channel_amazon_account_pricing_rules_create');

        $this->buttonList->remove('delete');

        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');

        try {

            /** @var string */
            $backUrl = $this->getUrl('channel/amazon/account_pricing_rules_index', ['merchant_id' => $merchantId]);

            $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $backUrl . '\')');
            $this->buttonList->update('back', 'class', 'spectrumButton spectrumButton--secondary back');
            $this->buttonList->update('reset', 'class', 'spectrumButton spectrumButton--secondary');
            $this->buttonList->update('save', 'label', __('Save pricing rule'));
            $this->buttonList->update('save', 'class', 'spectrumButton');
        } catch (NoSuchEntityException $e) {
            // remove save button
            $this->buttonList->remove('save');
        }
    }

    /**
     * Retrieve text for header element
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Pricing Rule Settings');
    }
}
