<?php

namespace CanadaSatellite\DynamicsIntegration\Controller\Page;

class View extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;

    function __construct(
    	\Magento\Framework\App\Action\Context $context,
    	\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory)
	{
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = ['message' => 'test'];
        return $result->setData($data);
    }
}