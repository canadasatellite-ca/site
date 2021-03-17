<?php

namespace BroSolutions\CryptoCoins\Controller\Crypto;

/**
 * Class Index
 * @package BroSolutions\CryptoCoins\Controller\Crypto
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    public $curlFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $jsonFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        $this->curlFactory = $curlFactory;
        $this->jsonFactory = $jsonFactory;
        return parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            return $this->_redirect($this->_url->getUrl('noroute'));
        }

        $curl = $this->curlFactory->create();
        $result = $this->resultFactory->create('json');
        try {
            $curl->get('https://api.binance.com/api/v3/ticker/24hr');
            return $result->setData(['result' => json_decode($curl->getBody())]);
        } catch (\Exception $exception) {
            return $result->setData(['result' => 'error']);
        }
    }
}
