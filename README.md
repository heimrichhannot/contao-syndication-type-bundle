# Contao Syndication Type Bundle

This bundle brings an extendable syndication framework to contao. Syndication can be easily added to your own bundle/module/element. There are already some bundles/entites supported out-of-the-box (see features section).

## Features
- out-of-the-box syndication support for
    - article
    - [Reader Bundle](https://github.com/heimrichhannot/contao-reader-bundle)
- bundled syndication types:
    - sharing: facebook, email, email feedback, twitter, whatsapp
    - export: ical, print
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

### Article syndication
You can replace the contao article syndication with the syndication of this bundle.

1. Set `huh_syndication_type.enable_article_syndication` to true
1. Clear your cache and update the database
1. You'll find the new syndication config in your article configuration

### Reader Bundle

1. Create a new reader config element of type syndication
1. Select syndications
1. output the template variable in your template (with raw filter)

## Developers

### Add syndications to your bundle

Syndication bundle is build to be reused. You can easily add it to your code.

1. Add all needed fields to your dca
    - We recommend creating a listener to the loadDataContainer Hook 
    - Add fields and configuration with `SyndicationTypeDcaProvider::prepareDca($table)`
    - Generate the palette with `SyndicationTypeDcaProvider::getPalette()` and place it where it should be. You can pass a boolean parameter to get the palette seperated by legends or not.
  
    ```php
    use HeimrichHannot\SyndicationTypeBundle\Dca\SyndicationTypeDcaProvider;
    
    function prepareTable(string $table, SyndicationTypeDcaProvider $syndicationTypeDcaProvider)
      {
          $dca = &$GLOBALS['TL_DCA']['tl_article'];
          $dca['palettes']['default'] = str_replace(
            'printable', 
            $syndicationTypeDcaProvider->getPalette(false), 
            $dca['palettes']['default']
          );
          $syndicationTypeDcaProvider->prepareDca($table);
      }
    ```

1. Generate syndication links in your controller/module/listener/...
    - create an instance of `SyndicationContext`
    - generate syndication links with `SyndicationLinkProviderGenerator::generateFromContext()` (will return a `SyndicationLinkProvider` instance, which is a collection of `SyndicationLink` instances)
    - render the syndication links with `SyndicationLinkRenderer::renderProvider()` (will return the rendered links as string. You could also use/create a custom link renderer)

        ```php
        use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
        use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkProviderGenerator;
        use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkRenderer;
        
        function addSyndication(array $data, array $configuration, string $url, SyndicationLinkProviderGenerator $linkProviderGenerator, SyndicationLinkRenderer $linkRenderer): string
        {
        
            $context = new SyndicationContext($data['title'], $data['text'], $url, $data, $configuration);
            $linkProviderGenerator = $linkProviderGenerator->generateFromContext($context);
            return $linkRenderer->renderProvider($linkProviderGenerator);
        }
        ```

1. Add export support.
    - this should be done, where your content to export is completely configured and fully rendered (or can be fully rendered). In the most cases, this can be done where you generate the syndication links, but maybe it must be done on a later point.
    - create a `SyndicationContext` instance
    - run `ExportSyndicationHandler::exportByContext()`

        ```php
        use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
        use HeimrichHannot\SyndicationTypeBundle\SyndicationType\ExportSyndicationHandler;
        
        function doExport(ExportSyndicationHandler $exportSyndicationHandler, string $title, string $buffer, string $url, array $data, array $configuration)
        {
            $context = new SyndicationContext($title, $buffer, $url, $data, $configuration);
            $exportSyndicationHandler->exportByContext($context); 
        }
        ```

### Add custom syndication type

1. Create a SyndicationType class
   - the class must implement `SyndicationTypeInterface` (or `ExportSyndicationTypeInterface`)
   - you can (and we recommend) extend `AbstractSyndicationType` or `AbstractExportSyndicationType` (for syndication types which do some export like pdf, print or ical), which already implement their corresponding interfaces
1. Implement the abstract methods
   - `getType()` - return an alias for the syndication type
   - `generate()` - generate the syndication link. A `SyndicationContext` instance is passed, a `SyndicationLink` instance must be returned. We recommend to use `SyndicationLinkFactory` to create the `SyndicationLink` instance.
1. Optional: If your SyndicationType extends one of the abstract classes, your can override following methods:
   - `getCategory()` - customize the syndication category. There are two categories predefined as constants in `AbstractSyndicationType`: `share` and `export`, but it is possible to use a custom category
   - `getPalette()` - if your syndication type depends on additions configuration, you can set the syndication type palette here
1. Optional: If your SyndicationType extends one of the abstract classes, your can use the following helper methods:
   - `getValueByFieldOption()` - return a configuration value from the context (shorthand so you don't have to do all the array validation)
   - `appendGetParameterToUrl()` - utils to append a get parameter to an url. Useful for creating export links
1. Register service type class as service with `huh.syndication_type.type` service tag
1. Create an Event Subscriber for `AddSyndicationTypeFieldsEvent`,`AddSyndicationTypePaletteSelectorsEvent` and `AddSyndicationTypeSubpalettesEvent` to add custom dca fields and subpalettes


## Configuration reference

```yaml
# Default configuration for extension with alias: "huh_syndication_type"
huh_syndication_type:

    # Enable this option to replace the default contao article syndication with syndication type bundle article syndication.
    enable_article_syndication: false
```