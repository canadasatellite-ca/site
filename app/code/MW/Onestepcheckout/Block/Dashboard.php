<?php

namespace MW\Onestepcheckout\Block;

class Dashboard extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \MW\Onestepcheckout\Helper\Data
	 */
	protected $_dataHelper;

	/**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \MW\Onestepcheckout\Model\System\Config\Source\Term
     */
    protected $_termCondition;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \MW\Onestepcheckout\Helper\Data $dataHelper
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \MW\Onestepcheckout\Model\System\Config\Source\Term $termCondition
     * @param array $data
     */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\MW\Onestepcheckout\Helper\Data $dataHelper,
		\Magento\Customer\Model\Session $customerSession,
        \MW\Onestepcheckout\Model\System\Config\Source\Term $termCondition,
		array $data = []
	) {
		$this->_dataHelper = $dataHelper;
		$this->_sessionManager = $context->getSession();
		$this->_customerSession = $customerSession;
        $this->_termCondition = $termCondition;
		parent::__construct($context, $data);
	}

	protected function _construct()
    {
        $this->setTemplate('MW_Onestepcheckout::dashboard.phtml');
    }

	protected function _prepareLayout()
    {
    	// Check has MW_DDate extension or not
        if ($this->_dataHelper->isDDateRunning()) {
        	$select = $this->getLayout()->createBlock('MW\DDate\Block\Onepage\Ddate')
                    ->setTemplate('MW_DDate::checkout/onepage/ddate_osc.phtml')
                    ->setName('ddate')
                    ->setId('ddate')
                    ->setTitle('Delivery Times')
                    ->setClass('delivery mw-osc-block-content');

			$this->setChild('ddate', $select);
        }

        return parent::_prepareLayout();
    }

    protected function _toHtml()
    {
        if (!$this->_dataHelper->getStoreConfig('onestepcheckout/general/enabled')) {
            return '';
        }
        if ($this->_sessionManager->getOs() == 'change') {
            return '';
        }

        return $this->fetchView($this->getTemplateFile());
    }

    /**
     * Retrive session manager object
     * @return object
     */
    public function getSessionManager()
    {
    	return $this->_sessionManager;
    }

    /**
     * Retrive customer session object
     * @return object
     */
    public function getCustomerSession()
    {
    	return $this->_customerSession;
    }

    /**
     * Retrive term information
     * 
     * @param  int $condition
     * @return string
     */
    public function getTermByCondition($condition)
    {
        return $this->_termCondition->getTermById($condition);
    }
}
