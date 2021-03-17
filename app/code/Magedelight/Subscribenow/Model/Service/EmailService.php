<?php

/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Model\Service;

use Magedelight\Subscribenow\Helper\Data as DataHelper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

/**
 * Send all extension email from here
 */
class EmailService
{
    const EMAIL_PROFILE_UPADATE = 'profile_update';
    const EMAIL_NEW_SUBSCRIPTION = 'new_subscription';
    const EMAIL_PAYMENT_FAILED = 'payment_failed';
    const EMAIL_CARD_ADD = 'new_card_add';
    const EMAIL_REMINDER = 'subscription_reminder';
    const EMAIL_TOPUP_REMINDER = 'wallet_topup_reminder';

    private static $templateData = [
        self::EMAIL_NEW_SUBSCRIPTION    => DataHelper::XML_PATH_SUBSCRIPTION_EMAIL,
        self::EMAIL_CARD_ADD            => DataHelper::XML_PATH_NEW_CARD_ADD_EMAIL,
        self::EMAIL_PAYMENT_FAILED      => DataHelper::XML_PATH_PAYMENT_FAILED_EMAIL,
        self::EMAIL_PROFILE_UPADATE     => DataHelper::XML_PATH_PROFILE_UPDATE_EMAIL,
        self::EMAIL_REMINDER            => DataHelper::XML_PATH_REMINDER_EMAIL,
        self::EMAIL_TOPUP_REMINDER      => DataHelper::XML_PATH_EWALLET_TOPUP_REMINDER_EMAIL
    ];

    private static $bccData = [
        self::EMAIL_NEW_SUBSCRIPTION    => DataHelper::XML_PATH_REMINDER_EMAIL_BCC,
        self::EMAIL_CARD_ADD            => DataHelper::XML_PATH_NEW_CARD_ADD_EMAIL_BCC,
        self::EMAIL_PAYMENT_FAILED      => DataHelper::XML_PATH_PAYMENT_FAILED_EMAIL_BCC,
        self::EMAIL_PROFILE_UPADATE     => DataHelper::XML_PATH_PROFILE_UPDATE_EMAIL_BCC,
        self::EMAIL_REMINDER            => DataHelper::XML_PATH_REMINDER_EMAIL_BCC,
        self::EMAIL_TOPUP_REMINDER      => DataHelper::XML_PATH_EWALLET_TOPUP_REMINDER_EMAIL
    ];

    /**
     * @var DataHelper
     */
    private $helper;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var object
     */
    private $store;

    /**
     * @var array
     */
    private $templateVars;

    /**
     * @var array
     */
    private $mailTo;

    /**
     * @var string
     */
    private $template;

    /**
     * @var array|null
     */
    private $bcc;

    /**
     * Constructor
     *
     * @param DataHelper $helper
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        DataHelper $helper,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder
    ) {
        $this->helper = $helper;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
    }

    public function send()
    {
        $this->inlineTranslation->suspend();
        $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->getTemplate())
                ->setTemplateVars($this->getTemplateVars())
                ->setTemplateOptions($this->getTemplateOption())
                ->setFrom($this->getSender())
                ->addTo($this->getSendTo())
                ->getTransport();

        if ($this->getBcc()) {
            $transport->addBcc($this->getBcc());
        }

        try {
            return $transport->sendMessage();
        } catch (\Exception $e) {
        }
        
        $this->inlineTranslation->resume();
        return null;
    }

    public function setStoreId($storeId)
    {
        $this->store = $storeId;
    }

    private function getStoreId()
    {
        return $this->store;
    }

    /**
     * Set Email Template Variable
     * array(
     *    'message'   => "This is sample test email from EmailService Class.."
     * );
     * @param array $data
     */
    public function setTemplateVars($data)
    {
        $this->templateVars = $data;
        return $this;
    }

    private function getTemplateVars()
    {
        return $this->templateVars;
    }

    private function getTemplate()
    {
        return $this->template;
    }

    private function getTemplateOption()
    {
        $area = \Magento\Framework\App\Area::AREA_FRONTEND;
        $storeId = $this->getStoreId();

        return [
            'area' => $area,
            'store' => $storeId
        ];
    }

    private function getSender()
    {
        return $this->helper->getScopeValue(DataHelper::XML_PATH_SUBSCRIPTION_SENDER, $this->getStoreId());
    }

    public function setSendTo($data)
    {
        $this->mailTo = $data;
        return $this;
    }

    private function getSendTo()
    {
        return $this->mailTo;
    }

    private function setEmailTemplateBcc($type)
    {
        $template = self::$templateData[$type] ?? null;
        $bcc = self::$bccData[$type] ?? null;

        $this->setTemplate($template);
        $this->setBcc($bcc);
    }

    public function setType($type)
    {
        $this->setEmailTemplateBcc($type);
        return $this;
    }

    private function setTemplate($template)
    {
        $this->template = $this->helper->getScopeValue($template, $this->getStoreId());
        return $this;
    }

    private function setBcc($mailpath)
    {
        $bcc = $this->helper->getScopeValue($mailpath, $this->getStoreId());
        if ($bcc) {
            $this->bcc = explode(',', $bcc);
        }
    }

    private function getBcc()
    {
        return $this->bcc;
    }
}
