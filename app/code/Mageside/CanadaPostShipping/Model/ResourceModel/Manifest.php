<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\ResourceModel;

use Mageside\CanadaPostShipping\Model\Service\Transmit;

/**
 * Class ManifestGroup
 */
class Manifest extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Shipment constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Define main table and id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageside_canadapost_manifest', 'id');
    }

    /**
     * @inheritdoc
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterSave($object);

        if ($object->getStatus() == Transmit::STATUS_TRANSMITTED) {
            $connection = $this->getConnection();
            $connection->update(
                $this->getTable('mageside_canadapost_shipment'),
                ['status' => Transmit::STATUS_TRANSMITTED],
                ['manifest_id = ?' => $object->getId()]
            );
        }
    }
}
