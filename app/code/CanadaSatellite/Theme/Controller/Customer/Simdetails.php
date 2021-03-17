<?php

namespace CanadaSatellite\Theme\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Registry;

class Simdetails extends \Magedelight\Firstdata\Controller\Firstdata
{
    /**
     * The SIM Factory
     *
     * @var \CanadaSatellite\Theme\Model\SimFactory
     */
    protected $_simFactory;

    /**
     * @var RedirectFactory
     */
    protected $_redirectFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;


    public function __construct(Context $context,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                \Magento\Framework\Registry $registry,
                                \Magento\Customer\Model\Session $customerSession,
                                \Magento\Framework\DataObject $requestObject,
                                \Magedelight\Firstdata\Model\Api\Soap $soapModel,
                                \Magedelight\Firstdata\Model\CardsFactory $cardFactory,
                                \CanadaSatellite\Theme\Model\SimFactory $simFactory,
                                \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
                                \Magento\Framework\UrlInterface $url
    ) {
        $this->_simFactory = $simFactory;
        $this->_redirectFactory = $redirectFactory;
        $this->_url = $url;
        return parent::__construct($context, $resultPageFactory, $registry, $customerSession, $requestObject,
            $soapModel, $cardFactory);
    }

    protected function _getSession()
    {
        return $this->_customerSession;
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
        $simId = $this->_request->getParam('id');
        if (!$simId) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->_redirectFactory->create();
            return $resultRedirect->setUrl($this->_url->getUrl('*/*/viewsim'));
        }

        /** @var \CanadaSatellite\Theme\Model\Sim $sim */
        $sim = $this->_simFactory->create()->load($simId);
        $customerId = $this->_customerSession->getCustomerId();
        if ($sim->getId()
            && $sim->getMagentoCustomerId()
                // confirm that the sim is for this customer, that customer didn't guess url of another customer's sim
            && $sim->getMagentoCustomerId() == $customerId
        ) {
            // Save SIM data into the registry. To pass it to Block\Customer\Sim\View.
            $this->registry->register('current_sim', $sim);
        } else {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->_redirectFactory->create();
            return $resultRedirect->setUrl($this->_url->getUrl('*/*/viewsim'));
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

//        /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
//        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
//        if ($navigationBlock) {
//            $navigationBlock->setActive('sales/order/history');
//        }
        return $resultPage;
    }
}