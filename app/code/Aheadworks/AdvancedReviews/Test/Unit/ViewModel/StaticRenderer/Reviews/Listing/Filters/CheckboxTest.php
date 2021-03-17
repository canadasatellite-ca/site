<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\ViewModel\StaticRenderer\Reviews\Listing\Filters;

use Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Listing\Filters\Checkbox;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Listing\Filters\Checkbox
 */
class CheckboxTest extends TestCase
{
    /**
     * @var Checkbox
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
            Checkbox::class,
            []
        );
    }

    /**
     * Test for getId method
     */
    public function testGetId()
    {
        $this->assertTrue(is_string($this->viewModel->getId()));
    }

    /**
     * Test for getLabel method
     */
    public function testGetLabel()
    {
        $this->assertTrue(is_string($this->viewModel->getLabel()));
    }
}
