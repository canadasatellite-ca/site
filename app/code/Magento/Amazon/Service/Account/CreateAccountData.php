<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

use Magento\Amazon\Model\Amazon\Definitions;

class CreateAccountData
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $countryCode;
    /**
     * @var string
     */
    private $websiteCode;

    /**
     * CreateAccountData constructor.
     * @param string $name
     * @param string $email
     * @param string $countryCode
     * @param string $website
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(string $name, string $email, string $countryCode, string $website)
    {
        \Assert\Assertion::notBlank($name, 'Name is required');
        \Assert\Assertion::notBlank($email, 'Email is required');
        \Assert\Assertion::email($email, 'Email is invalid');
        \Assert\Assertion::notBlank($countryCode, 'Country code is required');
        \Assert\Assertion::inArray(
            $countryCode,
            array_keys(Definitions::getEnabledMarketplaces()),
            'This marketplace is not supported yet'
        );
        \Assert\Assertion::notBlank($website, 'Website code is required');
        $this->name = $name;
        $this->email = $email;
        $this->countryCode = $countryCode;
        $this->websiteCode = $website;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getWebsiteCode(): string
    {
        return $this->websiteCode;
    }
}
