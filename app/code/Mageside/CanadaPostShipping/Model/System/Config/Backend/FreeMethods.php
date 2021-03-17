<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\System\Config\Backend;

class FreeMethods extends \Magento\Framework\App\Config\Value
{
    /**
     * Process data after load
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = !empty(trim($value)) ? unserialize($value) : null;
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     *
     * @return void
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $value = serialize($value);
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $this->setValue($value);
    }
}
