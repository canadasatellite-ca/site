<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageSuper\Casat\Plugin\Sales\Block\Adminhtml\Order\View\Items;

use Magento\Backend\Block\Template;

class DefaultRenderer
{
    /**
     * @param Template $originalBlock
     * @param $after
     * @return array
     */
    function afterGetColumns(Template $originalBlock, $after)
    {
        $after = $after + ['profit' => "col-profit",'margin'=>'col-margin'] ;
        return $after;
    }
}
