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


use Bloom\Exceptions\BloomException;

/**
 * Class JsonPrecededContentAggregator
 * @package Bloom\Aggregators
 */
class JsonPrecededContentAggregator extends AbstractContentAggregator
{
    /**
     * @var bool
     */
    private $forceArray;
    /**
     * @var int
     */
    private $depth = 512;

    /**
     * JsonPrecededContentAggregator constructor.
     * @param null $divider
     * @param bool $forceArray
     * @param int $depth
     */
    public function __construct($divider = null, bool $forceArray = true, int $depth = 512)
    {
        $this->forceArray = $forceArray;
        $this->depth = $depth;

        parent::__construct($divider);
    }

    /**
     * aggregates the data array from the data string.
     *
     * @param string $data
     * @throws BloomException when the decoding failed
     * @return array
     */
    protected function aggregate(string $data): array
    {
        $result = json_decode($data, $this->forceArray, $this->depth, JSON_BIGINT_AS_STRING);

        if ( $result === false ) {
            throw new BloomException('Failed to aggregate array from JSON string');
        }

        return $result;
    }

}