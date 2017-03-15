# BLOOM
ParseDown based MarkDown Content Enrichment Component.

_This Library is Work In Progress_

## Introduction

Welcome to the BLOOM repository. BLOOM is a ParseDown based
MarkDown Content Enrichment Component that provides a lot of
different way to automatically migrate a markdown document
to a specific configured DOM.

Additionally to DOM Patching, BLOOM provides jekyll-aware
MarkDown front matter Meta Data support that allows to properly
read markdown content that has a preceding front matter.

## DOM contextual Patching via `PipeChain` pipelines and `DOMDocument

DOM Patching in BLOOM is pipeline based. You can chain
PipeChains to the `ContentFactory`-instance who receive
a `DocumentContext`-instance for DOM Manipulation as the
pipeline payload.

`DocumentContext` serves methods to directly query
CSS-based selectors and shorthands to modify class-attributes
on the elements queried.

## Front Matter Meta Data

BLOOM comes with different Content Aggregator who will
relocate `YAML`, `JSON` or `INI` contents from a front matter
definition to a array representation served as a
`Content`-instances that does provide access to the rendered
HTML and the Meta Data array.

The default Aggregator is the `MetafreeContentAggregator`
which will ignore front-matters. If you need front matter
aggregation for documents just provide a Content Aggregator
instance to the constructor of the `ContentFactory`.

## Maintainer, State of this Package and License

Those are the Maintainer of this Package:

- [Matthias Kaschubowski](https://github.com/nhlm)

This package is released under the MIT license. A copy of the license
is placed at the root of this repository.

The State of this Package is unstable unless unit tests are added.

## Todo

- Adding Unit Tests
- Finalizing the package