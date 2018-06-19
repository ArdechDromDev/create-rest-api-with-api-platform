<?php

namespace AppBundle\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class RandomFilter extends AbstractFilter
{
    const PROPERTY_NAME = 'random';

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== self::PROPERTY_NAME) {
            return false;
        }

        $queryBuilder
            ->addSelect('RAND() as HIDDEN rand')
            ->addOrderBy('rand');

        return true;
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [
            'random' => [
                'property' => 'random',
                'type' => 'boolean',
                'required' => false,
                'swagger' => [
                    'description' => 'Apply random filter.',
                    'name' => 'Random',
                    'type' => 'boolean',
                ],
            ],
        ];

        return $description;
    }
}
