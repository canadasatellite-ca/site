<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Plugin\Catalog\Model\Indexer\Category\Product\Action;

class Full extends \Magento\Catalog\Model\Indexer\Category\Product\Action\Full
{
    function isRangingNeeded() {
        return false;
    }
}
