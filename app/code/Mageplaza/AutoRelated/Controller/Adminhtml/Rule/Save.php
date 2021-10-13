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

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\AutoRelated\Controller\Adminhtml\Rule;

/**
 * Class Save
 * @package Mageplaza\AutoRelated\Controller\Adminhtml\Rule
 */
class Save extends Rule
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $type = $this->getRequest()->getParam('type');
        $test = $this->getRequest()->getParam('test');
        if ($data) {
            try {
                /** @var $model \Mageplaza\AutoRelated\Model\Rule */
                $model = $this->autoRelatedRuleFactory->create();

                $filterValues = ['from_date' => $this->_dateFilter];
                if ($this->getRequest()->getParam('to_date')) {
                    $filterValues['to_date'] = $this->_dateFilter;
                }
                $inputFilter = new \Zend_Filter_Input($filterValues, [], $data);
                $data        = $inputFilter->getUnescaped();

                $id = $this->getRequest()->getParam('rule_id');
                if ($id && !$test) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new LocalizedException(__('The wrong rule is specified.'));
                    }
                }

                $validateResult = $model->validateData(new DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $this->_session->setPageData($data);
                    $this->_redirect('autorelated/rule/edit', ['id' => $model->getId(), 'type' => $model->getBlockType()]);

                    return;
                }

                if (isset($data['rule'])) {
                    if (isset($data['rule']['conditions'])) {
                        $data['conditions'] = $data['rule']['conditions'];
                    }
                    if (isset($data['rule']['actions'])) {
                        $data['actions'] = $data['rule']['actions'];
                    }
                    unset($data['rule']);
                }

                if (isset($data['display_additional'])) {
                    $data['display_additional'] = $this->helperData->serialize($data['display_additional']);
                }

                if (isset($data['add_ruc_product'])) {
                    $data['add_ruc_product'] = $this->helperData->serialize($data['add_ruc_product']);
                }

                if (isset($data['category_conditions_serialized'])) {
                    $data['category_conditions_serialized'] = $this->helperData->serialize(explode(',', $data['category_conditions_serialized']));
                }

                $data['block_type'] = $type;

                if ($id && $test) {
                    unset($data['rule_id']);
                    $data['parent_id'] = $id;
                }

                $model->loadPost($data);

                $this->_session->setPageData($model->getData());

                $model->save();
                $this->messageManager->addSuccess(__('You saved the rule.'));
                $this->_session->setPageData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('autorelated/rule/edit', ['id' => $model->getId(), 'type' => $model->getBlockType()]);

                    return;
                }
                $this->_redirect('autorelated/rule/*/');

                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('autorelated/rule/edit', ['id' => $model->getId(), 'type' => $model->getBlockType()]);
                } else {
                    $this->_redirect('autorelated/rule/new');
                }

                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the rule data. Please review the error log.')
                );
                $this->logger->critical($e);
                $this->_redirect('autorelated/rule/edit', ['id' => $this->getRequest()->getParam('rule_id'), 'type' => $this->getRequest()->getParam('type')]);

                return;
            }
        }
        $this->_redirect('autorelated/*/');
    }
}
