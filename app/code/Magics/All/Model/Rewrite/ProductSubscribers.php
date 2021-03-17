<?php

namespace Magics\All\Model\Rewrite;

/**
 * Class ProductSubscribers
 * @package Magics\All\Model\Rewrite
 */
class ProductSubscribers extends \Magedelight\Subscribenow\Model\ProductSubscribers
{
    protected function _beforeLoad($modelId, $field = null)
    {

        return parent::_beforeLoad($modelId);
    }
}