<?php

/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magedelight\Subscribenow\Model\ProductSubscribersFactory as SubscribeFactory;
use Magento\Framework\Registry;

abstract class AbstractSubscription extends Action
{

    const ACTIVE_MENU_PATH = 'subscribenow/account/profile';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * CustomerSession
     */
    protected $customerSession;

    /**
     * @var SubscriberFactory
     */
    protected $subscribeFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CustomerSession $customerSession
     * @param SubscribeFactory $subscribeFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomerSession $customerSession,
        SubscribeFactory $subscribeFactory,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->subscribeFactory = $subscribeFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    public function init()
    {
        $id = $this->getRequest()->getParam('id');
        $subscriptionModel = $this->subscribeFactory->create()->load($id);
        $this->registry->register('current_profile', $subscriptionModel);
        return $subscriptionModel;
    }

    /**
     * Check weather customer is logged in or not
     */
    public function validateCustomer()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->customerSession->setAfterAuthUrl($this->_url->getCurrentUrl());
            $this->customerSession->authenticate();
        }
        return $this;
    }
}
