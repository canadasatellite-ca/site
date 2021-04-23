<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageSuper\Casat\Block\Adminhtml\Sales\Order\View\Items;

use Magento\Backend\Block\Template;

class BundleRenderer
{
    /**
     * @param Template $originalBlock
     */
    function beforeToHtml(Template $originalBlock)
    {
        $originalBlock->setTemplate('MageSuper_Casat::order/view/items/renderer/bundle.phtml');
    }
}
