<?php

namespace CanadaSatellite\DynamicsIntegration\Controller\Page;

class View extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $restApi;

    function __construct(
    	\Magento\Framework\App\Action\Context $context,
    	\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi)
	{
        $this->resultJsonFactory = $resultJsonFactory;
        $this->restApi = $restApi;
        parent::__construct($context);
    }

    function execute()
    {
        $resJson = $this->restApi->getSim("{09ABB362-8A19-E411-B4DB-6C3BE5A8D268}");
        //$resJson = $this->restApi->getDevicesByCustomerId(4032);
        //$resJson = $this->restApi->getSimsByCustomerId(13769);
        $result = $this->resultJsonFactory->create();
        $data = ['message' => $resJson];
        return $result->setData($data);
    }
}