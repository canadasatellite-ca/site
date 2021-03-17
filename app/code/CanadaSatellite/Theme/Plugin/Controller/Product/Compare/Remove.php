<?php

namespace CanadaSatellite\Theme\Plugin\Controller\Product\Compare;

use Magento\Catalog\Controller\Product\Compare\Remove as CompareRemove;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Store\Model\StoreManagerInterface;
use CanadaSatellite\Theme\Controller\Router;

class Remove
{

    /**
     * @var RedirectFactory
     */
    protected $_resultRedirectFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        RedirectFactory $resultRedirectFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->_resultRedirectFactory = $resultRedirectFactory;
        $this->_storeManager = $storeManager;
    }

    public function afterExecute(CompareRemove $subject, $result)
    {
        $redirectPath = $subject->getRequest()->getPost('urlWithoutItem');

        if (isset($_SESSION['catalog']['is_incognito']) && $_SESSION['catalog']['is_incognito'] == 1) {
            $productId = $subject->getRequest()->getParam('product');
            if (($key = array_search($productId, $_SESSION['catalog']['compare_ids'])) !== false) {
                unset($_SESSION['catalog']['compare_ids'][$key]);
            }
        }

        // @todo add compare items from DB which not in link
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $redirectUrl = $baseUrl . Router::COMPARE_URL_SKU . '/' . $redirectPath;

        $resultRedirect = $this->_resultRedirectFactory->create();

        if ($redirectPath) {
            return $resultRedirect->setUrl($redirectUrl);
        } else {
            return $resultRedirect->setUrl($baseUrl);
        }
    }
}
