<?php

namespace CanadaSatellite\Theme\Block\Adminhtml\Quote\View\Items\Columns;

class Name extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns\Name
{

    function getOrderOptionsFromInfoBuyRequest()
    {
        $date_start = null;
        if ($options = $this->getItem()->getProduct()->getTypeInstance(true)->getOrderOptions($this->getItem()->getProduct())) {
            if (!isset($options['options']) && isset($options['info_buyRequest'])) {
                if (isset($options['info_buyRequest']['options'])) {
                    $options_info = $options['info_buyRequest']['options'];
                    if (isset (reset($options_info)['date_internal'])) {
                        $date_start = reset($options_info)['date_internal'];
                    }
                }
            }
        }

        return $date_start;
    }
}
