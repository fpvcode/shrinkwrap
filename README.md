> # UKRAINE NEEDS YOUR HELP NOW!
>
> On 24 February 2022, Russian [President Vladimir Putin ordered an invasion of Ukraine by Russian Armed Forces](https://www.bbc.com/news/world-europe-60504334).
>
> Your support is urgently needed.
>
> - Donate to the volunteers. Here is the volunteer fund helping the Ukrainian army to provide all the necessary equipment:
>  https://bank.gov.ua/en/news/all/natsionalniy-bank-vidkriv-spetsrahunok-dlya-zboru-koshtiv-na-potrebi-armiyi or https://savelife.in.ua/en/donate/
> - Triple-check social media sources. Russian disinformation is attempting to coverup and distort the reality in Ukraine.
> - Help Ukrainian refugees who are fleeing Russian attacks and shellings: https://www.globalcitizen.org/en/content/ways-to-help-ukraine-conflict/
> - Put pressure on your political representatives to provide help to Ukraine.
> - Believe in the Ukrainian people, they will not surrender, they don't have another Ukraine.
>
> THANK YOU!
----

# ShrinkWrap

## Description
**ShrinkWrap** it's just a wrapper for PHP minifiers of HTML/JS/CSS code. The main purpose of creating this wheel is the desire to have a more or less flexible tool for minifying ready-made HTML pages created by 3rd-party applications. Simply put - we submit an HTML page at the input, and at the output we have a page in which are (optional):
 - HTML, inline JS-scripts and CSS-styles are minified.
 - Referenced JS- and CSS-files (except external) are minified (also can be combined) and cached, and the links are appropriately replaced.

At the moment, **ShrinkWrap** uses the following minifiers (but there are plans to add more):
- HTML:
    - [HtmlMin](https://github.com/voku/HtmlMin).
- JS:
    - [Minify](https://github.com/matthiasmullie/minify).
- CSS:
    - [Minify](https://github.com/matthiasmullie/minify).

## Requirments
- `PHP >= 7.3.0`

## Installation
- Download via `composer`: `composer require fpvcode/shrinkwrap`. Due to dependency requirements the package loaded by default requires PHP version `8.1.0`. To download the package compatible with PHP `7.3.0`, create a `composer.json` file with the following content:
```
{
    "config": {
        "vendor-dir": "vendor",
        "platform": {
            "php": "7.3"
        }
    }
}

```
- Сlone the repo:`git clone https://github.com/fpvcode/shrinkwrap`.


## Usage
```
use fpvcode\ShrinkWrap;

$shrinkwrap = new ShrinkWrap($options);

// Global options
$shrinkwrap->doMinifyHtml(true);        // Allow to minify HTML Code. Default value is `false`.
$shrinkwrap->doMinifyInlineJs(true);    // Allow to minify inline scripts. The scripts combine and append in the bottom of related parent tag (`head` or `body`). Default value is `false`.
$shrinkwrap->doMinifyInlineCss(true);   // Allow to minify inline styles. The styles combine and append in the bottom of related parent tag (`head` or `body`). Default value is `false`.
$shrinkwrap->doMinifyJs(true);          // Allow to minify and cache non-minified JS-files. Default value is `false`.
$shrinkwrap->doCombineJs(true);         // Allow to combine all JS-files into one. Depends on `->doMinifyJS(true)`. Default value is `false`.
$shrinkwrap->doMinifyCss(true);         // Allow to minify and cache non-minified JS-files. Default value is `false`.
$shrinkwrap->doCombineCss(true);        // Allow to combine all CSS-files into one. Depends on `->doMinifyCSS(true)`. Default value is `false`.
$shrinkwrap->doLog(true);               // Show some info in the browser console. Default value is `false`.
$shrinkwrap->assetDir('assets');        // Directory to store minified files cache. Default value is `__DIR__/assets`.

/* --------------------------------------------------------------------------*/
// Configure minify engines and set their native options.
/* --------------------------------------------------------------------------*/

// Voku HtmlMin minify options.
$voku_options = [
	'doOptimizeViaHtmlDomParser'                   => true,  // optimize html via "HtmlDomParser()"
	'doRemoveComments'                             => true,  // remove default HTML comments (depends on "doOptimizeViaHtmlDomParser(true)")
	'doSumUpWhitespace'                            => true,  // sum-up extra whitespace from the Dom (depends on "doOptimizeViaHtmlDomParser(true)")
	'doRemoveWhitespaceAroundTags'                 => true,  // remove whitespace around tags (depends on "doOptimizeViaHtmlDomParser(true)")
	'doOptimizeAttributes'                         => true,  // optimize html attributes (depends on "doOptimizeViaHtmlDomParser(true)")
	'doRemoveHttpPrefixFromAttributes'             => true,  // remove optional "http:"-prefix from attributes (depends on "doOptimizeAttributes(true)")
	'doRemoveHttpsPrefixFromAttributes'            => true,  // remove optional "https:"-prefix from attributes (depends on "doOptimizeAttributes(true)")
	'doKeepHttpAndHttpsPrefixOnExternalAttributes' => true,  // keep "http:"- and "https:"-prefix for all external links
	'doMakeSameDomainsLinksRelative'               => [],    // make some links relative, by removing the domain from attributes (['example.com'])
	'doRemoveDefaultAttributes'                    => true,  // remove defaults (depends on "doOptimizeAttributes(true)" | disabled by default)
	'doRemoveDeprecatedAnchorName'                 => true,  // remove deprecated anchor-jump (depends on "doOptimizeAttributes(true)")
	'doRemoveDeprecatedScriptCharsetAttribute'     => true,  // remove deprecated charset-attribute - the browser will use the charset from the HTTP-Header, anyway (depends on "doOptimizeAttributes(true)")
	'doRemoveDeprecatedTypeFromScriptTag'          => true,  // remove deprecated script-mime-types (depends on "doOptimizeAttributes(true)")
	'doRemoveDeprecatedTypeFromStylesheetLink'     => true,  // remove "type=text/css" for css links (depends on "doOptimizeAttributes(true)")
	'doRemoveDeprecatedTypeFromStyleAndLinkTag'    => true,  // remove "type=text/css" from all links and styles
	'doRemoveDefaultMediaTypeFromStyleAndLinkTag'  => true,  // remove "media="all" from all links and styles
	'doRemoveDefaultTypeFromButton'                => false, // remove type="submit" from button tags
	'doRemoveEmptyAttributes'                      => true,  // remove some empty attributes (depends on "doOptimizeAttributes(true)")
	'doRemoveValueFromEmptyInput'                  => true,  // remove 'value=""' from empty <input> (depends on "doOptimizeAttributes(true)")
	'doSortCssClassNames'                          => true,  // sort css-class-names, for better gzip results (depends on "doOptimizeAttributes(true)")
	'doSortHtmlAttributes'                         => true,  // sort html-attributes, for better gzip results (depends on "doOptimizeAttributes(true)")
	'doRemoveSpacesBetweenTags'                    => true,  // remove more (aggressive) spaces in the dom (disabled by default)
	'doRemoveOmittedQuotes'                        => true,  // remove quotes e.g. class="lall" => class=lall
	'doRemoveOmittedHtmlTags'                      => true,  // remove ommitted html tags e.g. <p>lall</p> => <p>lall
];

$shrinkwrap->htmlEngineConfig('voku', $voku_options); // allows to customize the Voku HtmlMin minifier.

$html = '
<!DOCTYPE html>
<html dir="ltr" lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>HTML page</title>

    <script>
      function a() {
        let a = 0;
      }
    </script>

    <style>
      .class-a {
        border-radius: 4px;
      }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.js" type="text/javascript"></script>
    <script src="js/a.js" type="text/javascript"></script>
    <script src="js/b.min.js" type="text/javascript"></script>

    <link href="https://cdn.usebootstrap.com/bootstrap/3.3.7/css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/a.css" rel="stylesheet" media="screen">
    <link href="css/b.min.css" rel="stylesheet" media="screen">
  </head>

  <body>
    <div>
      Test HTML
    </div>

    <!-- Comment -->
    <script>
      function b() {
        let b = 0;
      }
    </script>

    <script>
      function c() {
        let c = 0;
      }
    </script>

    <!-- Comment -->
    <style>
      .class-b {
        border-radius: 4px;
      }
    </style>
    <style>
      .class-c {
        border-radius: 4px;
      }
    </style>
  </body>


</html>';

$html = $shrinkwrap->output($html);
```
Minified output:
```
<!DOCTYPE html>
<html dir="ltr" lang="en"><head><meta charset="UTF-8"><meta content="width=device-width, initial-scale=1" name="viewport"><meta content="IE=edge" http-equiv="X-UA-Compatible"><title>HTML page</title><script src="//code.jquery.com/jquery-3.7.1.js"></script><link href="//cdn.usebootstrap.com/bootstrap/3.3.7/css/bootstrap.css" media="screen" rel="stylesheet"><script src="assets/4eb126993fd8e08ddf2c186bd95cd514.min.js"></script><link href="assets/55a39d42c37de2a6ca9617f70dfc2bfa.min.css" rel="stylesheet"><script>function a(){let a=0}</script><style>.class-a{border-radius:4px}</style><body><div> Test HTML </div><script>function b(){let b=0};function c(){let c=0}</script><style>.class-b{border-radius:4px}.class-c{border-radius:4px}</style></body></head></html>
```

## Thanks
- Thanks to [Lars Moelleken](https://github.com/voku) for [HtmlMin](https://github.com/voku/HtmlMin) - HTML Compressor and Minifier for PHP.
- Thanks to [Matthias Mullie](https://github.com/matthiasmullie) for [Minify](https://github.com/matthiasmullie/minify) - CSS and JavaScript minifier.
- Thanks to [Masterminds](https://github.com/Masterminds) for [HTML5-PHP](https://github.com/Masterminds/html5-php) - An HTML5 parser and serializer for PHP.
- Thanks to [Microsoft](https://www.microsoft.com/) for [Visual Studio Code](https://code.visualstudio.com/) and [GitHub](https://github.com).
