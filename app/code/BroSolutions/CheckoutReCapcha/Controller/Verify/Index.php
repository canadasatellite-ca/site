<?php

namespace BroSolutions\CheckoutReCapcha\Controller\Verify;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    const GRECAPTCHA_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    protected $_pageFactory;

    private $curl;

    public $jsonResultFactory;

    public $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_pageFactory = $pageFactory;
        $this->curl = $curl;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->scopeConfig = $scopeConfig;
        return parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->jsonResultFactory->create();
        if (!$this->getRequest()->getParam('token') || !$this->getRequest()->isAjax()) {
            return $response->setData(['status' => false]);
        }

        $this->curl->post(
            self::GRECAPTCHA_VERIFY_URL,
            [
                'secret' => $this->scopeConfig->getValue('msp_securitysuite_recaptcha/general_V3/private_key_v3'),
                'response' => $this->getRequest()->getParam('token')
            ]
        );

        $result = json_decode($this->curl->getBody(), true);
        if ($result['success'] == true) {
            return $response->setData(['status' => true]);
        }

        return $response->setData([$result]);
    }
}
