<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AutoRelated
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AutoRelated\Controller\Adminhtml\Grid;

use Mageplaza\AutoRelated\Controller\Adminhtml\Rule;

/**
 * Class ProductList
 * @package Mageplaza\AutoRelated\Controller\Adminhtml\Grid
 */
class ProductList extends Rule
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    function execute()
    {
        try {
            /** @var \Magento\Framework\View\Layout $layout */
            $layout   = $this->_view->getLayout();
            $block    = $layout->createBlock('Mageplaza\AutoRelated\Block\Adminhtml\Rule\Edit\Tab\Grid');
            $response = $block->toHtml();
        } catch (\Exception $exception) {
            $response = __('An error occurred');
            $this->logger->critical($exception);
        }

        return $this->_response->setBody($response);
    }
}
