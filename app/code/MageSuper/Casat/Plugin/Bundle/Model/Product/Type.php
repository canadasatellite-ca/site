<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageSuper\Casat\Plugin\Bundle\Model\Product;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Bundle Price Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Type
{
    protected $registry;
    protected $request;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->request = $request;
        $this->registry = $registry;
    }

    public function beforeGetOptionsCollection(
        \Magento\Bundle\Model\Product\Type $subject,
        $product)
    {
        $module = $this->request->getControllerModule();
        if ($module == 'MW_Onestepcheckout') {
            $product->unsetData('_cache_instance_options_collection');
        }
    }
}
