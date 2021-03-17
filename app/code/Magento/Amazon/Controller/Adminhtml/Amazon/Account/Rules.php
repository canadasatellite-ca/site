<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account;

use Magento\Backend\App\Action;

/**
 * Class Rules
 */
abstract class Rules extends Action
{
    /**
     * Set specified data to current rule.
     * Set conditions recursively.
     *
     * @param array $data
     * @return array
     */
    protected function convertFlatToRecursive(array $data)
    {
        /** @var array */
        $arr = [];

        foreach ($data as $key => $value) {
            if (($key === 'conditions' || $key === 'actions') && is_array($value)) {
                foreach ($value as $id => $valueData) {
                    $path = explode('--', $id);
                    $node = &$arr;

                    for ($i = 0, $l = count($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = &$node[$key][$path[$i]];
                    }

                    foreach ($valueData as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            }
        }

        return $arr;
    }
}
