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


use PipeChain\PipeChainAwareInterface;

interface ContentInterface
{
    /**
     * returns an array of meta data of the document.
     *
     * @return array
     */
    public function getMetaData(): array;

    /**
     * returns the string of the document.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * aggregates a document instance with the provided content.
     *
     * @param string|null $documentClass
     * @return DocumentInterface
     */
    public function intoDocument(string $documentClass = null): DocumentInterface;
}