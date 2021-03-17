<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Controller\Postoffice;

class GetList extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Mageside\CanadaPostShipping\Model\Service\PostofficeFactory
     */
    protected $_postOfficeServiceFactory;

    /**
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    protected $_carrierHelper;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * Post constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Mageside\CanadaPostShipping\Model\Service\PostofficeFactory $postOfficeServiceFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Mageside\CanadaPostShipping\Model\Service\PostofficeFactory $postOfficeServiceFactory,
        \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_postOfficeServiceFactory = $postOfficeServiceFactory;
        $this->_carrierHelper = $carrierHelper;
        $this->_layoutFactory = $layoutFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('/');
        }

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Invalid form key.'));
            return $this->getResult(['error' => true]);
        }

        $postCode = $this->getRequest()->getParam('post_code');
        if (!$postCode) {
            $this->messageManager->addErrorMessage(__('Invalid post code.'));
            return $this->getResult(['error' => true]);
        }

        /** @var \Mageside\CanadaPostShipping\Model\Service\Postoffice $service */
        $service = $this->_postOfficeServiceFactory->create();
        $offices = $service->getNearestPostOffice($postCode);

        return $this->getResult($offices);
    }

    /**
     * @param $data
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function getResult($data)
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        if (!empty($data['messages'])) {
            foreach ($data['messages'] as $message) {
                $this->messageManager->addErrorMessage(__($message['message']));
            }
        }

        /** @var $block \Magento\Framework\View\Element\Messages */
        $block = $this->_layoutFactory->create()->getMessagesBlock();
        $block->setMessages($this->messageManager->getMessages(true));
        $messages = $block->getGroupedHtml();
        $data['messages'] = $messages;

        return $resultJson->setData($data);
    }
}
