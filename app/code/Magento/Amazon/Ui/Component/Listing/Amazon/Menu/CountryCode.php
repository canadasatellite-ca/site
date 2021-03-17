<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Menu;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CountryCode
 */
class CountryCode implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $countryCodes = [];
        foreach (Definitions::getEnabledMarketplaces() as $marketplace) {
            $countryCode = $marketplace['countryCode'];
            $name = $marketplace['name'];
            $countryCodes[] = ['value' => $countryCode, 'label' => __($name)];
        }
        return $countryCodes;
    }
}
