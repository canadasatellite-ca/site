<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl;

use DomainException;
use Magento\Amazon\GraphQl\Resolver\ResolverInterface;
use Magento\Amazon\GraphQl\Resolver\ResultTransformerInterface;

class ResolverProvider
{
    /**
     * @var array|ResolverInterface[]
     */
    private $queries;
    /**
     * @var array|ResolverInterface[]
     */
    private $mutations;
    /**
     * @var array|ResolverInterface[]
     */
    private $fields;
    /**
     * @var array|ResultTransformerInterface[]
     */
    private $resultTransformers;

    /**
     * @param ResolverInterface[] $queries
     * @param ResolverInterface[] $mutations
     * @param array $fields
     * @param $resultTransformers
     */
    public function __construct(
        array $queries = [],
        array $mutations = [],
        array $fields = [],
        array $resultTransformers = []
    ) {
        $this->queries = $this->validateResolvers('query', $queries);
        $this->mutations = $this->validateResolvers('mutation', $mutations);
        $this->fields = $this->validateResolvers('field', $fields);
        $this->resultTransformers = $this->validateResultTransformers($resultTransformers);
    }

    private function validateResolvers(string $type, array $resolvers): array
    {
        foreach ($resolvers as $name => $resolver) {
            if (!$resolver instanceof ResolverInterface) {
                throw new DomainException(
                    'Provided ' . $type . ' resolver ' . $name . ' does not implement ' . ResolverInterface::class
                );
            }
        }
        return $resolvers;
    }

    private function validateResultTransformers(array $resolvers): array
    {
        foreach ($resolvers as $name => $resolver) {
            if (!$resolver instanceof ResultTransformerInterface) {
                throw new DomainException(
                    'Provided result transformer ' . $name . ' does not implement ' . ResultTransformerInterface::class
                );
            }
        }
        return $resolvers;
    }

    /**
     * @param string $name
     * @return ResolverInterface|null
     */
    public function getMutationResolver(string $name): ?ResolverInterface
    {
        return $this->mutations[$name] ?? null;
    }

    public function getQueryFieldResolver(string $name): ?ResolverInterface
    {
        return $this->queries[$name] ?? null;
    }

    public function getFieldResolver(string $type, string $field): ?ResolverInterface
    {
        $name = "$type.$field";
        return $this->fields[$name] ?? null;
    }

    public function getResultTransformer(string $returnType): ?ResultTransformerInterface
    {
        return $this->resultTransformers[$returnType] ?? null;
    }
}
