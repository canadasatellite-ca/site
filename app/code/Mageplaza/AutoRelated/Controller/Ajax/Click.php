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

namespace Mageplaza\AutoRelated\Controller\Ajax;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\SessionFactory;
use Magento\Framework\App\Action\Action;
use Mageplaza\AutoRelated\Helper\Data;
use Mageplaza\AutoRelated\Model\ResourceModel\RuleFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Load
 * @package Mageplaza\AutoRelated\Controller\Ajax
 */
class Click extends Action
{
    /**
     * @var \Mageplaza\AutoRelated\Model\ResourceModel\RuleFactory
     */
    protected $autoRelatedRuleFac;

    /**
     * @var \Magento\Catalog\Model\SessionFactory
     */
    protected $catalogSession;

    /**
     * @var \Mageplaza\AutoRelated\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Model\SessionFactory $catalogSession
     * @param \Mageplaza\AutoRelated\Helper\Data $helperData
     * @param \Mageplaza\AutoRelated\Model\ResourceModel\RuleFactory $autoRelatedRuleFac
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        SessionFactory $catalogSession,
        Data $helperData,
        RuleFactory $autoRelatedRuleFac
    )
    {
        parent::__construct($context);

        $this->autoRelatedRuleFac = $autoRelatedRuleFac;
        $this->catalogSession     = $catalogSession;
        $this->helperData         = $helperData;
        $this->logger             = $logger;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        if (!$this->helperData->isEnabled()) {
            return false;
        }

        $ruleId  = $this->getRequest()->getParam('ruleId');
        $session = $this->catalogSession->create();
        if ($ruleId) {
            try {
                if (!$session->getAutoRelated()) {
                    $this->autoRelatedRuleFac->create()->updateClick($ruleId);
                    $session->setAutoRelated(true);
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }
}
