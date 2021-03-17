<?php

namespace MW\Onestepcheckout\Block\Checkout;

class Top extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry;

	/**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
	protected $_sessionManager;

	/**
     * @var \Magento\Customer\Model\Session
     */
	protected $_customerSession;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Customer\Model\Session $customerSession,
		array $data = []
	) {
		$this->_coreRegistry = $coreRegistry;
		$this->_sessionManager = $context->getSession();
		$this->_customerSession = $customerSession;
		parent::__construct($context, $data);
	}

	public function getRegistry()
    {
    	return $this->_coreRegistry;
    }

    public function getSessionManager()
    {
    	return $this->_sessionManager;
    }

    public function getCustomerSession()
    {
    	return $this->_customerSession;
    }
}
