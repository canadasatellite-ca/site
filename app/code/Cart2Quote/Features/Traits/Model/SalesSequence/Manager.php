<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\SalesSequence;
/**
 * Trait Manager
 */
trait Manager
{
    /**
     * Returns sequence for given entityType and store
     *
     * @param string $entityType
     * @param int $storeId
     * @return \Magento\Framework\DB\Sequence\SequenceInterface|\Magento\SalesSequence\Model\Sequence
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSequence($entityType, $storeId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$sequence = $this->sequenceFactory->create(
            [
                'meta' => $this->resourceSequenceMeta->loadByEntityTypeAndStore(
                    $entityType,
                    $storeId
                ),
                'entityType' => $this->entityConfig->loadByCode($entityType)
            ]
        );
        $prefix = $this->helperData->getQuotePrefix($storeId);
        if ($prefix) {
            $sequence->setPrefix($prefix);
        }
        return $sequence;
		}
	}
}
