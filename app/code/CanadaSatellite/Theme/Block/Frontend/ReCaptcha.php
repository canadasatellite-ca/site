<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_ReCaptcha
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace CanadaSatellite\Theme\Block\Frontend;

use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Element\Template;
use CanadaSatellite\Theme\Model\Config\Config;
use MSP\ReCaptcha\Model\LayoutSettings;

//class ReCaptcha extends Template
class ReCaptcha extends \MageSuper\Faq\Block\Faq\Faqlist
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $data;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var LayoutSettings
     */
    private $layoutSettings;

    /**
     * ReCaptcha constructor.
     * @param Template\Context $context
     * @param DecoderInterface $decoder
     * @param EncoderInterface $encoder
     * @param LayoutSettings $layoutSettings
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        DecoderInterface $decoder,
        \Magento\Customer\Model\Session $customerSession,
        \Magedelight\Faqs\Model\FaqFactory $faqFactory,
        \Magedelight\Faqs\Model\Faq $faqList,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Template\Context $contextExtra,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \MageSuper\Faq\Model\Faq $faqList1,
        Config $config,
        EncoderInterface $encoder,
        LayoutSettings $layoutSettings,
        array $data = []
    ) {
        $this->data = $data;
        $this->decoder = $decoder;
        $this->encoder = $encoder;
        $this->layoutSettings = $layoutSettings;
        $this->config = $config;
        parent::__construct($contextExtra, $customerSession,
                            $faqFactory, $faqList,
                            $registry, $httpContext,
                            $storeManager, $filterProvider, $faqList1, $data);
    }

    /**
     * Get public reCaptcha key
     * @return string
     */
    public function getPublicKey()
    {
        return $this->config->getPublicKey();
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $layout = $this->decoder->decode(parent::getJsLayout());
        $layout['components']['msp-recaptcha']['settings'] = $this->layoutSettings->getCaptchaSettings();
        return $this->encoder->encode($layout);
    }
}
