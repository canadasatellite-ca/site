<?php

namespace MW\Onestepcheckout\Controller\Adminhtml;

abstract class Onestepcheckout extends \Magento\Backend\App\Action
{
	/**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @var \Magento\Framework\Xml\Parser
     */
    protected $_xmlParser;

    /**
     * @var \MW\Onestepcheckout\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\Xml\Parser $xmlParser
     * @param \MW\Onestepcheckout\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Xml\Parser $xmlParser,
        \MW\Onestepcheckout\Helper\Data $dataHelper
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_xmlParser = $xmlParser;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }

	/**
     * Access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mw_Onestepcheckout::system_config');
    }
}
