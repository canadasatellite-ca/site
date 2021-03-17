<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\WrappingType;
use GraphQL\Type\Schema;
use GraphQL\Utils\BuildSchema;
use Magento\Amazon\Model\Amazon\Definitions;

class SchemaProvider
{
    /**
     * @var ResolverProvider
     */
    private $resolverProvider;

    public function __construct(
        ResolverProvider $resolverProvider
    ) {
        $this->resolverProvider = $resolverProvider;
    }

    public function getSchema(): Schema
    {
        $typeConfigDecorator = function ($typeConfig, $typeDefinitionNode) {
            $type = $typeConfig['name'];
            if ($type === 'CountryCode') {
                $typeConfig['values'] = $this->getCountryCodes();
            }
            if ($type === 'AmazonIdType') {
                $typeConfig['values'] = Definitions::getAmazonIdTypes();
            }
            $typeConfig['resolveField'] = function (
                $value,
                $args,
                $context,
                ResolveInfo $info
            ) use ($type) {
                $returnType = $info->returnType instanceof WrappingType
                    ? $info->returnType->getWrappedType(true)->name
                    : $info->returnType->name;

                $result = $this->resolveFieldValue($type, $value, $args, $context, $info);

                $resultTransformer = $this->resolverProvider->getResultTransformer($returnType);
                return $resultTransformer
                    ? $resultTransformer->resolve($result, $value, $args, $context, $info)
                    : $result;
            };
            return $typeConfig;
        };

        // @todo: cache parsed schema: https://webonyx.github.io/graphql-php/type-system/type-language/#performance-considerations
        return BuildSchema::build(file_get_contents(__DIR__ . '/../schema.graphqls'), $typeConfigDecorator);
    }

    private function resolveFieldValue(string $type, $value, $args, $context, ResolveInfo $info)
    {
        if ($this->getFieldDirective('proxy', $info)) {
            return $value;
        }

        $field = $info->fieldName;
        if ($type === 'Query') {
            $resolver = $this->resolverProvider->getQueryFieldResolver($field);
            return $resolver ? $resolver->resolve($value, $args, $context, $info) : null;
        }
        if ($type === 'Mutation') {
            $resolver = $this->resolverProvider->getMutationResolver($field);
            return $resolver ? $resolver->resolve($value, $args, $context, $info) : null;
        }
        $resolver = $this->resolverProvider->getFieldResolver($type, $field);
        if ($resolver) {
            return $resolver->resolve($value, $args, $context, $info);
        }

        if (is_array($value)) {
            return $value[$field] ?? null;
        }

        if ($value instanceof \ArrayAccess) {
            return $value->offsetExists($field) ? $value->offsetGet($field) : null;
        }
        return null;
    }

    private function getCountryCodes(): array
    {
        $countryCodes = [];
        foreach (Definitions::getEnabledMarketplaces() as $marketplace) {
            $countryCodes[$marketplace['countryCode']] = ['description' => $marketplace['name']];
        }
        return $countryCodes;
    }

    private function getFieldDirective(string $directiveName, ResolveInfo $info): ?array
    {
        $directiveData = null;
        $field = $info->fieldName;
        $directives = $info->parentType->getField($field)->astNode->directives;
        foreach ($directives as $directive) {
            if ($directive->name->value === $directiveName) {
                $directiveData = ['directive' => $directiveName];
                foreach ($directive->arguments as $argument) {
                    $directiveData[$argument->name->value] = $argument->value->value;
                }
            }
        }
        return $directiveData;
    }
}
