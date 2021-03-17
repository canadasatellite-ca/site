<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Events;

/**
 * Class Converter
 *
 * @package Cart2Quote\Quotation\Plugin\Events
 */
class Converter
{
    /**
     * After convert trigger
     *
     * @param \Magento\Framework\Event\Config\Converter $subject
     * @param array $results
     * @return array
     */
    public function afterConvert(\Magento\Framework\Event\Config\Converter $subject, $results)
    {
        foreach ($results as $eventName => $eventConfig) {
            if (strpos($eventName, 'carttoquote') !== false) {
                unset($results[$eventName]);
                $eventName = str_replace('carttoquote', 'cart2quote', $eventName);
                $results[$eventName] = $eventConfig;
            }
        }

        return $results;
    }
}
