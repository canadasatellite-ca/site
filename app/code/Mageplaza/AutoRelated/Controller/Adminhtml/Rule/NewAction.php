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
 * Class NewAction
 * @package Mageplaza\AutoRelated\Controller\Adminhtml\Rule
 */
class NewAction extends Rule
{
    /**
     * @return mixed
     */
    function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
