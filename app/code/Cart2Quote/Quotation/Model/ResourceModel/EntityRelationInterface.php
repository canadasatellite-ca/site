<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel;

/**
 * Interface EntityRelationInterface
 */
interface EntityRelationInterface
{
    /**
     * Process object relations
     *
     * @param \Magento\Sales\Model\AbstractModel $object
     * @return void
     */
    public function processRelation(\Magento\Sales\Model\AbstractModel $object);
}
