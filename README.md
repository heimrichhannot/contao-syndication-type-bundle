# Contao Syndication Type Bundle

This bundle brings an extendable syndication framework to contao. Syndication can be easily added to your own bundle/module/element. There are already some bundles/entites supported out-of-the-box (see features section).

## Features
- out-of-the-box syndication support for
    - [Reader Bundle](https://github.com/heimrichhannot/contao-reader-bundle)
- bundled syndication types:
    - sharing: facebook, email, email feedback, twitter
    - export: ical
- expandable and customizable syndication framework
    - add pdf export through [PDF Creator Bundle](https://github.com/heimrichhannot/contao-pdf-creator-bundle)
- generated links and link lists implementing [PSR-13](https://www.php-fig.org/psr/psr-13/) `LinkInterface` and `LinkProviderInterface`

## Usage

### Requirements
- php ^7.2
- contao ^4.4
- for iCal (.ics) export: `eluceo/ical (^0.16) `
- for PDF export: `heimrichhannot/contao-pdf-creator-bundle`

### Install

1. Install with composer or contao manager
1. Update database

### Reader Bundle

1. Create a new reader config element of type syndication
1. Select syndications
1. output the template variable in your template (with raw filter)

## Developers

### Add syndications a custom data container

1. Add fields with `SyndicationTypeDcaProvider` service to your dca
1. Generate syndication links in your module/content element/controller/eventlistener/etc. with `SyndicationLinkProviderGenerator`. 
1. Render links with bundled `SyndicationLinkRenderer` or a custom renderer

### Add custom syndication type

1. Create a class implementing `SyndicationTypeInterface` (we recommend extending `AbstractSyndicationType`)
1. Register service type class as service with `huh.syndication_type.type` service tag
1. Create a Event Subscriber for `AddSyndicationTypeFieldsEvent`,`AddSyndicationTypePaletteSelectorsEvent` and `AddSyndicationTypeSubpalettesEvent` to add custom dca fields and subpalettes



