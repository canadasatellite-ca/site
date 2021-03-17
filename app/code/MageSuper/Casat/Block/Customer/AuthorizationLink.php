<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Block\Customer;

use Magento\Customer\Model\Context;

/**
 * Customer authorization link
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class AuthorizationLink extends  \Magento\Customer\Block\Account\AuthorizationLink
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }
        $logout_part = '';
        if($this->isLoggedIn()){
            $logout_part = '<ul><li><a href="'.$this->_customerUrl->getLogoutUrl().'" class="account-logout-link">' . $this->getLabel() . '</a></li></ul>';
        }

        return '<li class="my-accoun-link"><a class="account-link" href="'.$this->_customerUrl->getAccountUrl().'">' . __('My Account'). '</a>
                '.$logout_part.'
            </li>';
    }
}
