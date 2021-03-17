<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Status;

use Cart2Quote\Quotation\Model\ResourceModel\EntityAbstract;
use Cart2Quote\Quotation\Model\Spi\QuoteStatusHistoryResourceInterface;

/**
 * Flat quotation quote status history resourcemodel
 */
class History extends EntityAbstract implements QuoteStatusHistoryResourceInterface
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Status\History {
        _beforeSave as private _traitBeforeSave;
        _construct as private _traitConstruct;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Status\History\Validator
     */
    protected $validator;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'quotation_quote_status_history_resource';

    /**
     * Override for Split DB
     *
     * @var string
     */
    protected $connectionName = "checkout";

    /**
     * History constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Attribute $attribute
     * @param \Magento\SalesSequence\Model\Manager $sequenceManager
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite $entityRelationComposite
     * @param \Cart2Quote\Quotation\Model\Quote\Status\History\Validator $validator
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Sales\Model\ResourceModel\Attribute $attribute,
        \Magento\SalesSequence\Model\Manager $sequenceManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite $entityRelationComposite,
        \Cart2Quote\Quotation\Model\Quote\Status\History\Validator $validator,
        $resourcePrefix = null
    ) {
        $this->connectionName = 'checkout';
        $this->validator = $validator;
        parent::__construct(
            $context,
            $attribute,
            $sequenceManager,
            $entitySnapshot,
            $entityRelationComposite,
            $resourcePrefix
        );
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this->_traitBeforeSave($object);
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }
}
