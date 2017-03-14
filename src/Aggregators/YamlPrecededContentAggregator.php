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
 * Class YamlPrecededContentAggregator
 * @package Bloom\Aggregators
 */
class YamlPrecededContentAggregator extends AbstractContentAggregator
{
    /**
     *
     */
    const SYMFONY = 0;
    /**
     *
     */
    const EXTENSION = 1;

    /**
     * @var int
     */
    private $preferredLibrary;

    /**
     * YamlPrecededContentAggregator constructor.
     * @param null $divider
     * @param int|null $preferredLibrary
     */
    public function __construct($divider = null, int $preferredLibrary = null)
    {
        $this->preferredLibrary = $preferredLibrary ?? static::SYMFONY;

        parent::__construct($divider);
    }


    /**
     * aggregates the data array from the data string.
     *
     * @param string $data
     * @throws BloomException when something went wrong parsing the meta data as YAML
     * @return array
     */
    protected function aggregate(string $data): array
    {
        if ( $this->preferredLibrary === static::SYMFONY && class_exists('Symfony\\Components\\Yaml\\Yaml', true) ) {
            try {
                return \Symfony\Components\Yaml\Yaml::parse($data);
            }
            catch ( \Throwable $exception ) {
                throw new BloomException(
                    'Something went wrong parsing YAML using symfony`s YAML component',
                    500,
                    $exception
                );
            }
        }

        if ( $this->preferredLibrary === static::EXTENSION && extension_loaded('yaml') ) {
            $result = yaml_parse($data);

            if ( $result === false ) {
                throw new BloomException('Something went wrong parsing YAML using PHP`s YAML extension');
            }

            return $result;
        }

        throw new BloomException('Your preferred YAML library is not available');
    }


}