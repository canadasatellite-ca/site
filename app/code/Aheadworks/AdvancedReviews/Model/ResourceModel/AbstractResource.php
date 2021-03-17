<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;

/**
 * Class AbstractResource
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel
 */
abstract class AbstractResource extends AbstractDb
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param Context $context
     * @param EntityManager $entityManager
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Save object
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $object
            ->validateBeforeSave()
            ->beforeSave();
        $this->entityManager->save($object);
        $object->afterSave();

        return $this;
    }

    /**
     * Delete object
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        $object->afterDelete();

        return $this;
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param int $objectId
     * @param string $field
     * @return $this
     */
    public function load(AbstractModel $object, $objectId, $field = null)
    {
        if (!empty($objectId)) {
            $arguments = $this->getArgumentsForLoading();
            $this->entityManager->load($object, $objectId, $arguments);
        }
        return $this;
    }

    /**
     * Retrieve arguments array for entity loading
     *
     * @return array
     */
    protected function getArgumentsForLoading()
    {
        return [];
    }
}
