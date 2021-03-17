<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

use Assert\Assertion;
use Magento\Amazon\Model\Amazon\Definitions;

class SearchMappingData
{
    /**
     * @var string
     */
    private $amazonIdType;
    /**
     * @var string
     */
    private $magentoAttributeCode;

    /**
     * SearchMappingData constructor.
     * @param string $amazonIdType
     * @param string $magentoAttributeCode
     * @param array $magentoAttributeCodes
     */
    public function __construct(
        string $amazonIdType,
        string $magentoAttributeCode,
        array $magentoAttributeCodes
    ) {
        Assertion::inArray(
            $amazonIdType,
            Definitions::getAmazonIdTypes(),
            $amazonIdType . ' is not a valid Amazon ID type.'
        );
        Assertion::inArray(
            $magentoAttributeCode,
            $magentoAttributeCodes,
            'Attribute is not a valid magento product attribute.'
        );
        $this->amazonIdType = $amazonIdType;
        $this->magentoAttributeCode = $magentoAttributeCode;
    }

    /**
     * @return string
     */
    public function getAmazonIdType(): string
    {
        return $this->amazonIdType;
    }

    /**
     * @return string
     */
    public function getMagentoAttributeCode(): string
    {
        return $this->magentoAttributeCode;
    }
}
