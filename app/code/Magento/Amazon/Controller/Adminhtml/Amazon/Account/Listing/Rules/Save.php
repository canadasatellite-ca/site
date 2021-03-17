<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing\Rules;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\ListingRuleInterface;
use Magento\Amazon\Api\ListingRuleRepositoryInterface;
use Magento\Amazon\Controller\Adminhtml\Amazon\Account\Rules;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\Indexer\StockProcessor;
use Magento\Amazon\Service\Account\ChangeAccountStatus;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\CatalogRule\Model\Rule\WebsitesOptionsProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
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
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /** @var StockProcessor $stockProcessor */
    protected $stockProcessor;
    /** @var ListingRuleRepositoryInterface $listingRuleRepository */
    protected $listingRuleRepository;
    /** @var DataPersistorInterface $dataPersistor */
    protected $dataPersistor;
    /** @var Json $serializer */
    protected $serializer;
    /** @var WebsitesOptionsProvider $websitesOptionsProvider */
    protected $websitesOptionsProvider;
    /**
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;
    /**
     * @var ChangeAccountStatus
     */
    private $changeAccountStatus;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AccountRepositoryInterface $accountRepository
     * @param StockProcessor $stockProcessor
     * @param ListingRuleRepositoryInterface $listingRuleRepository
     * @param DataPersistorInterface $dataPersistor
     * @param Json $serializer
     * @param WebsitesOptionsProvider $websitesOptionsProvider
     * @param AscClientLogger $logger
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AccountRepositoryInterface $accountRepository,
        StockProcessor $stockProcessor,
        ListingRuleRepositoryInterface $listingRuleRepository,
        DataPersistorInterface $dataPersistor,
        Json $serializer,
        WebsitesOptionsProvider $websitesOptionsProvider,
        AscClientLogger $logger,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl,
        ChangeAccountStatus $changeAccountStatus
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->accountRepository = $accountRepository;
        $this->stockProcessor = $stockProcessor;
        $this->listingRuleRepository = $listingRuleRepository;
        $this->dataPersistor = $dataPersistor;
        $this->serializer = $serializer;
        $this->websitesOptionsProvider = $websitesOptionsProvider;
        $this->logger = $logger;
        $this->frontendUrl = $frontendUrl;
        $this->changeAccountStatus = $changeAccountStatus;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Save user defined listing rule
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NoSuchEntityException
     * @throws \Magento\Amazon\Model\ApiClient\ApiException
     * @throws \Magento\Amazon\Model\ApiClient\ResponseValidationException
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var array */
        $params = $this->dataPersistor->get('listing_rule_post_data');
        /** @var int */
        $merchantId = $params['merchant_id'] ?? '';

        try {
            /** @var AccountInterface */
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager
                ->addErrorMessage(__('There was an error in saving the listing rule. Please try again.'));
            return $resultRedirect->setPath($this->frontendUrl->getHomeUrl());
        }

        /** @var ListingRuleInterface */
        $rule = $this->createListingRule($account->getMerchantId());

        // save rule
        try {
            $this->listingRuleRepository->save($rule);
        } catch (\Exception $e) {
            $this->messageManager
                ->addErrorMessage(__('There was an error in saving the listing rule. Please try again.'));
            return $resultRedirect->setPath($this->frontendUrl->getStoreDetailsUrl($account));
        }

        /**
         * Rules saved for the first time so we should activate the store as it's now ready for sync
         */
        if (Definitions::ACCOUNT_STATUS_INCOMPLETE === (int)$account->getIsActive()) {
            try {
                $this->changeAccountStatus->activateIncompleteStoreByAccount($account);
            } catch (CouldNotSaveException $e) {
                $this->logger->critical('Exception occurred during saving listing rules', ['exception' => $e]);
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                return $resultRedirect->setPath($this->frontendUrl->getStoreDetailsUrl($account));
            }
        }

        return $resultRedirect->setPath($this->frontendUrl->getStoreDetailsUrl($account));
    }

    /**
     * Builds listing rule object with user inputs
     *
     * @param int $merchantId
     * @return ListingRuleInterface
     * @throws NoSuchEntityException
     */
    private function createListingRule($merchantId): ListingRuleInterface
    {
        /** @var ListingRuleInterface */
        $rule = $this->listingRuleRepository->getByMerchantId($merchantId);
        /** @var array */
        $params = $this->dataPersistor->get('listing_rule_post_data');
        $websiteId = $params['website_id'] ?? 0;
        /** @var array */
        $ruleData = $params['rule'] ?? [];

        $rule->setMerchantId($merchantId);
        $rule->setWebsiteId($websiteId);

        if (isset($ruleData['conditions'])) {
            $arr = $this->convertFlatToRecursive($ruleData);
            $rule->setConditionsSerialized($this->serializer->serialize($arr['conditions'][1]));
        }

        $this->dataPersistor->clear('listing_rule_post_data');
        return $rule;
    }
}
