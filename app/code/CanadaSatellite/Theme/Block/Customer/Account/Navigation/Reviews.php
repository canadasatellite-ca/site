<?php

namespace CanadaSatellite\Theme\Block\Customer\Account\Navigation;

class Reviews extends \Magento\Customer\Block\Account\SortLink
{

    protected $_reviews;

    public function __construct(
        \Magento\Review\Block\Customer\ListCustomer $reviews,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        array $data = [])
    {
        $this->_reviews = $reviews;
        parent::__construct($context, $defaultPath, $data);
    }

    protected function _toHtml()
    {
        if ($this->_reviews->getReviews() && count($this->_reviews->getReviews())) {
            return parent::_toHtml();
        }
        return '';
    }

}