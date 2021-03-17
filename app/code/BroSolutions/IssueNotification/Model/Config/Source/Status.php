<?php

namespace BroSolutions\IssueNotification\Model\Config\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'new', 'label' => __('New')],
            ['value' => 'complete', 'label' => __('Complete')],
        ];
    }
}
