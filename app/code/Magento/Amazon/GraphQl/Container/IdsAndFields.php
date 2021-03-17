<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Container;

use GraphQL\Type\Definition\ResolveInfo;

class IdsAndFields
{
    private $idsContainer;
    private $fieldsContainer;

    public function ids(): IdsContainer
    {
        if (null === $this->idsContainer) {
            $this->idsContainer = new IdsContainer();
        }

        return $this->idsContainer;
    }

    public function fields(): FieldsContainer
    {
        if (null === $this->fieldsContainer) {
            $this->fieldsContainer = new FieldsContainer();
        }

        return $this->fieldsContainer;
    }

    public function addSingle($id, ResolveInfo $info): void
    {
        $this->ids()->add($id);
        $this->fields()->addAll($info->getFieldSelection());
    }

    public function addSet(array $ids, ResolveInfo $info): void
    {
        $this->ids()->addAll($ids);
        $this->fields()->addAll($info->getFieldSelection());
    }
}
