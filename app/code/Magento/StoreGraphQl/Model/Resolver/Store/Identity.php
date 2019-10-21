<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\StoreGraphQl\Model\Resolver\Store;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

/**
 * Class Identity
 *
 * @package Magento\StoreGraphQl\Model\Resolver\Store
 */
class Identity implements IdentityInterface
{
    /**
     * @var string
     */
    private $cacheTag = \Magento\Framework\App\Config::CACHE_TAG;

    /**
     * @inheritDoc
     */
    public function getIdentities(array $resolvedData): array
    {
        $ids =  empty($resolvedData) ?
            [] : array_merge([$this->cacheTag], array_map(function ($key) { return sprintf('%s_%s', $this->cacheTag, $key); }, array_keys($resolvedData)));
        return $ids;
    }
}
