<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Cart2Quote\Quotation\Test\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Class CreateStoreTest
 *
 * @package Cart2Quote\Quotation\Test\Integration
 */
class CreateStoreTest extends TestCase
{
    /**
     * @magentoConfigFixture default_store quotation_general/general/cart2quote_enable 1
     * @magentoConfigFixture default_store cart2quote_quotation/global/enable 1
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoAppIsolation enabled
     */
    public function testCreateStore()
    {
        //This is just here to trigger the core_fixturestore
    }
}
