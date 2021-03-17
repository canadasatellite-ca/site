<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Cart2Quote\Quotation\Test\Integration\Controller;

use Magento\TestFramework\TestCase\AbstractController;

/**
 * Class DispatchTest
 *
 * @package Cart2Quote\Quotation\Test\Integration\Controller
 */
class DispatchTest extends AbstractController
{
    /**
     * @magentoConfigFixture default_store quotation_general/general/cart2quote_enable 1
     * @magentoConfigFixture default_store cart2quote_quotation/global/enable 1
     * @magentoConfigFixture default_store cart2quote_quotation/global/show_sidebar 1
     * @magentoAppArea frontend
     */
    public function testHomepage()
    {
        $this->dispatch('');
        $this->assertContains('miniquote', $this->getResponse()->getBody());
    }
}
