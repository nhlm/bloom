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


use Bloom\Aggregators\MetafreeContentAggregator;
use Bloom\Exceptions\BloomException;
use PipeChain\PipeChainInterface;

/**
 * Class ContentFactory
 * @package Bloom
 */
class ContentFactory implements ContentFactoryInterface
{
    /**
     * @var ContentAggregatorInterface
     */
    private $contentAggregator;

    /**
     * @var PipeChainInterface
     */
    private $metaDataChain;
    
    /**
     * @var PipeChainInterface
     */
    private $contentDataChain;

    /**
     * @var \ParsedownExtra
     */
    private $markdown;

    /**
     * ContentFactory constructor.
     * @param ContentAggregatorInterface|null $contentAggregator
     */
    public function __construct(ContentAggregatorInterface $contentAggregator = null)
    {
        $this->contentAggregator = $contentAggregator ?? new MetafreeContentAggregator();
        $this->markdown = new \ParsedownExtra();
    }

    /**
     * chains a pipeline to the meta data pipeline.
     *
     * @param PipeChainInterface[] ...$pipeChain
     * @return ContentFactoryInterface
     */
    public function withMetaDataPipelines(PipeChainInterface ...$pipeChain): ContentFactoryInterface
    {
        foreach ( $pipeChain as $current ) {
            if ( ! $this->metaDataChain instanceof PipeChainInterface ) {
                $this->metaDataChain = $current;
                continue;
            }

            $this->metaDataChain->chain($current);
        }

        return $this;
    }

    /**
     * chains a pipeline to the content pipeline.
     *
     * @param PipeChainInterface[] ...$pipeChain
     * @return ContentFactoryInterface
     */
    public function withContentPipelines(PipeChainInterface ...$pipeChain): ContentFactoryInterface
    {
        foreach ( $pipeChain as $current ) {
            if ( ! $this->contentDataChain instanceof PipeChainInterface ) {
                $this->contentDataChain = $current;
                continue;
            }

            $this->contentDataChain->chain($current);
        }

        return $this;
    }

    /**
     * processes a markdown-alike string.
     *
     * @param string $inbound
     * @throws BloomException when something went wrong
     * @return ContentInterface
     */
    public function process(string $inbound): ContentInterface
    {
        $contentObject = $this->contentAggregator->patch($inbound);

        if ( ! $this->contentDataChain && ! $this->metaDataChain ) {
            return new Content(
                $contentObject->getMetaData(),
                $this->markdown->text($contentObject->getContent())
            );
        }

        $metaData = $contentObject->getMetaData();
        $content = $this->markdown->text($contentObject->getContent());

        if ( $this->metaDataChain instanceof PipeChainInterface ) {
            $metaData = $this->metaDataChain->process($metaData);

            if ( ! is_array($metaData) ) {
                throw new BloomException('The meta data process chain must result in an array');
            }
        }

        if ( $this->contentDataChain instanceof PipeChainInterface ) {
            $dom = new \DOMDocument('1.0', 'utf-8');
            $dom->loadHTML($content);
            $body = $dom->getElementsByTagName('body')->item(0);

            $context = new DocumentContext($metaData, $body);

            $processedBody = $this->metaDataChain->process($context);

            if ( ! $processedBody instanceof \DOMElement ) {
                throw new BloomException('The content process chain must result in a HTML body representing DOMElement');
            }

            if ( ! $processedBody->tagName === 'body' ) {
                throw new BloomException('The content process chain resulting tag must be a body tag');
            }

            $content = '';

            foreach ( range(0, $processedBody->childNodes->length - 1) as $node ) {
                $content .= PHP_EOL.$dom->saveHTML($node);
            }

            $content = ltrim($content, PHP_EOL);
        }

        return new Content($metaData, $content);
    }

    /**
     * processes markdown-alike contents of a file.
     *
     * @param \SplFileInfo $file
     * @throws BloomException when the provided file is not compatible
     * @return ContentInterface
     */
    public function processFile(\SplFileInfo $file): ContentInterface
    {
        if ( ! $file->isFile() ) {
            throw new BloomException('Provided file object points not to a file');
        }

        return $this->process(file_get_contents($file->getPathname()));
    }

    /**
     * processes a markdown-alike string into a document instance.
     *
     * @param string $inbound
     * @param string|null $documentClass
     * @return DocumentInterface
     */
    public function processIntoDocument(string $inbound, string $documentClass = null): DocumentInterface
    {
        return $this->process($inbound)->intoDocument($documentClass);
    }

    /**
     * processes markdown-alike contents of a file into a document instance.
     *
     * @param \SplFileInfo $file
     * @param string|null $documentClass
     * @return DocumentInterface
     */
    public function processFileIntoDocument(\SplFileInfo $file, string $documentClass = null): DocumentInterface
    {
        return $this->processFile($file)->intoDocument($documentClass);
    }

}