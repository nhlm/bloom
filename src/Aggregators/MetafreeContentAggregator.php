<?php
/**
 * This file is part of the BLOOM Project.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Bloom\Aggregators;


use Bloom\Content;
use Bloom\ContentAggregatorInterface;
use Bloom\ContentInterface;

class MetafreeContentAggregator implements ContentAggregatorInterface
{
    /**
     * composes a content instance from the provided content.
     *
     * @param string $content
     * @return ContentInterface
     */
    public function patch(string $content): ContentInterface
    {
        return new Content([], $content);
    }

}