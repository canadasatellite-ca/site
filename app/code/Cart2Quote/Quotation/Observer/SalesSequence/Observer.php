<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Observer\SalesSequence;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CreateSequence
 */
class Observer implements ObserverInterface
{
    /**
     * @var \Magento\SalesSequence\Model\Builder
     */
    protected $sequenceBuilder;

    /**
     * @var \Magento\SalesSequence\Model\EntityPool
     */
    protected $entityPool;

    /**
     * @var \Cart2Quote\Quotation\Model\SalesSequence\Config
     */
    protected $sequenceConfig;

    /**
     * @param \Magento\SalesSequence\Model\Builder $sequenceBuilder
     * @param \Cart2Quote\Quotation\Model\SalesSequence\EntityPool $entityPool
     * @param \Cart2Quote\Quotation\Model\SalesSequence\Config $sequenceConfig
     */
    public function __construct(
        \Magento\SalesSequence\Model\Builder $sequenceBuilder,
        \Cart2Quote\Quotation\Model\SalesSequence\EntityPool $entityPool,
        \Cart2Quote\Quotation\Model\SalesSequence\Config $sequenceConfig
    ) {
        $this->sequenceBuilder = $sequenceBuilder;
        $this->entityPool = $entityPool;
        $this->sequenceConfig = $sequenceConfig;
    }

    /**
     * Execute (observer entypoint)
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $observer->getData('store')->getId();
        foreach ($this->entityPool->getEntities() as $entityType) {
            $this->sequenceBuilder->setPrefix($this->sequenceConfig->get('prefix'))
                ->setSuffix($this->sequenceConfig->get('suffix'))
                ->setStartValue($this->sequenceConfig->get('startValue'))
                ->setStoreId($storeId)
                ->setStep($this->sequenceConfig->get('step'))
                ->setWarningValue($this->sequenceConfig->get('warningValue'))
                ->setMaxValue($this->sequenceConfig->get('maxValue'))
                ->setEntityType($entityType)
                ->create();
        }
        return $this;
    }
}
