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

class Content implements ContentInterface
{
    protected $metaData = [];
    protected $content = "";

    public function __construct(array $metaData, string $content)
    {
        $this->metaData = $metaData;
        $this->content = $content;
    }

    /**
     * returns an array of meta data of the document.
     *
     * @return array
     */
    public function getMetaData(): array
    {
        return $this->metaData;
    }

    /**
     * returns the string of the document.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * aggregates a document instance with the provided content.
     *
     * @param string|null $documentClass
     * @throws BloomException when the documentClass parameter does hold a document class name that is incompatible
     * @return DocumentInterface
     */
    public function intoDocument(string $documentClass = null): DocumentInterface
    {
        $documentClass = $documentClass ?? Document::class;

        if ( ! is_a($documentClass, DocumentInterface::class, true) ) {
            throw new BloomException('Provided document class must be instance of Bloom\\DocumentInterface');
        }

        return new $documentClass($this);
    }

}