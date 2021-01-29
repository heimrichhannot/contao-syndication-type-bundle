# Contao Syndication Config Element Type Bundle

This bundle brings an extendable syndication framework to contao. Syndication can be easily added to your own bundle/module/element. There are already some bundles/entites supported out-of-the-box (see features section).

## Features
- add syndication support for
    - [Reader Bundle](https://github.com/heimrichhannot/contao-reader-bundle)
- bundles syndication types:
    - sharing:
        - facebook
    - export:
        - todo
- expandable and customizable syndication framework
- generated links and link lists implementing [PSR-13](https://www.php-fig.org/psr/psr-13/) `LinkInterface` and `LinkProviderInterface`

## Usage

### Install

1. Install with composer or contao manager
1. Update database

### Reader Bundle

1. Create a new reader config element of type syndication
1. Select syndications
1. output the template variable in your template (with raw filter)

## Developers

### Add syndications a custom data container

1. Add fields with `DcaFieldProvider` service to your dca
1. Generate syndication links in your module/content element/controller/eventlistener/etc. with `SyndicationLinkProviderGenerator`. 
1. Render links with bundled `SyndicationLinkRenderer` or a custom renderer

### Add custom syndication type

1. Create a class implementing `SyndicationTypeInterface` (we recommend extending `AbstractSyndicationType`)
1. Register service type class as service with `huh.syndication_type.type` service tag



