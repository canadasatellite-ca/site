<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Pricing\Rules;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\PricingRuleInterface;
use Magento\Amazon\Api\Data\PricingRuleInterfaceFactory;
use Magento\Amazon\Api\PricingRuleRepositoryInterface;
use Magento\Amazon\Controller\Adminhtml\Amazon\Account\Rules;
use Magento\Amazon\Model\Indexer\PricingProcessor;
use Magento\Amazon\Ui\AdminStorePageUrl;
use Magento\Amazon\Ui\FrontendUrl;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Save
 */
class Save extends Rules
{
    /** @var PageFactory */
    protected $resultPageFactory;
    /** @var PricingProcessor $pricingProcessor */
    protected $pricingProcessor;
    /** @var PricingRuleRepositoryInterface $pricingRuleRepository */
    protected $pricingRuleRepository;
    /** @var PricingRuleInterfaceFactory $pricingRuleFactory */
    protected $pricingRuleFactory;
    /** @var Json $serializer */
    protected $serializer;
    /**
     * @var AdminStorePageUrl
     */
    private $adminStorePageUrl;
    /**
     * @var FrontendUrl
     */
    private $frontendUrl;
    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PricingProcessor $pricingProcessor
     * @param PricingRuleRepositoryInterface $pricingRuleRepository
     * @param PricingRuleInterfaceFactory $pricingRuleFactory
     * @param Json $serializer
     * @param AdminStorePageUrl $adminStorePageUrl
     * @param FrontendUrl $frontendUrl
     * @param AccountRepositoryInterface $accountRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PricingProcessor $pricingProcessor,
        PricingRuleRepositoryInterface $pricingRuleRepository,
        PricingRuleInterfaceFactory $pricingRuleFactory,
        Json $serializer,
        AdminStorePageUrl $adminStorePageUrl,
        FrontendUrl $frontendUrl,
        AccountRepositoryInterface $accountRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->pricingProcessor = $pricingProcessor;
        $this->pricingRuleRepository = $pricingRuleRepository;
        $this->pricingRuleFactory = $pricingRuleFactory;
        $this->serializer = $serializer;
        $this->adminStorePageUrl = $adminStorePageUrl;
        $this->frontendUrl = $frontendUrl;
        $this->accountRepository = $accountRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Save pricing rule
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var string */
        if (!$merchantId = $this->getRequest()->getParam('merchant_id')) {
            $this->messageManager->addErrorMessage(
                __('There was an error in saving the pricing rule. Please try again.')
            );
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        try {
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Cannot save the pricing rule: merchant does not exist.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var PricingRuleInterface */
        $rule = $this->processRuleSettings($merchantId);
        $rule = $this->processRuleConditions($rule);
        $rule = $this->processRuleActions($rule);
        $this->invalidateIndexer();

        try {
            $this->pricingRuleRepository->save($rule);
            $this->messageManager->addSuccessMessage(__('The pricing rule has been successfully saved.'));
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(
                __('There was an error in saving the pricing rule. Please try again.')
            );
        }

        return $resultRedirect->setPath($this->adminStorePageUrl->pricingRulesCreate($account, $rule));
    }

    /**
     * Captures and formats pricing rule settings
     *
     * @param int $merchantId
     * @return PricingRuleInterface
     */
    private function processRuleSettings($merchantId)
    {
        try {
            $rule = $this->pricingRuleRepository->getById($this->getRequest()->getParam('id'));
        } catch (NoSuchEntityException $e) {
            $rule = $this->pricingRuleFactory->create();
        }

        // add price rule data
        $rule->setMerchantId($merchantId);
        $rule->setName($this->getRequest()->getParam('name'));
        $rule->setDescription($this->getRequest()->getParam('description'));
        $rule->setIsActive($this->getRequest()->getParam('is_active'));
        $rule->setStopRulesProcessing($this->getRequest()->getParam('stop_rules_processing'));
        $rule->setSortOrder($this->getRequest()->getParam('sort_order'));
        $rule->setAuto($this->getRequest()->getParam('auto'));
        $rule->setAutoSource($this->getRequest()->getParam('auto_source'));
        $rule->setAutoMinimumFeedback($this->getRequest()->getParam('auto_minimum_feedback'));
        $rule->setAutoFeedbackCount($this->getRequest()->getParam('auto_feedback_count'));
        $rule->setAutoCondition($this->getRequest()->getParam('auto_condition'));
        $rule->setNewVariance($this->getRequest()->getParam('new_variance'));
        $rule->setRefurbishedVariance($this->getRequest()->getParam('refurbished_variance'));
        $rule->setUsedlikenewVariance($this->getRequest()->getParam('usedlikenew_variance'));
        $rule->setUsedVerygoodVariance($this->getRequest()->getParam('usedverygood_variance'));
        $rule->setUsedgoodVariance($this->getRequest()->getParam('usedgood_variance'));
        $rule->setUsedacceptableVariance($this->getRequest()->getParam('usedacceptable_variance'));
        $rule->setCollectiblelikenewVariance($this->getRequest()->getParam('collectiblelikenew_variance'));
        $rule->setCollectibleverygoodVariance($this->getRequest()->getParam('collectibleverygood_variance'));
        $rule->setCollectiblegoodVariance($this->getRequest()->getParam('collectiblegood_variance'));
        $rule->setCollectibleacceptableVariance($this->getRequest()->getParam('collectibleacceptable_variance'));
        $rule->setFloor($this->getRequest()->getParam('floor'));
        $rule->setFloorPriceMovement($this->getRequest()->getParam('floor_price_movement'));
        $rule->setFloorSimpleAction($this->getRequest()->getParam('floor_simple_action'));
        $rule->setFloorDiscountAmount($this->getRequest()->getParam('floor_discount_amount'));
        $rule->setCeiling($this->getRequest()->getParam('ceiling'));
        $rule->setCeilingPriceMovement($this->getRequest()->getParam('ceiling_price_movement'));
        $rule->setCeilingSimpleAction($this->getRequest()->getParam('ceiling_simple_action'));
        $rule->setCeilingDiscountAmount($this->getRequest()->getParam('ceiling_discount_amount'));

        // set to and from date (if applicable)
        $fromDate = ($this->getRequest()->getParam('from_date')) ? $this->getRequest()->getParam('from_date') : null;
        $toDate = ($this->getRequest()->getParam('to_date')) ? $this->getRequest()->getParam('to_date') : null;
        $rule->setFromDate($fromDate);
        $rule->setToDate($toDate);

        return $rule;
    }

    /**
     * Captures and formats pricing rule conditions
     *
     * @param PricingRuleInterface $rule
     * @return PricingRuleInterface
     */
    private function processRuleConditions($rule)
    {
        /** @var array */
        $data = $this->getRequest()->getParams();

        $data['conditions'] = $data['rule']['conditions'];
        $arr = $this->convertFlatToRecursive($data);

        $rule->setConditionsSerialized($this->serializer->serialize($arr['conditions'][1]));

        return $rule;
    }

    /**
     * Captures and formats pricing rule actions
     *
     * @param PricingRuleInterface $rule
     * @return PricingRuleInterface
     */
    private function processRuleActions($rule)
    {
        // intelligent repricing rule
        if ($rule->getAuto()) {
            $rule->setPriceMovement($this->getRequest()->getParam('price_movement_two'));
            $rule->setSimpleAction($this->getRequest()->getParam('simple_action_two'));
            $rule->setDiscountAmount($this->getRequest()->getParam('discount_amount_two'));
        } else { // standard catalog price rule
            $rule->setPriceMovement($this->getRequest()->getParam('price_movement_one'));
            $rule->setSimpleAction($this->getRequest()->getParam('simple_action_one'));
            $rule->setDiscountAmount($this->getRequest()->getParam('discount_amount_one'));
        }

        return $rule;
    }

    /**
     * Invalidates pricing indexer
     *
     * @return void
     */
    private function invalidateIndexer()
    {
        $this->pricingProcessor->updateMode();
        $this->pricingProcessor->getIndexer()->invalidate();
    }
}
