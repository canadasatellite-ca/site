<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model;

class Manifest extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageside\CanadaPostShipping\Model\ResourceModel\Manifest');
    }

    public function createManifestGroupId()
    {
        return time();
    }
}
