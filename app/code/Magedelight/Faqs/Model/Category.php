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

class Category extends \Magento\Framework\Model\AbstractModel
{

    const CATEGORY_ID = 'category_id'; // We define the id fieldname

    /**
     * Prefix of model events names
     *
     * @var string
     */

    public $eventPrefix = 'faqs'; // parent value is 'core_abstract'

    /**
     * Name of the event object
     *
     * @var string
     */
    public $eventObject = 'category'; // parent value is 'object'

    /**
     * Name of object id field
     *
     * @var string
     */
    public $idFieldName = self::CATEGORY_ID;

    const STATUS_ENABLED = 1;
    
    const STATUS_DISABLED = 2;

    public $stdLib;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime $stdLib,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->stdLib = $stdLib;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magedelight\Faqs\Model\ResourceModel\Category');
    }

    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $urlKey = $this->getUrlKey();
        if (!$urlKey) {
            $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $this->getTitle());
            $urlKey = strtolower($urlKey);
            $urlKey = trim($urlKey, '-');
        }
        $this->setUrlKey($urlKey);
        $this->setUpdatedAt($this->stdLib->formatDate(true, true));
        if (!$this->getId()) {
            $this->setCreatedAt($this->stdLib->formatDate(true, true));
        }
    }
}
