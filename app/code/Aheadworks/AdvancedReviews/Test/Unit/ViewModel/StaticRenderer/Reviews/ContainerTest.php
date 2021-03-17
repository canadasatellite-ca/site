<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\ViewModel\StaticRenderer\Reviews;

use Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Container;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Container
 */
class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    private $viewModel;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->viewModel = $objectManager->getObject(
            Container::class,
            []
        );
    }

    /**
     * Test for getBlockIdentities method
     */
    public function testGetBlockIdentities()
    {
        $this->assertTrue(is_array($this->viewModel->getBlockIdentities()));
    }
}
