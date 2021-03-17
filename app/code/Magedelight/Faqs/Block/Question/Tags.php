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
namespace Magedelight\Faqs\Block\Question;

class Tags extends \Magento\Framework\View\Element\Template
{
    public $faqColletion;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory $faqColletion,
        array $data = []
    ) {
        $this->faqColletion = $faqColletion;
        parent::__construct($context, $data);
    }
    
    
    public function getQuestionData($storeId = null) {
        $data = $this->getRequest()->getParams();
        $storeId = ($storeId === null) ? $this->_storeManager->getStore()->getId() : $storeId;
        $questionColletion =  $this->faqColletion->create()
                ->addFieldToFilter(
                        ['tags'],
                        [
                            ["finset"=>[$data['tag']]]
                        ]
                )
                ->addFieldToFilter(
                        ['store_id', 'store_id'],
                        [
                            ["finset"=>[$storeId]],
                            ["finset"=>[0]]
                        ]
                )
                ->addFieldToFilter('status', [
                    'eq' => \Magedelight\Faqs\Model\Faq::STATUS_ENABLED
                    ])
                ->addFieldToFilter('question_type', [
                    'in' => [\Magedelight\Faqs\Model\Faq::GENERIC_FAQ ,\Magedelight\Faqs\Model\Faq::BOTH_FAQ]
                    ])
                ->addFieldToFilter('answer', ['notnull' => true])
                ->setOrder('position', 'ASC');
        return $questionColletion;
    }
}
