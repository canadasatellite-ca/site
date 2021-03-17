<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Backend\Controller\Adminhtml\Dashboard;

/**
 * Class AjaxBlockPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Backend\Controller\Adminhtml\Dashboard
 */
class AjaxBlockPlugin
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * AjaxBlockPlugin constructor
     *
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @param \Magento\Backend\Controller\Adminhtml\Dashboard\AjaxBlock $subject
     * @param callable $proceed
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function aroundExecute(
        \Magento\Backend\Controller\Adminhtml\Dashboard\AjaxBlock $subject,
        callable $proceed
    ) {
        $blockTab = $subject->getRequest()->getParam('block');
        if ($blockTab != 'tab_quotes') {
            return $proceed();
        }

        $output = '';
        $blockClassSuffix = str_replace(
            ' ',
            '\\',
            ucwords(str_replace('_', ' ', $blockTab))
        );

        if (in_array($blockTab, ['tab_quotes'])) {
            $output = $this->layoutFactory->create()
                ->createBlock('Cart2Quote\\Quotation\\Block\\Adminhtml\\Dashboard\\' . $blockClassSuffix)
                ->toHtml();
        }

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($output);
    }
}
