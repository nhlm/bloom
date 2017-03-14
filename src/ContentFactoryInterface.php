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


use PipeChain\PipeChainInterface;

interface ContentFactoryInterface
{
    /**
     * chains a pipeline to the meta data pipeline.
     *
     * @param PipeChainInterface[] ...$pipeChain
     * @return ContentFactoryInterface
     */
    public function withMetaDataPipelines(PipeChainInterface ...$pipeChain): ContentFactoryInterface;

    /**
     * chains a pipeline to the content pipeline.
     *
     * @param PipeChainInterface[] ...$pipeChain
     * @return ContentFactoryInterface
     */
    public function withContentPipelines(PipeChainInterface ...$pipeChain): ContentFactoryInterface;

    /**
     * processes a markdown-alike string.
     *
     * @param string $inbound
     * @return ContentInterface
     */
    public function process(string $inbound): ContentInterface;

    /**
     * processes markdown-alike contents of a file.
     *
     * @param \SplFileInfo $file
     * @return ContentInterface
     */
    public function processFile(\SplFileInfo $file): ContentInterface;

    /**
     * processes a markdown-alike string into a document instance.
     *
     * @param string $inbound
     * @param string|null $documentClass
     * @return DocumentInterface
     */
    public function processIntoDocument(string $inbound, string $documentClass = null): DocumentInterface;

    /**
     * processes markdown-alike contents of a file into a document instance.
     *
     * @param \SplFileInfo $file
     * @param string|null $documentClass
     * @return DocumentInterface
     */
    public function processFileIntoDocument(\SplFileInfo $file, string $documentClass = null): DocumentInterface;
}