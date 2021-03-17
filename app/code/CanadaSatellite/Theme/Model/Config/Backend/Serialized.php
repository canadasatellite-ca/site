<?php

namespace CanadaSatellite\Theme\Model\Config\Backend;

class Serialized extends \Magento\Config\Model\Config\Backend\Serialized
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
