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

use Mageplaza\AutoRelated\Controller\Adminhtml\Rule;

/**
 * Class Test
 * @package Mageplaza\AutoRelated\Controller\Adminhtml\Rule
 */
class Test extends Rule
{
    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    function execute()
    {
        $id   = $this->getRequest()->getParam('id');
        $type = $this->getRequest()->getParam('type');
        if ($id && $type) {
            $model = $this->autoRelatedRuleFactory->create()->load($id);
            if (!$model->hasChild()) {
                $this->coreRegistry->register('autorelated_test_add', true);

                return $this->resultForwardFactory->create()->forward('edit');
            }
        }
        $this->messageManager->addError(__('Can not Add A/B Testing.'));

        return $this->_redirect('autorelated/*');
    }
}
