<?php
/**
 * This file is part of the BLOOM Project.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Bloom;


use Bloom\Exceptions\BloomException;
use Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * Class PipelinePayload
 * @package Bloom
 */
class DocumentContext implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $metaData = [];
    /**
     * @var \DOMElement
     */
    protected $context;

    /**
     * @var CssSelectorConverter
     */
    protected $cssSelector;

    /**
     * @var \DOMXPath
     */
    protected $xpath;

    /**
     * PipelinePayload constructor.
     * @param array $metaData
     * @param \DOMElement $context
     */
    public function __construct(array $metaData, \DOMElement $context)
    {
        $this->metaData = $metaData;
        $this->context = $context;
        $this->cssSelector = new CssSelectorConverter();
        $this->xpath = new \DOMXPath($context->ownerDocument);
    }

    /**
     * returns the context element of the current document context.
     *
     * @return \DOMElement
     */
    public function getContext(): \DOMElement
    {
        return $this->context;
    }

    /**
     * queries the DOM in the current context by the provided CSS selector.
     *
     * @param string $selector
     * @return \DOMNodeList
     */
    public function querySelector(string $selector): \DOMNodeList
    {
        return $this->xpath->query($this->cssSelector->toXPath($selector), $this->context);
    }

    /**
     * queries the DOM in the current context by the provided XPATH selector.
     *
     * @param string $selector
     * @return \DOMNodeList
     */
    public function xpath(string $selector): \DOMNodeList
    {
        return $this->xpath->query($selector, $this->context);
    }

    /**
     * forces a set of classes to be set to elements who match the provided CSS selector.
     *
     * @param string $selector
     * @param \string[] ...$classes
     */
    public function forceClasses(string $selector, string ... $classes)
    {
        $this->each($selector, function(DocumentContext $context) use ($classes) {
            $context->getContext()->setAttribute('class', join(' ', $classes));
        });
    }

    /**
     * adds a class to elements who match the provided CSS selector.
     *
     * @param string $selector
     * @param string $class
     */
    public function addClass(string $selector, string $class)
    {
        $this->each($selector, function(DocumentContext $context) use ($class) {
            $current = $context->getContext()->getAttribute('class');
            $context->getContext()->setAttribute('class', join(' ', array_filter([$current, $class])));
        });
    }

    /**
     * removes a class from elements who match the provided CSS selectors. Empty class attributes are
     * automatically removed.
     *
     * @param string $selector
     * @param string $class
     */
    public function removeClass(string $selector, string $class)
    {
        $this->each($selector, function(DocumentContext $context) use ($class) {
            $current = $context->getContext()->getAttribute('class');
            $list = array_filter(explode(' ', $current), function(string $in) use ($class) {
                return $in !== $class && ! empty(trim($in));
            });

            if ( empty($list) ) {
                $context->getContext()->removeAttribute('class');
            }
            else {
                $context->getContext()->setAttribute('class', join(' ', $list));
            }
        });
    }

    /**
     * iterates the callback on the node list of the result based on the provided CSS selector.
     * Each callback call has a DocumentContext instance with the element as its context as the first parameter.
     * All return values are ignored.
     *
     * @param string $selector
     * @param callable $callback
     */
    public function each(string $selector, callable $callback)
    {
        foreach ( $this->querySelector($selector) as $current ) {
            $context = clone $this;
            $context->context = $current;

            $callback($context);
            unset($context);
        }
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->metaData);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->metaData[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws BloomException
     */
    public function offsetSet($offset, $value)
    {
        throw new BloomException('You can not modify the aggregated meta data in this context');
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws BloomException
     */
    public function offsetUnset($offset)
    {
        throw new BloomException('You can not modify the aggregated meta data in this context');
    }

}