<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace MageSuper\Casat\Plugin\Catalog\Model\Product\Option;

use Magento\Framework\Model\AbstractModel;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;

/**
 * Catalog product option select type model
 *
 * @method \Magento\Catalog\Model\ResourceModel\Product\Option\Value _getResource()
 * @method \Magento\Catalog\Model\ResourceModel\Product\Option\Value getResource()
 * @method int getOptionId()
 * @method \Magento\Catalog\Model\Product\Option\Value setOptionId(int $value)
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Value extends \Magento\Catalog\Model\Product\Option\Value implements \Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface
{


    /**
     * @return $this
     */
    function saveValues()
    {
        foreach ($this->getValues() as $value) {
            $this->setData(
                $value
            )->setData(
                'option_id',
                $this->getOption()->getId()
            )->setData(
                'store_id',
                $this->getOption()->getStoreId()
            );
            $id = $this->getId();
            $this->unsetData('option_type_id');
            if ($this->getData('is_delete') == '1') {
                if ($id) {
                    $this->deleteValues($id);
                    $this->delete();
                }
            } else {
                $this->isDeleted(false);
                $this->save();
            }
        }
        //eof foreach()
        return $this;
    }
}
