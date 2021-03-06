<?php declare(strict_types=1);

namespace LaravelJsonApi\NeomerxInline\JsonApi\Parser\RelationshipData;

/**
 * Copyright 2015-2020 info@neomerx.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use IteratorAggregate;
use LaravelJsonApi\NeomerxInline\JsonApi\Contracts\Factories\FactoryInterface;
use LaravelJsonApi\NeomerxInline\JsonApi\Contracts\Parser\EditableContextInterface;
use LaravelJsonApi\NeomerxInline\JsonApi\Contracts\Parser\RelationshipDataInterface;
use LaravelJsonApi\NeomerxInline\JsonApi\Contracts\Schema\IdentifierInterface;
use LaravelJsonApi\NeomerxInline\JsonApi\Contracts\Schema\PositionInterface;
use LaravelJsonApi\NeomerxInline\JsonApi\Contracts\Schema\SchemaContainerInterface;
use LaravelJsonApi\NeomerxInline\JsonApi\Contracts\Schema\SchemaInterface;
use LaravelJsonApi\NeomerxInline\JsonApi\Exceptions\InvalidArgumentException;
use LaravelJsonApi\NeomerxInline\JsonApi\Parser\IdentifierAndResource;
use Traversable;
use function Neomerx\JsonApi\I18n\format as _;

/**
 * @package Neomerx\JsonApi
 */
trait ParseRelationshipDataTrait
{
    /**
     * @param FactoryInterface         $factory
     * @param SchemaContainerInterface $container
     * @param EditableContextInterface $context
     * @param string                   $parentType
     * @param string                   $name
     * @param array                    $description
     * @param int                      $nextLevel
     * @param string                   $nextPathPrefix
     *
     * @return array [has data, parsed data, next position]
     */
    private function parseRelationshipData(
        FactoryInterface $factory,
        SchemaContainerInterface $container,
        EditableContextInterface $context,
        string $parentType,
        string $name,
        array $description,
        int $nextLevel,
        string $nextPathPrefix
    ): array {
        $hasData = \array_key_exists(SchemaInterface::RELATIONSHIP_DATA, $description);
        // either no data or data should be array/object/null
        \assert(
            $hasData === false ||
            (
                \is_array($data = $description[SchemaInterface::RELATIONSHIP_DATA]) === true ||
                \is_object($data) === true ||
                $data === null
            )
        );

        $nextPosition = $factory->createPosition(
            $nextLevel,
            $nextPathPrefix . $name,
            $parentType,
            $name
        );

        $relationshipData = $hasData === true ? $this->parseData(
            $factory,
            $container,
            $context,
            $nextPosition,
            $description[SchemaInterface::RELATIONSHIP_DATA]
        ) : null;

        return [$hasData, $relationshipData, $nextPosition];
    }

    /**
     * @param FactoryInterface         $factory
     * @param SchemaContainerInterface $container
     * @param EditableContextInterface $context
     * @param PositionInterface        $position
     * @param mixed                    $data
     *
     * @return RelationshipDataInterface
     */
    private function parseData(
        FactoryInterface $factory,
        SchemaContainerInterface $container,
        EditableContextInterface $context,
        PositionInterface $position,
        $data
    ): RelationshipDataInterface {
        // support if data is callable (e.g. a closure used to postpone actual data reading)
        if (\is_callable($data) === true) {
            $data = \call_user_func($data);
        }

        if ($container->hasSchema($data) === true) {
            return $factory->createRelationshipDataIsResource($container, $context, $position, $data);
        } elseif ($data instanceof IdentifierInterface) {
            return $factory->createRelationshipDataIsIdentifier($container, $context, $position, $data);
        } elseif (\is_array($data) === true) {
            return $factory->createRelationshipDataIsCollection($container, $context, $position, $data);
        } elseif ($data instanceof Traversable) {
            return $factory->createRelationshipDataIsCollection(
                $container,
                $context,
                $position,
                $data instanceof IteratorAggregate ? $data->getIterator() : $data
            );
        } elseif ($data === null) {
            return $factory->createRelationshipDataIsNull();
        }

        throw new InvalidArgumentException(
            _(IdentifierAndResource::MSG_NO_SCHEMA_FOUND, \get_class($data), $position->getPath())
        );
    }
}
