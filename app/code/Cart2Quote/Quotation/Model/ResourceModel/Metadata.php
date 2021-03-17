<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel;

/**
 * Class Metadata
 *
 * @package Cart2Quote\Quotation\Model\ResourceModel
 */
class Metadata
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Metadata {
        getMapper as private traitGetMapper;
        getNewInstance as private traitGetNewInstance;
    }

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $resourceClassName;

    /**
     * @var string
     */
    protected $modelClassName;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $resourceClassName
     * @param string $modelClassName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $resourceClassName,
        $modelClassName
    ) {
        $this->objectManager = $objectManager;
        $this->resourceClassName = $resourceClassName;
        $this->modelClassName = $modelClassName;
    }

    /**
     * Get mapper
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    public function getMapper()
    {
        return $this->traitGetMapper();
    }

    /**
     * Get new instance
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface
     */
    public function getNewInstance()
    {
        return $this->traitGetNewInstance();
    }
}
