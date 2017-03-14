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

abstract class AbstractContentAggregator implements ContentAggregatorInterface
{
    const DEFAULT_DIVIDER = "\n---\n";

    private $divider;

    public function __construct(string $divider = null)
    {
        $this->divider = $divider ?? static::DEFAULT_DIVIDER;
    }

    /**
     * composes a content instance from the provided content.
     *
     * @param string $content
     * @return ContentInterface
     */
    public function patch(string $content): ContentInterface
    {
        list ( $data, $contents ) = explode($this->divider, $content, 1);

        return new Content($this->aggregate(trim($data)), ltrim($contents));
    }

    /**
     * aggregates the data array from the data string.
     *
     * @param string $data
     * @return array
     */
    abstract protected function aggregate(string $data): array;
}