<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

class UpdateAccountData
{
    /**
     * @var string
     */
    private $uuid;
    /**
     * @var string|null
     */
    private $name;
    /**
     * @var string|null
     */
    private $email;

    public function __construct(string $uuid, ?string $name, ?string $email)
    {
        \Assert\Assertion::notBlank($uuid, 'UUID is required');
        \Assert\Assertion::uuid($uuid, 'UUID is invalid');
        if (null !== $email) {
            \Assert\Assertion::notBlank($email, 'Email value is required');
            \Assert\Assertion::email($email, 'Email is invalid');
        }
        if (null !== $name) {
            \Assert\Assertion::notBlank($name, 'Name value is required');
        }
        $this->uuid = $uuid;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}
