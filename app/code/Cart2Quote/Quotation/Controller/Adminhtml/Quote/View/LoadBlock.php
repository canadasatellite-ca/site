<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote\View;

/**
 * Class LoadBlock
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote\View
 */
class LoadBlock extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote\View
{
    /**
     * Loading page block
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $request = $this->getRequest();
        try {
            $this->_initQuote();
            $this->_initSession()->_processData();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, $e->getMessage());
        }
        $this->_reloadQuote();
        $asJson = $request->getParam('json');
        $block = $request->getParam('block');

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        if ($asJson) {
            $resultPage->addHandle('quotation_quote_view_load_block_json');
        } else {
            $resultPage->addHandle('quotation_quote_view_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $resultPage->addHandle('quotation_quote_view_load_block_' . $block);
            }
        }

        $result = $resultPage->getLayout()->renderElement('content');
        if ($request->getParam('as_js_varname')) {
            $this->_session->setUpdateResult($result);

            return $this->resultRedirectFactory->create()->setPath('quotation/*/showUpdateResult');
        }

        return $this->resultRawFactory->create()->setContents($result);
    }
}
