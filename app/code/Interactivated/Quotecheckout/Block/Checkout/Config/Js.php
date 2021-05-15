<?php

namespace Interactivated\Quotecheckout\Block\Checkout\Config;

class Js extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
	protected $_scopeResolver;

	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_customerSession;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param array $data
	 */
	function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\App\ScopeResolverInterface $scopeResolver,
		\Magento\Customer\Model\Session $customerSession,
		array $data = []
	) {
		$this->_scopeResolver = $scopeResolver;
		$this->_customerSession = $customerSession;
		parent::__construct($context, $data);
	}

	function _construct()
	{
		$this->setTemplate('Interactivated_Quotecheckout::config/js.phtml');
	}

	function isCurrentlySecure()
	{
		return (int) $this->_scopeResolver->getScope()->isCurrentlySecure();
	}

	function getCustomer()
	{
		return $this->_customerSession->getCustomer();
	}
}
