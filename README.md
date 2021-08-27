# Contao Syndication Type Bundle
[![Latest Stable Version](https://img.shields.io/packagist/v/heimrichhannot/contao-syndication-type-bundle.svg)](https://packagist.org/packages/heimrichhannot/contao-syndication-type-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/heimrichhannot/contao-syndication-type-bundle.svg)](https://packagist.org/packages/heimrichhannot/contao-syndication-type-bundle)

This bundle brings an extendable syndication framework to contao. Syndication can be easily added to your own bundle/module/element. There are already some bundles/entites supported out-of-the-box (see features section).

## Features
- out-of-the-box syndication support for
    - article
    - content element
    - [Reader Bundle](https://github.com/heimrichhannot/contao-reader-bundle)
- bundled syndication types:
    - sharing: facebook, email, email feedback, twitter, whatsapp, xing, linkedin
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

       composer require heimrichhannot/contao-syndication-type-bundle

2. Update database

### Article syndication
You can replace the contao article syndication with the syndication of this bundle.

1. Set `huh_syndication_type.enable_article_syndication` to true
1. Clear your cache and update the database
1. You'll find the new syndication config in your article configuration

### Content element syndication
You can add syndication as a content Element to every article.

1. Set `huh_syndication_type.enable_content_syndication` to true
1. Clear your cache and update the database
1. You'll find the new syndication type in content element type selection

### Reader Bundle

1. Create a new reader config element of type syndication
1. Select syndications
1. output the template variable in your template (with raw filter)

### Print content

To print your content, you have two options: print the whole page (a simple window.print() link) or use a custom template to print. Print template are written in twig and the file name must start with `syndication_type_print_`, for example `syndication_type_print_default.html.twig`. You can extend [our default](src/Resources/views/syndication/syndication_type_print_default.html.twig) template or create a complete custom template.

While creating you print template, you may want to see a preview without the print popup. To get one, just append `synPrintDebug` parameter to the print url.

## Developers

### Customize link rendering

If you need more control over the output of the link rendering, you have different options:

#### Override or change default templates

You can override following templates (just use the contao template inheritance with twig templates thanks to [Twig Support Bundle](https://github.com/heimrichhannot/contao-twig-support-bundle)):
- `syndication_provider_default.html.twig`
- `syndication_link_default.html.twig`

It is also possible to create custom versions like `syndication_provider_acme.html.twig`. To use a custom link template, you can use the `BeforeRenderSyndicationLinks` event, pass the option on SyndicationLinkRenderer methods call or decorate the `SyndicationLinkRenderer` service. Change the provider template can be archived by pass the option on SyndicationLinkRenderer methods call or decorate the `SyndicationLinkRenderer` service.

#### Use the BeforeRenderSyndicationLinks event

You can customize the options and the links that are rendered through the BeforeRenderSyndicationLinks.

Example (Contao 4.9+):

```php
namespace Acme\ExampleBundle\EventListener;

use HeimrichHannot\SyndicationTypeBundle\Event\BeforeRenderSyndicationLinksEvent;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;

/**
 * @ServiceTag("kernel.event_listener", event="HeimrichHannot\SyndicationTypeBundle\Event\BeforeRenderSyndicationLinksEvent")
 */
class SyndicationBeforeRenderSyndicationLinksEventListener
{
    public function __invoke(BeforeRenderSyndicationLinksEvent $event): void
    {
        $options = $event->getLinkRenderOptions();
        $options['template'] = 'syndication_link_acme';
        $event->setLinkRenderOptions($options);
    }
}
```

#### Decorate the `SyndicationLinkRenderer` service

You can customize all options by decorating the `SyndicationLinkRenderer` service. If you're not familiar with that, it sounds complicated, but symfony make it easy and here is a working example (just change namespaces and class names): 

```yaml
# services.yml
services:
  App\Syndication\DecoratedLinkRenderer:
    decorates: HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkRenderer
```

```php
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkRenderer;

class DecoratedLinkRenderer extends SyndicationLinkRenderer
{
    protected SyndicationLinkRenderer $inner;

    public function __construct(SyndicationLinkRenderer $inner)
    {
        $this->inner = $inner;
    }

    public function renderProvider(SyndicationLinkProvider $provider, array $options = []): string
    {
        // Tell the renderProvider method to call the customized render method
        return $this->inner->renderProvider($provider, array_merge($options, [
            'render_callback' => [$this, 'render']
        ]));
    }

    public function render(SyndicationLink $link, array $options = []): string
    {
        // add or customize link attributes
        $options['attributes']['class'] = trim(($options['attributes']['class']  ?? '').' btn btn-primary');
        // don't output template dev comments
        $options['disable_dev_comments'] = true;
        // a custom template (pass only the name)
        $options['template'] = 'a_really_custom_link_template';
        // override the link content
        $options['content'] = "Click THIS link!";
       
        return $this->inner->render($link, $options);
    }
}
```



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
    enable_content_syndication: false
```
