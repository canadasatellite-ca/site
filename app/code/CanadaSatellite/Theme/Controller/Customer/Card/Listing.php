<?php

namespace CanadaSatellite\Theme\Controller\Customer\Card;

use Magento\Framework\App\RequestInterface;

class Listing extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Customer session.
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;

        parent::__construct($context);
    }

    public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        return parent::dispatch($request);
    }
    
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }

    protected function _getSession()
    {
        return $this->_customerSession;
    }
}
