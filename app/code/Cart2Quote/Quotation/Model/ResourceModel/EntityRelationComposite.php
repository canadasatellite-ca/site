<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel;

/**
 * Class EntityRelationComposite
 */
class EntityRelationComposite
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\EntityRelationComposite {
        processRelations as private traitProcessRelations;
    }

    /**
     * @var array
     */
    protected $relationProcessors;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param array $relationProcessors
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $relationProcessors = []
    ) {
        $this->eventManager = $eventManager;
        $this->relationProcessors = $relationProcessors;
    }

    /**
     * Process relations
     *
     * @param \Magento\Sales\Model\AbstractModel $object
     * @return void
     */
    public function processRelations(\Magento\Sales\Model\AbstractModel $object)
    {
        $this->traitProcessRelations($object);
    }
}
