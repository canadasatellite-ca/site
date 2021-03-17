<?php
/**
 * Magedelight
 * Copyright (C) 2018 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2018 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Plugin;

use Magedelight\Subscribenow\Model\Subscription;

class ProductPlugin
{

    private $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Set Final Price Product Detail
     *
     * @param object $product
     * @param object $result
     *
     * @return float
     */
    public function afterGetFinalPrice($product, $result)
    {
        $result = $this->subscription->getFinalPrice($product, $result);
        return $result;
    }
}
