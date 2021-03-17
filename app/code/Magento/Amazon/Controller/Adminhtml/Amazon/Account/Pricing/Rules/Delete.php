<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Pricing\Rules;

use Magento\Amazon\Api\PricingRuleRepositoryInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Class Delete
 */
class Delete extends Action
{
    /** @var PricingRuleRepositoryInterface $pricingRuleRepository */
    protected $pricingRuleRepository;
    /**
     * @var AscClientLogger
     */
    private $logger;

    /**
     * @param Action\Context $context
     * @param PricingRuleRepositoryInterface $pricingRuleRepository
     * @param AscClientLogger $logger
     */
    public function __construct(
        Action\Context $context,
        PricingRuleRepositoryInterface $pricingRuleRepository,
        AscClientLogger $logger
    ) {
        parent::__construct($context);
        $this->pricingRuleRepository = $pricingRuleRepository;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Deletes existing pricing rule
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @var int */
        $id = $this->getRequest()->getParam('id');

        try {
            $this->pricingRuleRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('You have successfully deleted the pricing rule.'));
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->critical('Exception occurred during deleting pricing rule', ['exception' => $e]);
        }

        return $resultRedirect->setPath('channel/amazon/account_pricing_rules_index', ['merchant_id' => $merchantId]);
    }
}
