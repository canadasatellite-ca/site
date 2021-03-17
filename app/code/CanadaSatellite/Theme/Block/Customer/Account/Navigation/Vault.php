<?php

namespace CanadaSatellite\Theme\Block\Customer\Account\Navigation;

class Vault extends \Magento\Customer\Block\Account\SortLink
{

    protected $_customerCards;

    protected $_vault;

    public function __construct(
        \Magedelight\Firstdata\Block\Customer\Cards\Listing $customerCards,
        \Magento\Vault\Block\Customer\CreditCards $vault,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        array $data = [])
    {
        $this->_vault = $vault;
        $this->_customerCards = $customerCards;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        if (count($this->_vault->getPaymentTokens()) || count($this->_customerCards->getCustomerCards())) {
            return parent::_toHtml();
        }
        return '';
    }
}
