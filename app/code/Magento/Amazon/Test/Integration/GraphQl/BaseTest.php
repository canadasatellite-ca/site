<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Test\Integration\GraphQl;

abstract class BaseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Amazon\GraphQl\GraphQl
     */
    private $graphQl;
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->graphQl = $this->objectManager->get(\Magento\Amazon\GraphQl\GraphQl::class);
    }

    protected function query(?string $query, ?array $variables = [], ?string $operationName = null): array
    {
        $response = $this->graphQl->handle($query, $variables, $operationName);
        return json_decode($response, true);
    }

    protected function assertHasNoError(array $result)
    {
        $this->assertArrayNotHasKey(
            'errors',
            $result,
            'Response contains errors: ' . json_encode($result['errors'] ?? [])
        );
    }

    protected function getChildNode(array $data, string $path, string $fullPath = null)
    {
        if ($fullPath === null) {
            $fullPath = $path;
        }
        $path = trim($path);
        $this->assertNotEmpty($path);
        $childNodes = explode('.', $path);
        if (count($childNodes) === 1) {
            $this->assertArrayHasKey($path, $data, 'Child node ' . $path . ' does not exist in path ' . $fullPath);
            return $data[$path];
        }
        $nextNode = $childNodes[0];
        $this->assertArrayHasKey($nextNode, $data, 'Child node ' . $nextNode . ' does not exist in path ' . $fullPath);
        $restOfPath = array_slice($childNodes, 1);
        return $this->getChildNode($data[$nextNode], implode('.', $restOfPath), $fullPath);
    }

    protected function getFixtureData(string $name)
    {
        $filename = pathinfo($name, PATHINFO_FILENAME);
        $extension = pathinfo($name, PATHINFO_EXTENSION) ?: 'php';
        $path = sprintf('%s/../fixtures/data/%s.%s', __DIR__, $filename, $extension);
        $this->assertFileExists($path);
        if ($extension === 'php') {
            return require $path;
        }
        return file_get_contents($path);
    }
}
