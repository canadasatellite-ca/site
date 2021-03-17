<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AutoRelated
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AutoRelated\Controller\Adminhtml\Rule;

use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\AutoRelated\Controller\Adminhtml\Rule;

/**
 * Class Edit
 * @package Mageplaza\AutoRelated\Controller\Adminhtml\Rule
 */
class Edit extends Rule
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $id        = $this->getRequest()->getParam('id');
        $type      = $this->getRequest()->getParam('type');
        $model     = $this->autoRelatedRuleFactory->create();
        $ruleModel = $this->autoRelatedRuleFactory->create();
        if ($id) {
            try {
                $model->load($id);
                if ($model->getBlockType() != $type) {
                    $this->messageManager->addError(__('Something went wrong.'));

                    return $this->_redirect('autorelated/*');
                }
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('autorelated/*');

                return;
            }
        }

        if ($this->coreRegistry->registry('autorelated_test_add') && (!$id || $model->hasChild() || $model->getParentId())) {
            $this->messageManager->addError(__('Can not Add A/B Testing.'));

            return $this->_redirect('autorelated/*');
        }

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->coreRegistry->register('autorelated_rule_category', $model->getCategoryConditionsSerialized());
        $this->coreRegistry->register('autorelated_rule', $model);
        $this->coreRegistry->register('autorelated_type', $type);
        $this->coreRegistry->register('autoRelated_type_product', $ruleModel->load($id));

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend($id ? $model->getName() : __('New Related Block Rule'));

        $title = $id ? __('Edit Rule') : __('New Rule');
        $this->_addBreadcrumb($title, $title);

        return $resultPage;
    }
}
