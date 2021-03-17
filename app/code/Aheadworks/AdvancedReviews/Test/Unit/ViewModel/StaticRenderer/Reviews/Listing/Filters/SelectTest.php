<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\ViewModel\StaticRenderer\Reviews\Listing\Filters;

use Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Listing\Filters\Select;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Listing\Filters\Checkbox
 */
class SelectTest extends TestCase
{
    /**
     * @var Select
     */
    private $viewModel;

    /**
     * @var OptionSourceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionsProviderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->optionsProviderMock = $this->createMock(OptionSourceInterface::class);

        $this->viewModel = $objectManager->getObject(
            Select::class,
            [
                'id' => 'filter_id',
                'optionsProvider' => $this->optionsProviderMock,
            ]
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
     * Test for getOptions method
     */
    public function testGetOptions()
    {
        $options = [];
        $this->optionsProviderMock->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($options);
        $this->assertTrue(is_array($this->viewModel->getOptions()));
    }

    /**
     * Test for getOptionValue method
     *
     * @param array $optionData
     * @param string $result
     * @dataProvider getOptionValueDataProvider
     */
    public function testGetOptionValue($optionData, $result)
    {
        $this->assertEquals($result, $this->viewModel->getOptionValue($optionData));
    }

    /**
     * @return array
     */
    public function getOptionValueDataProvider()
    {
        return [
            [
                'optionData' => [],
                'result' => '',
            ],
            [
                'optionData' => [
                    'value' => null,
                ],
                'result' => '',
            ],
            [
                'optionData' => [
                    'value' => '',
                ],
                'result' => '',
            ],
            [
                'optionData' => [
                    'value' => 'option value',
                ],
                'result' => 'option value',
            ],
        ];
    }

    /**
     * Test for getOptionLabel method
     *
     * @param array $optionData
     * @param string $result
     * @dataProvider getOptionLabelDataProvider
     */
    public function testGetOptionLabel($optionData, $result)
    {
        $this->assertEquals($result, $this->viewModel->getOptionLabel($optionData));
    }

    /**
     * @return array
     */
    public function getOptionLabelDataProvider()
    {
        return [
            [
                'optionData' => [],
                'result' => '',
            ],
            [
                'optionData' => [
                    'label' => null,
                ],
                'result' => '',
            ],
            [
                'optionData' => [
                    'label' => '',
                ],
                'result' => '',
            ],
            [
                'optionData' => [
                    'label' => 'option label',
                ],
                'result' => 'option label',
            ],
        ];
    }
}
