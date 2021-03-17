<?php
/* Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category Magedelight
 * @package Magedelight_Faqs
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 * 
 */

namespace Magedelight\Faqs\Model;

use \Magento\Framework\Model\AbstractModel;

class Faq extends AbstractModel
{

    const FAQ_ID = 'question_id'; // We define the id fieldname
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    const PRODUCT_FAQ = 1;
    const GENERIC_FAQ = 2;
    const BOTH_FAQ = 3;
    const ADMIN_CUSTOMER = 'admin';
    const LOGIN_CUSTOMER = 'customer';
    const GUEST_CUSTOMER = 'guest';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    public $eventPrefix = 'faqs'; // parent value is 'core_abstract'
    public $_eventPrefix = 'faqs';

    /**
     * Name of the event object
     *
     * @var string
     */
    public $eventObject = 'faq'; // parent value is 'object'

    /**
     * Name of object id field
     *
     * @var string
     */
    public $idFieldName = self::FAQ_ID; // parent value is 'id'

    /**
     * Initialize resource model
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _construct()
    {
        $this->_init('Magedelight\Faqs\Model\ResourceModel\Faq');
    }
    // @codingStandardsIgnoreEnd

    public function getEnableStatus()
    {
        return 1;
    }

    public function getDisableStatus()
    {
        return 0;
    }

    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    public function getAvailableCreatedStatuses()
    {
        return [
            self::ADMIN_CUSTOMER => __('Admin'),
            self::LOGIN_CUSTOMER => __('Customer'),
            self::GUEST_CUSTOMER => __('Guest')
        ];
    }

    public function getAvailableQuestiontypes()
    {
        return [
            self::PRODUCT_FAQ => __('Product Faq'),
            self::GENERIC_FAQ => __('Generic Faq'),
            self::BOTH_FAQ => __('For Both')
        ];
    }

    public function getProducts(\Magedelight\Faqs\Model\Faq $object)
    {
        return $this->getResource()->getProducts($object);
    }

    public function getFaqIds($productId)
    {
        return $this->getResource()->getFaqIds($productId);
    }

    public function addProductsids($faqId, $productId)
    {
        return $this->getResource()->addProductsids($faqId, $productId);
    }
}
