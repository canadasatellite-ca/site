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
namespace Magedelight\Faqs\Controller\Faq;

use Magento\Customer\Model\Session;

class Addquetion extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magedelight\Faqs\Model\Faq
     */
    public $modelfaq;

    /**
     * @var \Magedelight\Faqs\Model\Product
     */
    public $modelproduct;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * Addquetion constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param Session $customerSession
     * @param \Magedelight\Faqs\Model\Faq $modelfaq
     * @param \Magedelight\Faqs\Model\Product $modelproduct
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Session $customerSession,
        \Magedelight\Faqs\Model\Faq $modelfaq,
        \Magedelight\Faqs\Model\Product $modelproduct,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $customerSession;
        $this->modelfaq = $modelfaq;
        $this->modelproduct = $modelproduct;
        parent::__construct($context);
    }

    /**
     * Question Save Action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!empty($data['recaptcha_response_v3']) || (!empty($data['form_key']) && !empty($data['question']))) {
            $faqData = [];

            if (isset($data['question'])) {
                $faqData['question'] = $data['question'];
            }

            $faqData['position'] = '10';
            $faqData['like'] = '1';
            $faqData['dislike'] = '0';
            $faqData['question_type'] = \Magedelight\Faqs\Model\Faq::PRODUCT_FAQ;
            $faqData['status'] = \Magedelight\Faqs\Model\Category::STATUS_DISABLED;
            $faqData['store_id'] = '0';
            $faqData['is_most_viewed'] = '2';
            $faqData['tags'] = 'product';

            if ($this->session->isLoggedIn()) {
                $faqData['created_by'] = \Magedelight\Faqs\Model\Faq::LOGIN_CUSTOMER;
                $faqData['customer_id'] = $this->session->getCustomer()->getId();  // get Customer Id
                $faqData['category_id'] = null;
                $faqData['customer_name'] = $this->session->getCustomer()->getName();
                $faqData['customer_email'] = $this->session->getCustomer()->getEmail();
            } else {
                $faqData['created_by'] = \Magedelight\Faqs\Model\Faq::GUEST_CUSTOMER;
                $faqData['customer_id'] = '0';
                if (!isset($data['guest_name'])) {
                    $this->messageManager->addError(__('Please fill your name'));
                    $this->_redirect($data['producturl']);
                    return;
                }
                $faqData['customer_name'] = $data['guest_name'];
                $faqData['customer_email'] = $data['guest_emailaddress'];
                $faqData['category_id'] = null;
            }

            $faqData['phone'] = $data['phone'];

            if ($data) {
                /** @var \Magedelight\Faqs\Model\Faq $model */
                $model = $this->modelfaq;
                $model->setData($faqData);
                try {
                    $model->save();
                    $this->modelfaq->addProductsids($model->getId(), $data['productid']);
                    $this->messageManager->addSuccess(__('Your question has been successfully submitted'));
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the item'));
                }
                $this->_redirect($data['producturl']);
            }

            $this->_redirect($data['producturl']);
        } else {
            if (is_array($data) && array_key_exists('producturl', $data) && is_string($data['producturl'])) {
                $this->_redirect($data['producturl']);
            } else {
                $this->_redirect('/');
            }
        }
    }
}
