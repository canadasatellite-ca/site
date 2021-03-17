<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Source;

class NonMailableAttribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    private $_options;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_attributeResource;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $attributeResource
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $attributeResource
    ) {
        $this->_attributeResource = $attributeResource;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $attributes = $this->getAttributes();

            $options = [];
            $options[] = [
                'value' => 'none',
                'label' => __('None')
            ];
            foreach ($attributes as $attribute) {
                $options[] = [
                    'value' => $attribute['attribute_code'],
                    'label' => $attribute['frontend_label']
                ];
            }
            $this->_options = $options;
        }

        return $this->_options;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $connection = $this->_attributeResource->getConnection();
        $select = $connection->select()
            ->from(
                ['a' => $this->_attributeResource->getTable('eav_attribute')],
                ['a.attribute_id', 'a.attribute_code', 'a.frontend_label']
            )
            ->join(
                ['t' => $this->_attributeResource->getTable('eav_entity_type')],
                'a.entity_type_id = t.entity_type_id',
                []
            )
            ->where('t.entity_type_code = ?', 'catalog_product')
            ->where('a.frontend_input = ?', 'boolean');

        return $connection->fetchAll($select);
    }
}