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
namespace Magedelight\Faqs\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magedelight\Faqs\Model\FaqFactory;
use Magento\Framework\Registry;

abstract class Faq extends Action
{
    /**
     * Faq factory
     *
     * @var FaqFactory
     */
    public $faqFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @param Registry $registry
     * @param FaqFactory $FaqFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        FaqFactory $faqFactory,
        Context $context
    ) {
    
        $this->coreRegistry = $registry;
        $this->faqFactory = $faqFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magedelight\Faqs\Model\Faq
     */
    public function initFaq()
    {
        $faqId  = (int) $this->getRequest()->getParam('id');
        $faq = $this->faqFactory->create();
        if ($faqId) {
            $faq->load($faqId);
        }
        $this->coreRegistry->register('faqs_faq', $faq);
        return $faq;
    }

    public function filterData($data)
    {
        if ($data['question_type'] == 1) {
            $data['category_id'] = null;
        }
        return $data;
    }
}
