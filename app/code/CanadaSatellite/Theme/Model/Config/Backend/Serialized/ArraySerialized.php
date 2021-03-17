<?php

namespace CanadaSatellite\Theme\Model\Config\Backend\Serialized;

/**
 * @api
 * @since 100.0.2
 */
class ArraySerialized extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * @return void
     */
    protected function _afterLoad()
    {
        try {
            parent::_afterLoad();
        } catch (\Exception $e) {
            //
        }

        $value = $this->getValue();
        if ($value && !is_array($value)) {
            $value = @unserialize($value);
            $this->setValue($value);
        }
    }
}
