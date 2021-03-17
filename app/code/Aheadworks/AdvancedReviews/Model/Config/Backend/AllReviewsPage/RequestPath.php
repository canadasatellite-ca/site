<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Config\Backend\AllReviewsPage;

use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Model\ScopeInterface;
use Magento\UrlRewrite\Helper\UrlRewrite as UrlRewriteHelper;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Model\Url\Rewrite\Service\CustomEntity as UrlRewriteCustomEntityService;
use Magento\Store\Model\Website;
use Aheadworks\AdvancedReviews\Model\Config;

/**
 * Class RequestPath
 *
 * @package Aheadworks\AdvancedReviews\Model\Config\Backend\AllReviewsPage
 */
class RequestPath extends ConfigValue
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlRewriteHelper
     */
    private $urlRewriteHelper;

    /**
     * @var UrlRewriteCustomEntityService
     */
    private $urlRewriteCustomEntityService;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param StoreManagerInterface $storeManager
     * @param UrlRewriteHelper $urlRewriteHelper
     * @param UrlRewriteCustomEntityService $urlRewriteCustomEntityService
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        StoreManagerInterface $storeManager,
        UrlRewriteHelper $urlRewriteHelper,
        UrlRewriteCustomEntityService $urlRewriteCustomEntityService,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
        $this->storeManager = $storeManager;
        $this->urlRewriteHelper = $urlRewriteHelper;
        $this->urlRewriteCustomEntityService = $urlRewriteCustomEntityService;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $this->urlRewriteHelper->validateRequestPath($this->getValue());
        return parent::beforeSave();
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $newRequestPath = $this->getValue();
            $oldRequestPath = $this->getOldValue();
            $storeIds = $this->getStoreIds();

            $this->urlRewriteCustomEntityService->deleteRewrites($storeIds, $oldRequestPath);
            $this->urlRewriteCustomEntityService->addRewrites(
                $storeIds,
                $newRequestPath,
                Config::ALL_REVIEWS_PAGE_CANONICAL_URL_PATH
            );
        }
        return parent::afterSave();
    }

    /**
     * @return array
     */
    protected function getStoreIds()
    {
        try {
            if ($this->getScope() == 'stores') {
                $storeIds = [$this->getScopeId()];
            } elseif ($this->getScope() == 'websites') {
                /** @var Website $website */
                $website = $this->storeManager->getWebsite($this->getScopeId());
                $storeIds = array_keys($website->getStoreIds());
                $storeIds = array_diff($storeIds, $this->getOverrideStoreIds($storeIds));
            } else {
                $storeIds = array_keys($this->storeManager->getStores());
                $storeIds = array_diff($storeIds, $this->getOverrideStoreIds($storeIds));
            }
        } catch (LocalizedException $exception) {
            $storeIds = [];
        }
        return array_values($storeIds);
    }

    /**
     * @param array $storeIds
     * @return array
     */
    protected function getOverrideStoreIds($storeIds)
    {
        $excludeIds = [];
        foreach ($storeIds as $storeId) {
            $requestPath = $this->_config->getValue($this->getPath(), ScopeInterface::SCOPE_STORE, $storeId);
            if ($requestPath != $this->getOldValue()) {
                $excludeIds[] = $storeId;
            }
        }
        return $excludeIds;
    }
}
