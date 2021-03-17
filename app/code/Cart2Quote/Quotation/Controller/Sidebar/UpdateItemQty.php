<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Sidebar;

/**
 * Class UpdateItemQty
 * @package Cart2Quote\Quotation\Controller\Sidebar
 */
class UpdateItemQty extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Cart2Quote\Quotation\Model\MiniCart\Sidebar
     */
    private $sidebar;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * UpdateItemQty constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Cart2Quote\Quotation\Model\MiniCart\Sidebar $sidebar
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Cart2Quote\Quotation\Model\MiniCart\Sidebar $sidebar,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper,
        \Psr\Log\LoggerInterface $logger

    ) {
        parent::__construct($context);
        $this->sidebar = $sidebar;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\App\Response\Http|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $itemId = (int)$this->getRequest()->getParam('item_id');
        $itemQty = (int)$this->getRequest()->getParam('item_qty') * 1;
        try {
            $item = $this->sidebar->updateQuoteItem($itemId, $itemQty);

            $this->_eventManager->dispatch(
                'quotation_quote_update_item_complete',
                ['item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            return $this->jsonResponse();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Compile JSON response
     *
     * @param string $error
     * @return \Magento\Framework\App\Response\Http
     */
    protected function jsonResponse($error = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->serialize($this->sidebar->getResponseData($error))
        );
    }
}
