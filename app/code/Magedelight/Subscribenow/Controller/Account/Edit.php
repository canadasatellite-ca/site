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
use Magento\Framework\App\Action\Action;
use Magedelight\Subscribenow\Model\ProductSubscribersFactory as SubscribeFactory;
use Magedelight\Subscribenow\Model\ProductSubscriptionHistory;

class Edit extends Action
{
    
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var SubscribeFactory
     */
    private $subscribersFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param SubscribeFactory $subscribersFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        SubscribeFactory $subscribersFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->subscribersFactory = $subscribersFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('id');
        $model = $this->subscribersFactory->create()->load($id);
        try {
            $model->updateSubscription($this->getRequest()->getParams(), ProductSubscriptionHistory::HISTORY_BY_CUSTOMER);
            $this->messageManager->addSuccessMessage(__('Subscription profile has been successfully updated.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Item is not updating'));
        }
        return $resultRedirect->setPath('*/*/view', ['id' => $id]);
    }
}
