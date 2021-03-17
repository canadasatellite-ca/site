<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Rule;

use Magento\CatalogRule\Model\Rule\Condition\Product;

/**
 * Class ProductOverride
 */
class ProductOverride extends Product
{
    /** @const int */
    const IS_NOT_OPERATOR = '!=';

    /**
     * Extends core functionality and when "is_channel" is set on the product instance
     * it edits the behavior to accomodate custom rule engine functionality
     *
     * Customizations include allowing product "not visible" to be matching (if passes all other rules)
     * and edits the behavior of the "is not" operator to allow if null
     *
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        /** @var bool */
        $ruleType = $model->getData('is_channel');

        if (!$ruleType) {
            return parent::validate($model);
        }

        /** @var string */
        $attrCode = $this->getAttribute();

        if ('category_ids' == $attrCode) {
            return $this->validateAttribute($model->getCategoryIds());
        }

        /** @var string */
        $oldAttrValue = $model->getData($attrCode);

        if ($oldAttrValue === null) {

            /** @var string */
            $operator = $this->getOperatorForValidate();

            // reverse logic on is not
            if ($operator == self::IS_NOT_OPERATOR) {
                return true;
            }
        }

        $this->_setAttributeValue($model);

        $result = $this->validateAttribute($model->getData($attrCode));
        $this->_restoreOldAttrValue($model, $oldAttrValue);

        return (bool)$result;
    }
}
