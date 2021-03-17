<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\System\Config\License;

/**
 * Class Check
 * @package Cart2Quote\Quotation\Controller\Adminhtml\System\Config\License
 */
class Check extends \Magento\Backend\App\Action
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data\Metadata
     */
    private $metadata;

    /**
     * Check constructor.
     * @param \Cart2Quote\Quotation\Helper\Data\Metadata $metadata
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data\Metadata $metadata,
        \Magento\Backend\App\Action\Context $context

    ) {
        parent::__construct($context);
        $this->metadata = $metadata;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('orderId');
        if (\class_exists(\Cart2Quote\License\Model\License::class)) {
            $license = \Cart2Quote\License\Model\License::getInstance();
            $this->metadata->setOrderId($orderId);
            $license->reload();
        }

        return;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}