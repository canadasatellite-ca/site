<?php

namespace CanadaSatellite\Theme\Plugin\Block;

use MageWorx\Downloads\Block\Catalog\Product\Link;
use Magento\UrlRewrite\Model\UrlRewrite;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

class ProductLink
{

    /**
     * @var UrlRewriteFactory
     */
    protected $_urlRewriteFactory;

    /**
     * @var UrlRewriteCollectionFactory
     */
    protected $_urlRewriteCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    function __construct(
        UrlRewriteFactory $urlRewriteFactory,
        StoreManagerInterface $storeManager,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory
    ) {
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_storeManager = $storeManager;
        $this->_urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
    }

    function aroundGetAttachmentLink(
        Link $subject,
        callable $proceed,
        $attachmentId,
        $fileName)
    {
        $targetRoute = 'mwdownloads/download/link/id/' . $attachmentId;
        $requestRoute = $this->_reformatFileName($fileName);
        $currentUrl = $subject->getBaseUrl();
        $requestPath = $currentUrl . $requestRoute;
        $originalStoreId = $this->_storeManager->getStore()->getId();

        /** @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewriteCollection */
        $urlRewriteCollection = $this->_urlRewriteCollectionFactory->create();
        $urlRewriteCollection
            ->addFieldToFilter('request_path',$requestRoute)
            ->addFieldToFilter('store_id',$originalStoreId)
            ->setPageSize(1)
            ->setCurPage(1);

        if (!$urlRewriteCollection->getFirstItem()->getId()) {
            $this->_saveNewRewriteUrl($targetRoute, $requestRoute, $originalStoreId);
        }

        return $requestPath;
    }

    function _saveNewRewriteUrl($targetRoute, $requestRoute, $originalStoreId)
    {
        /** @var UrlRewrite $urlRewriteModel */
        $urlRewriteModel = $this->_urlRewriteFactory->create();
        $urlRewriteModel->setStoreId($originalStoreId);
        $urlRewriteModel->setIsSystem(0);
        $urlRewriteModel->setTargetPath($targetRoute);
        $urlRewriteModel->setRequestPath($requestRoute);
        $urlRewriteModel->save();
    }

    function _reformatFileName($fileName)
    {
        $reformatFileName = strtolower(trim($fileName));
        $reformatFileName = substr ($reformatFileName,5);
        $pos = strripos($reformatFileName, '.');
        $reformatFileName = substr_replace( $reformatFileName, '-', $pos, 1);

        return $reformatFileName;
    }

}