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

namespace CanadaSatellite\Theme\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\ScopeInterface;
use MSP\ReCaptcha\Model\Config\Source\Type;

class Config extends \MSP\ReCaptcha\Model\Config
{
    const XML_PATH_PUBLIC_KEY = 'msp_securitysuite_recaptcha/general_V3/public_key_v3';
    const XML_PATH_PRIVATE_KEY = 'msp_securitysuite_recaptcha/general_V3/private_key_v3';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
}
