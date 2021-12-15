# Changelog
All notable changes to this project will be documented in this file. 

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.5.0] - 2021-12-15
- Added: [BREAKING!] SyndicationLinkRendererContext parameter to SyndicationLinkRenderer::renderProvider()
- Added: [BREAKING!] SyndicationLink type property
- Fixed: IcsExportSyndication not using SyndicationLinkFactory

## [0.4.0] - 2021-08-27
- Added: BeforeRenderSyndicationLinks to customize link list and link render options
- Added: twig template for link provider
- Changed: use TwigTemplateRenderer for rendering link templates
- Removed: disable_indexer_comments option on SyndicationLinkRenderer::renderProvider() as it is now customizable from template

## [0.3.5] - 2021-08-26
- Changed: allowed some symfony 5 packages
- Fixed: hard reader bundle dependency
- Fixed: added missing symfony/config dependency

## [0.3.4] - 2021-08-10
- made services public
- fixed exception if title or description fields are empty in SyndicationConfigElementType

## [0.3.3] - 2021-07-21
- added render_callback option to `SyndicationLinkRenderer::renderProvider()`
- added Customize link rendering to readme
- fixed missing translations for fields introduced in 0.3.2

## [0.3.2] - 2021-07-21
- added street, postal and city field select to ics syndication type

## [0.3.1] - 2021-06-22
- added new syndication types: xing, linkedin
- moved content element creation to InitilizeSystemListner

## [0.3.0] - 2021-06-14
- added content element syndication type(#1)

## [0.2.4] - 2021-06-11
- fixed SyndicationTypeDcaProvider not to override all subpalettes

## [0.2.3] - 2021-06-08
- fixed PrintSyndicatonType and ConfigElementTypeDcaProvider to use correct symdony TranslatorInterface

## [0.2.2] - 2021-05-10
- allow twig support bundle ^1.0

## [0.2.1] - 2021-04-13
- upgraded dependencies
- fixed ReaderBeforeRenderEventSubscriber

## [0.2.0] - 2021-04-06
- added WhatsApp syndication
- changed: added span to default link template
- changed: added syndication-link and syndication-link__text classes to default link element
- changed link relations now only rendered when set as relations in a SyndicationLink in SyndicationLinkRenderer
- changed some relations to use only valid relations types and build-in constants

## [0.1.1] - 2021-03-09
- added minimal supported version for reader bundle

## [0.1.0] - 2021-03-09
Initial release
