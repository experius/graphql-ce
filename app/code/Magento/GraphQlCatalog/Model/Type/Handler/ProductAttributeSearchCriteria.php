<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GraphQlCatalog\Model\Type\Handler;

use Magento\GraphQl\Model\EntityAttributeList;
use Magento\GraphQl\Model\Type\HandlerInterface;
use Magento\Framework\GraphQl\TypeFactory;
use Magento\GraphQl\Model\Type\Handler\Pool;
use Magento\GraphQl\Model\Type\Handler\SearchCriteriaExpression;

/**
 * Define ProductAttributeSearchCriteria's GraphQL type
 */
class ProductAttributeSearchCriteria implements HandlerInterface
{
    const PRODUCT_ATTRIBUTE_SEARCH_CRITERIA_TYPE_NAME = 'ProductAttributeSearchCriteria';

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var EntityAttributeList
     */
    private $entityAttributeList;

    /**
     * @var TypeFactory
     */
    private $typeFactory;

    /**
     * @param Pool $pool
     * @param EntityAttributeList $entityAttributeList
     * @param TypeFactory $typeFactory
     */
    public function __construct(
        Pool $pool,
        EntityAttributeList $entityAttributeList,
        TypeFactory $typeFactory
    ) {
        $this->pool = $pool;
        $this->entityAttributeList = $entityAttributeList;
        $this->typeFactory = $typeFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        $reflector = new \ReflectionClass($this);
        return $this->typeFactory->createInputObject(
            [
                'name' => $reflector->getShortName(),
                'fields' => $this->getFields()
            ]
        );
    }

    /**
     * Retrieve fields
     *
     * @return \Closure|array
     */
    private function getFields()
    {
        $productAttributeSearchCriteriaClassName = self::PRODUCT_ATTRIBUTE_SEARCH_CRITERIA_TYPE_NAME;
        $attributes = $this->entityAttributeList->getDefaultEntityAttributes(\Magento\Catalog\Model\Product::ENTITY);
        $schema = [];
        foreach ($attributes as $attribute) {
            $schema[$attribute->getAttributeCode()] = $this->pool->getType(
                SearchCriteriaExpression::SEARCH_CRITERIA_EXPRESSION_TYPE_NAME
            );
        }

        $fields = function () use ($schema, $productAttributeSearchCriteriaClassName) {
            $schema = array_merge($schema, ['or' => $this->pool->getType($productAttributeSearchCriteriaClassName)]);

            return $schema;
        };

        return $fields;
    }
}
