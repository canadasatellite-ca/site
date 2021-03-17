<?php
/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Cart2Quote
 * @package     Desk
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Desk\Helper\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection helper
 */
class Collection extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Sets array(label => data, value => data)
     * to array(labelData, valueData)
     *
     * @param AbstractCollection $collection
     * @return array
     */
    public function toGridOptionArray(AbstractCollection $collection)
    {
        $options = $collection->toOptionArray();
        $newOptions = [];

        foreach ($options as $option) {
            $newOptions[$option['value']] = ucfirst($option['label']);
        }
        return $newOptions;
    }

    /**
     * Upper case the toOptionArray values
     *
     * @param AbstractCollection $collection
     * @return array
     */
    public function ucfirstToOptionArray(AbstractCollection $collection)
    {
        $options = $collection->toOptionArray();

        foreach ($options as $key => $option) {
            $option['label'] = ucfirst($option['label']);
            $options[$key] = $option;
        }
        return $options;
    }
}
