<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel;
/**
 * Trait EntityRelationComposite
 */
trait EntityRelationComposite
{
    /**
     * Process relations
     *
     * @param \Magento\Sales\Model\AbstractModel $object
     * @return void
     */
    private function processRelations(\Magento\Sales\Model\AbstractModel $object)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($this->relationProcessors as $processor) {
            /** @var \Cart2Quote\Quotation\Model\ResourceModel\EntityRelationInterface $processor */
            $processor->processRelation($object);
        }
        $this->eventManager->dispatch(
            $object->getEventPrefix() . '_process_relation',
            [
                'object' => $object
            ]
        );
		}
	}
}
