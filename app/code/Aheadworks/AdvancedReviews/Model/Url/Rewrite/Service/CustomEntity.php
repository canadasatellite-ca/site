<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Url\Rewrite\Service;

use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteData;
use Magento\UrlRewrite\Model\UrlRewrite;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Psr\Log\LoggerInterface;

/**
 * Class CustomEntity
 *
 * @package Aheadworks\AdvancedReviews\Model\Url\Rewrite\Service
 */
class CustomEntity
{
    /**
     * Entity type for url rewrites
     */
    const URL_REWRITE_ENTITY_TYPE = Rewrite::ENTITY_TYPE_CUSTOM;

    /**
     * Redirect type
     */
    const REDIRECT_TYPE = 0;

    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;

    /**
     * @var UrlRewriteFactory
     */
    private $urlRewriteFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param UrlPersistInterface $urlPersist
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        UrlRewriteFactory $urlRewriteFactory,
        LoggerInterface $logger
    ) {
        $this->urlPersist = $urlPersist;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->logger = $logger;
    }

    /**
     * Delete custom rewrites for specific stores of definite request path
     *
     * @param int[] $storeIds
     * @param string $requestPath
     * @return void
     */
    public function deleteRewrites($storeIds, $requestPath)
    {
        if (!empty($storeIds) && (!empty($requestPath))) {
            $this->urlPersist->deleteByData(
                [
                    UrlRewriteData::ENTITY_TYPE => self::URL_REWRITE_ENTITY_TYPE,
                    UrlRewriteData::STORE_ID => $storeIds,
                    UrlRewriteData::REQUEST_PATH => $requestPath,
                ]
            );
        }
    }

    /**
     * Add custom rewrites for specific stores from definite request path to target path
     *
     * @param int[] $storeIds
     * @param string $requestPath
     * @param string $targetPath
     * @return void
     */
    public function addRewrites($storeIds, $requestPath, $targetPath)
    {
        if (!empty($requestPath) && !empty($targetPath)) {
            foreach ($storeIds as $storeId) {
                try {
                    /** @var UrlRewrite $urlRewrite */
                    $urlRewrite = $this->urlRewriteFactory->create();
                    $urlRewrite->setStoreId($storeId)
                        ->setEntityType(self::URL_REWRITE_ENTITY_TYPE)
                        ->setRequestPath($requestPath)
                        ->setTargetPath($targetPath)
                        ->setRedirectType(self::REDIRECT_TYPE);
                    $urlRewrite->save();
                } catch (\Exception $exception) {
                    $this->logger->warning($exception->getMessage());
                }
            }
        }
    }
}
