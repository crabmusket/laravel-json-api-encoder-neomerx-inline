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

class Post
{

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

    /**
     * @var User
     */
    public $author;

    /**
     * @var array
     */
    public $comments;

    /**
     * Post constructor.
     *
     * @param string $id
     * @param string $title
     * @param string $content
     * @param User|null $author
     */
    public function __construct(
        string $id,
        string $title,
        string $content = '...',
        User $author = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->author = $author ?: new User('123', 'Frankie Manning');
        $this->comments = [];
    }

    /**
     * @return string
     */
    public function getRouteKey(): string
    {
        return $this->id;
    }

    /**
     * @param Comment ...$comments
     * @return $this
     */
    public function withComments(Comment ...$comments): self
    {
        $this->comments = $comments;

        return $this;
    }
}
