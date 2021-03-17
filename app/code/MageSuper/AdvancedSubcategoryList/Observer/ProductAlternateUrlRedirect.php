<?php

namespace MageSuper\AdvancedSubcategoryList\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ProductAlternateUrlRedirect implements ObserverInterface
{
    protected $_responseFactory;
    protected $_url;

    public function __construct(

        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url

    )
    {
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
    }

    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $product = $observer->getProduct();
        $alternate_url = $product->getAlternateUrl();
        if ($alternate_url != '') {
            $this->_responseFactory->create()->setRedirect($alternate_url)->sendResponse();
            die();
            //return $alternate_url;
        }
    }
}