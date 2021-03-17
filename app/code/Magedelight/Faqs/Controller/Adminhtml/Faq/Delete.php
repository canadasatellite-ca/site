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
namespace Magedelight\Faqs\Controller\Adminhtml\Faq;
 
use Magento\Backend\App\Action;
 
class Delete extends Action
{
    public $faqModel;
    
    public function __construct(
        Action\Context $context,
        \Magedelight\Faqs\Model\Faq $faqModel
    ) {
        parent::__construct($context);
        $this->faqModel = $faqModel;
    }
 
    /**
     * {@inheritdoc}
     */
     // @codingStandardsIgnoreStart
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magedelight_Faqs::config_faq');
    }
    // @codingStandardsIgnoreEnd
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $faqModel = $this->faqModel;
                $faqModel->load($id);
                $faqModel->delete();
                $this->messageManager->addSuccess(__('Question deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Question does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}
