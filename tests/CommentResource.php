<?php
/**
 * Copyright 2020 Cloud Creativity Limited
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

declare(strict_types=1);

namespace LaravelJsonApi\Encoder\Neomerx\Tests;

use LaravelJsonApi\Core\Document\ResourceObject\Identifier;
use LaravelJsonApi\Core\Document\ResourceObject\ToOne;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class CommentResource extends JsonApiResource
{
    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return 'comments';
    }

    /**
     * @inheritDoc
     */
    public function attributes(): iterable
    {
        return [
            'content' => $this->content,
        ];
    }

    /**
     * @inheritDoc
     */
    public function relationships(): iterable
    {
        return [
            'post' => new ToOne(
                new Identifier('posts', $this->post->getRouteKey()),
                true,
                $this->selfUrl(),
                'post'
            ),
            'user' => new ToOne(
                $this->user,
                true,
                $this->selfUrl(),
                'user'
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function selfUrl(): string
    {
        return sprintf(
            'http://example.com/api/v1/%s/%s',
            $this->type(),
            $this->id()
        );
    }


}
