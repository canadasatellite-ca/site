<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Rules;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;

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
        $this->_controller = 'adminhtml_amazon_account_listing_rules';
        $this->_mode = 'view';
        $this->_blockGroup = 'Magento_Amazon';
        $this->setData('id', 'channel_amazon_account_listing_rules_index');

        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');

        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');

        $this->buttonList->remove('save');
        $this->buttonList->remove('back');

        /** @var AccountInterface */
        $account = $this->accountRepository->getByMerchantId($merchantId);
    }
}
