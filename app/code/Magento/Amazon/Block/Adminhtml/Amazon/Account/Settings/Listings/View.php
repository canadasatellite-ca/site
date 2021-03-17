<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Settings\Listings;

use Magento\Amazon\Api\AccountRepositoryInterface;
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
        $this->_controller = 'adminhtml_amazon_account_settings_listings';
        $this->_mode = 'view';
        $this->_blockGroup = 'Magento_Amazon';
        $this->setData('id', 'channel_amazon_account_settings_listings_index');

        $this->buttonList->remove('delete');

        $this->buttonList->remove('save');
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
    }
}
