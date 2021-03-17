<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl;

use Exception;
use GraphQL\GraphQL as GraphQlServer;
use Throwable;

class GraphQl
{
    /**
     * @var SchemaProvider
     */
    private $schemaProvider;

    /**
     * @var \Magento\Amazon\Logger\DebugLogging
     */
    private $debugLogging;

    public function __construct(
        \Magento\Amazon\GraphQl\SchemaProvider $schemaProvider,
        \Magento\Amazon\Logger\DebugLogging $debugLogging
    ) {
        $this->schemaProvider = $schemaProvider;
        $this->debugLogging = $debugLogging;
    }

    public function handle(?string $query, ?array $variables = [], ?string $operationName = null): string
    {
        try {
            $result = GraphQlServer::executeQuery(
                $this->schemaProvider->getSchema(),
                $query,
                null,
                new Context(),
                $variables,
                $operationName
            );
            return json_encode($result->toArray($this->debugLogging->isEnabled()));
        } catch (Exception $e) {
            return $this->formatError($e->getMessage());
        } catch (Throwable $e) {
            return $this->formatError($e->getMessage());
        }
    }

    public function formatError(string $message): string
    {
        return json_encode([
            'errors' => [
                [
                    'message' => $message
                ]
            ]
        ]);
    }
}
