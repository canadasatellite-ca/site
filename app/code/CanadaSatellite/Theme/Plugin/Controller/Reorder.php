<?php

namespace CanadaSatellite\Theme\Plugin\Controller;

use Magento\Sales\Controller\AbstractController\Reorder as AbstractReorder;
use Magento\Framework\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;

class Reorder
{
    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    protected $orderLoader;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param Registry $registry
     */
    function __construct(
        OrderLoaderInterface $orderLoader,
        Registry $registry,
        RequestInterface $request,
        RedirectFactory $resultRedirectFactory,
        ObjectManagerInterface $objectManager,
        ManagerInterface $messageManager
    ) {
        $this->orderLoader = $orderLoader;
        $this->_coreRegistry = $registry;
        $this->_request = $request;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_objectManager = $objectManager;
        $this->_messageManager = $messageManager;
    }

    function aroundExecute(AbstractReorder $subject, callable $proceed)
    {
        $result = $this->orderLoader->load($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }
        $order = $this->_coreRegistry->registry('current_order');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        /* @var $cart \Magento\Checkout\Model\Cart */
        $cart = $this->_objectManager->get(\Magento\Checkout\Model\Cart::class);
        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $cart->addOrderItem($item);
                $cart->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_objectManager->get(\Magento\Checkout\Model\Session::class)->getUseNotice(true)) {
                    $this->_messageManager->addNotice($e->getMessage());
                } else {
                    $this->_messageManager->addError($e->getMessage());
                }
                return $resultRedirect->setUrl($this->_getPathRedirectIssuedProduct($item));
            } catch (\Exception $e) {
                $this->_messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
                return $resultRedirect->setUrl($this->_getPathRedirectIssuedProduct($item));
            }
        }
        return $resultRedirect->setPath('checkout/cart');
    }

    function _getPathRedirectIssuedProduct ($item){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
        return $product->getProductUrl();
    }
}