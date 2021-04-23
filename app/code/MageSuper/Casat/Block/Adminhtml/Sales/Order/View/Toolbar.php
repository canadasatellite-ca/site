<?php

namespace MageSuper\Casat\Block\Adminhtml\Sales\Order\View;

use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;

class Toolbar
{
    protected $_logger;
    protected $_context;

    protected $_objectManager;
    protected $_registry;

    function __construct(

        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {

        $this->_logger = $logger;
        $this->_registry = $registry;
        $this->_objectManager = $objectManager;
    }

    /**
     * @param ToolbarContext $toolbar
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @return array
     */
    function beforePushButtons(
        ToolbarContext $toolbar,
        AbstractBlock $context,
        ButtonList $buttonList
    )
    {

        if (!$context instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
            return [$context, $buttonList];
        }
        $buttonList->add('casat-showhideprofit',
            [
                'label' => __('Show'),
                'onclick' => 'jQuery(\'.col-profit, .col-margin, .total-profit, .total-margin\').toggle();return false;',
                'class' => 'showhide'
            ]
        );
        return [$context, $buttonList];
    }
}