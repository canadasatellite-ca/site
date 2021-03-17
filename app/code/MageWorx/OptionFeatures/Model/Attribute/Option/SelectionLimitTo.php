<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionFeatures\Model\Attribute\Option;

use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionFeatures\Helper\Data as Helper;
use MageWorx\OptionBase\Api\AttributeInterface;
use MageWorx\OptionBase\Model\Product\Option\AbstractAttribute;

class SelectionLimitTo extends AbstractAttribute implements AttributeInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param ResourceConnection $resource
     * @param Helper $helper
     */
    public function __construct(
        ResourceConnection $resource,
        Helper $helper
    ) {
        $this->helper = $helper;
        parent::__construct($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Helper::KEY_SELECTION_LIMIT_TO;
    }

    /**
     * Prepare attribute data before save
     * Returns modified value, which is ready for db save
     *
     * @param \Magento\Catalog\Model\Product\Option|\Magento\Catalog\Model\Product\Option\Value|array $data
     * @return string
     */
    public function prepareDataBeforeSave($data)
    {
        if (is_object($data)) {
            $fromValue = $data->getData(Helper::KEY_SELECTION_LIMIT_FROM);
            $toValue   = $data->getData($this->getName());
            return $fromValue > $toValue ? $fromValue : $toValue;
        } elseif (is_array($data)
            && isset($data[$this->getName()])
            && isset($data[Helper::KEY_SELECTION_LIMIT_FROM])
        ) {
            $fromValue = $data[Helper::KEY_SELECTION_LIMIT_FROM];
            $toValue   = $data[$this->getName()];
            return $fromValue > $toValue ? $fromValue : $toValue;
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function importTemplateMageOne($data)
    {
        return isset($data[$this->getName()]) ? $data[$this->getName()] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function importTemplateMageTwo($data)
    {
        return isset($data[$this->getName()]) ? $data[$this->getName()] : '';
    }
}
