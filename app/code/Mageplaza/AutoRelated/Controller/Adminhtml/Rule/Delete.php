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

use Magento\Framework\Exception\LocalizedException;
use Mageplaza\AutoRelated\Controller\Adminhtml\Rule;

/**
 * Class Delete
 * @package Mageplaza\AutoRelated\Controller\Adminhtml\Rule
 */
class Delete extends Rule
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->autoRelatedRuleFactory->create()->deleteById($id);
                $this->messageManager->addSuccess(__('You deleted the rule.'));
                $this->_redirect('autorelated/*/');

                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete this rule right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('autorelated/*/');

                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a rule to delete.'));
        $this->_redirect('autorelated/*/');
    }
}
