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


use Bloom\ContentAggregatorInterface;
use Bloom\Exceptions\BloomException;

/**
 * Class IniPrecededContentAggregator
 * @package Bloom\Aggregators
 */
class IniPrecededContentAggregator extends AbstractContentAggregator implements ContentAggregatorInterface
{
    /**
     * forces to acclimate types of ini parameters
     */
    const TYPED = INI_SCANNER_TYPED;

    /**
     * behaves regular.
     */
    const COMMON = INI_SCANNER_NORMAL;

    /**
     * does provide unaltered data.
     */
    const RAW = INI_SCANNER_RAW;

    /**
     * @var int
     */
    protected $mode = INI_SCANNER_TYPED;

    /**
     * @var bool
     */
    protected $processSections = false;

    /**
     * IniPrecededContentAggregator constructor.
     * @param string|null $divider
     * @param bool $processSections
     * @param int|null $mode
     */
    public function __construct(string $divider = null , bool $processSections = false, int $mode = null)
    {
        $this->processSections = $processSections;
        $this->mode = $mode ?? $this->mode;

        parent::__construct($divider);
    }

    /**
     * aggregates the data array from the data string.
     *
     * @param string $data
     * @throws BloomException on failure
     * @return array
     */
    protected function aggregate(string $data): array
    {
        $result = parse_ini_string($data, $this->processSections, $this->mode);

        if ( $result === false ) {
            throw new BloomException('Failed to parse ini-alike head of document');
        }

        return $result;
    }


}