<?php

declare(strict_types=1);

namespace fpvcode\ShrinkWrap;

use voku\helper\HtmlMin;

/**
 * [Description MinifyHTML].
 */
class MinifyHTML extends ShrinkWrap {
    /**
     * @var array
     */
    private $available_engines = ['voku', 'regex'];

    /**
     * @var string
     */
    private $engine;

    /**
     * @var array
     */
    private $options;

    /**
     * @var mixed
     */
    private $minifier;

    /**
     * @var array
     */
    private $voku_options = [
        'doOptimizeViaHtmlDomParser'                   => false, // optimize html via "HtmlDomParser()"
        'doRemoveComments'                             => false, // remove default HTML comments (depends on "doOptimizeViaHtmlDomParser(true)")
        'doSumUpWhitespace'                            => false, // sum-up extra whitespace from the Dom (depends on "doOptimizeViaHtmlDomParser(true)")
        'doRemoveWhitespaceAroundTags'                 => false, // remove whitespace around tags (depends on "doOptimizeViaHtmlDomParser(true)")
        'doOptimizeAttributes'                         => false, // optimize html attributes (depends on "doOptimizeViaHtmlDomParser(true)")
        'doRemoveHttpPrefixFromAttributes'             => false, // remove optional "http:"-prefix from attributes (depends on "doOptimizeAttributes(true)")
        'doRemoveHttpsPrefixFromAttributes'            => false, // remove optional "https:"-prefix from attributes (depends on "doOptimizeAttributes(true)")
        'doKeepHttpAndHttpsPrefixOnExternalAttributes' => false, // keep "http:"- and "https:"-prefix for all external links
        'doMakeSameDomainsLinksRelative'               => [],    // make some links relative, by removing the domain from attributes (['example.com'])
        'doRemoveDefaultAttributes'                    => false, // remove defaults (depends on "doOptimizeAttributes(true)" | disabled by default)
        'doRemoveDeprecatedAnchorName'                 => false, // remove deprecated anchor-jump (depends on "doOptimizeAttributes(true)")
        'doRemoveDeprecatedScriptCharsetAttribute'     => false, // remove deprecated charset-attribute - the browser will use the charset from the HTTP-Header, anyway (depends on "doOptimizeAttributes(true)")
        'doRemoveDeprecatedTypeFromScriptTag'          => false, // remove deprecated script-mime-types (depends on "doOptimizeAttributes(true)")
        'doRemoveDeprecatedTypeFromStylesheetLink'     => false, // remove "type=text/css" for css links (depends on "doOptimizeAttributes(true)")
        'doRemoveDeprecatedTypeFromStyleAndLinkTag'    => false, // remove "type=text/css" from all links and styles
        'doRemoveDefaultMediaTypeFromStyleAndLinkTag'  => false, // remove "media="all" from all links and styles
        'doRemoveDefaultTypeFromButton'                => false, // remove type="submit" from button tags
        'doRemoveEmptyAttributes'                      => false, // remove some empty attributes (depends on "doOptimizeAttributes(true)")
        'doRemoveValueFromEmptyInput'                  => false, // remove 'value=""' from empty <input> (depends on "doOptimizeAttributes(true)")
        'doSortCssClassNames'                          => false, // sort css-class-names, for better gzip results (depends on "doOptimizeAttributes(true)")
        'doSortHtmlAttributes'                         => false, // sort html-attributes, for better gzip results (depends on "doOptimizeAttributes(true)")
        'doRemoveSpacesBetweenTags'                    => false, // remove more (aggressive) spaces in the dom (disabled by default)
        'doRemoveOmittedQuotes'                        => false, // remove quotes e.g. class="lall" => class=lall
        'doRemoveOmittedHtmlTags'                      => false, // remove ommitted html tags e.g. <p>lall</p> => <p>lall
    ];

    /**
     * @param string $engine
     * @param array  $options
     */
    public function __construct(string $engine = 'voku', array $options = []) {
        in_array($engine, $this->available_engines) ? $this->engine = $engine : $this->engine = 'voku';
        $this->options = $options;
    }

    /**
     * @param null|string $data
     *
     * @return null|mixed
     */
    public function init(string $data = null) {
        switch ($this->engine) {
            case 'voku':
                $this->minifier = new HtmlMin($data);

                if ($this->options) {
                    $this->options = array_merge($this->voku_options, $this->options);
                    foreach ($this->options as $key => $value) {
                        $this->minifier->{$key}($value);
                    }
                }

                return $this->minifier;
        }
    }

    /**
     * @param null|string $data
     *
     * @return null|mixed
     */
    public function minify(string $data = null) {
        switch ($this->engine) {
            case 'voku':
                return $this->minifier->minify($data);
        }
    }
}
