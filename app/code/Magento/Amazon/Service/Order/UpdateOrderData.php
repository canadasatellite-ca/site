<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Order;

class UpdateOrderData
{
    /**
     * @var string
     */
    private $orderId;
    /**
     * @var string|null
     */
    private $addressOne;
    /**
     * @var string|null
     */
    private $addressTwo;
    /**
     * @var string|null
     */
    private $addressThree;
    /**
     * @var string|null
     */
    private $city;
    /**
     * @var string|null
     */
    private $region;
    /**
     * @var string|null
     */
    private $postalCode;
    /**
     * @var string|null
     */
    private $country;

    public function __construct(string $orderId, ?string $addressOne, ?string $addressTwo, ?string $addressThree, ?string $city, ?string $region, ?string $postalCode, ?string $country)
    {
        \Assert\Assertion::notBlank($orderId, 'Order Id is required');
        \Assert\Assertion::nullOrNotBlank($addressOne, 'Shipping address 1 is a required value');
        \Assert\Assertion::nullOrNotBlank($city, 'City is a required value');
        \Assert\Assertion::nullOrNotBlank($region, 'Region is a required value');
        \Assert\Assertion::nullOrNotBlank($postalCode, 'Postal Code is a required value');
        \Assert\Assertion::nullOrNotBlank($country, 'Country is a required value');
        $this->orderId = $orderId;
        $this->addressOne = $addressOne;
        $this->addressTwo = $addressTwo;
        $this->addressThree = $addressThree;
        $this->city = $city;
        $this->region = $region;
        $this->postalCode = $postalCode;
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @return string|null
     */
    public function getAddressOne(): ?string
    {
        return $this->addressOne;
    }

    /**
     * @return string|null
     */
    public function getAddressTwo(): ?string
    {
        return $this->addressTwo;
    }

    /**
     * @return string|null
     */
    public function getAddressThree(): ?string
    {
        return $this->addressThree;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }
}
