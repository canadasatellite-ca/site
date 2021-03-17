<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote\View;

/**
 * Class ShowUpdateResult
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote\View
 */
class ShowUpdateResult extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote\View
{
    /**
     * Show item update result from loadBlockAction
     * - to prevent popup alert with resend data question
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $session = $this->_session;
        if ($session->hasUpdateResult() && is_scalar($session->getUpdateResult())) {
            $resultRaw->setContents($session->getUpdateResult());
        }
        $session->unsUpdateResult();

        return $resultRaw;
    }
}
