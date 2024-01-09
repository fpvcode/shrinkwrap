<?php

declare(strict_types=1);

namespace fpvcode\ShrinkWrap;

use Masterminds\HTML5;

/**
 * Class ShrinkWrap.
 */
class ShrinkWrap {
    /**
     * @var bool
     */
    private $doMinifyHtml = false;

    /**
     * @var bool
     */
    private $doMinifyInlineJs = false;

    /**
     * @var bool
     */
    private $doMinifyInlineCss = false;

    /**
     * @var bool
     */
    private $doMinifyJs = false;

    /**
     * @var bool
     */
    private $doCombineJs = false;

    /**
     * @var bool
     */
    private $doMinifyCss = false;

    /**
     * @var bool
     */
    private $doCombineCss = false;

    /**
     * @var bool
     */
    private $doLog = false;

    /**
     * @var string
     */
    private $assetDir = 'assets';

    /**
     * @var string an html page
     */
    private $html;

    /**
     * @var HTML5 HTML DOM parser
     */
    private $domparser;

    /**
     * @var MinifyHTML
     */
    private $htmlMinifier;

    /**
     * @var MinifyJS
     */
    private $jsMinifier;

    /**
     * @var MinifyCSS
     */
    private $cssMinifier;

    /**
     * @var array
     */
    private $timelog = [];

    /**
     * ShrinkWrap constructor.
     *
     * @param string[] (optional) init options
     */
    public function __construct() {
        $this->htmlEngineConfig();
        $this->jsEngineConfig();
        $this->cssEngineConfig();

        $this->domparser = new HTML5();
    }

    /**
     * @param mixed $method
     * @param mixed $args
     */
    public function __call($method, $args) {
        echo "Calling unknown object method '{$method}' " . json_encode($args) . "\n";
    }

    /**
     * @param bool (optional) $value
     */
    public function doMinifyHtml($value = false): void {
        $this->doMinifyHtml = (bool)$value;
    }

    /**
     * @param bool (optional) $value
     */
    public function doMinifyInlineJs($value = false): void {
        $this->doMinifyInlineJs = (bool)$value;
    }

    /**
     * @param bool (optional) $value
     */
    public function doMinifyInlineCss($value = false): void {
        $this->doMinifyInlineCss = (bool)$value;
    }

    /**
     * @param bool (optional) $value
     */
    public function doMinifyJs($value = false): void {
        $this->doMinifyJs = (bool)$value;
    }

    /**
     * @param bool (optional) $value
     */
    public function doCombineJs($value = false): void {
        $this->doCombineJs = (bool)$value;
    }

    /**
     * @param bool (optional) $value
     */
    public function doMinifyCss($value = false): void {
        $this->doMinifyCss = (bool)$value;
    }

    /**
     * @param bool (optional) $value
     */
    public function doCombineCss($value = false): void {
        $this->doCombineCss = (bool)$value;
    }

    /**
     * @param bool (optional) $value
     */
    public function doLog($value = false): void {
        $this->doLog = (bool)$value;
    }

    /**
     * @param string (optional) $value A directory using to store cached minified files
     */
    public function assetDir($value = 'assets'): void {
        $this->assetDir = trim($value, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!is_dir($this->assetDir)) try {
            mkdir($this->assetDir, 0777, true);
        } catch (\Throwable $throw) {
            echo $throw;
        }
    }

    /**
     * Init JS-minifier engines wrapper.
     *
     * @param string (optional) $engine  An engine used to minify inline and referenced JS-scripts
     * @param string (optional) $options The engine native options
     */
    public function jsEngineConfig(string $engine = 'matthiasmullie', array $options = []): void {
        $this->jsMinifier = new MinifyJS($engine, $options);
    }

    /**
     * Init CSS-minifier engines wrapper.
     *
     * @param string (optional) $engine  An engine used to minify inline and referenced CSS-styles
     * @param string (optional) $options The engine native options
     */
    public function cssEngineConfig(string $engine = 'matthiasmullie', array $options = []): void {
        $this->cssMinifier = new MinifyCSS($engine, $options);
    }

    /**
     * Init HTML-minifier engines wrapper.
     *
     * @param string (optional) $engine  An engine used to minify HTML code
     * @param string (optional) $options The engine native options
     */
    public function htmlEngineConfig(string $engine = 'voku', array $options = []): void {
        $this->htmlMinifier = new MinifyHTML($engine, $options);
    }

    /**
     * Minifies page HTML code, inline scripts and styles, referenced JS- and CSS-files.
     *
     * @param string $html Regular HTML-page
     *
     * @return string Minified HTML-page
     */
    public function output(string $html): string {
        $this->html = $html;

        if ($this->doMinifyJs) {
            $this->minRefByType('js');
        }

        if ($this->doMinifyCss) {
            $this->minRefByType('css');
        }

        if ($this->doMinifyInlineJs) {
            $this->minTagByType('js');
        }

        if ($this->doMinifyInlineCss) {
            $this->minTagByType('css');
        }

        if ($this->doMinifyHtml) {
            $this->minifyHTML();
        }

        if ($this->doLog) {
            $htmldom = $this->domparser->loadHTML($this->html);

            if ($htmldom->getElementsByTagName('body')->length) {
                $container = $htmldom->getElementsByTagName('body')->item(0);

                $content = 'console.table(' . json_encode($this->timelog) . ')';

                $script = $htmldom->createElement('script');
                $script->appendChild($htmldom->createTextNode($content));

                $container->appendChild($script);

                $this->html = $this->domparser->saveHTML($htmldom);
            }
        }

        return $this->html;
    }

    /**
     * Minify page HTML code.
     */
    private function minifyHTML(): void {
        if ($this->doLog) {
            $osize = strlen($this->html);
            $start = microtime(true);
        }

        $minifier = $this->htmlMinifier->init();
        $this->html = $minifier->minify($this->html);

        if ($this->doLog) {
            $this->timelog['html'] = [round((microtime(true) - $start), 4) . 'μs', $osize, strlen($this->html)];
        }
    }

    /**
     * Minify and combines all inline JS-scripts and CSS-styles.
     *
     * @param string (optional) $type Type of content of processed tags (js/css)
     */
    private function minTagByType(string $type = 'js'): void {
        if ($this->doLog) {
            $start = microtime(true);
            $osize = strlen($this->html);
        }

        $htmldom = $this->domparser->loadHTML($this->html);

        switch ($type) {
            case 'css':
                $t = 'style';

                break;

            case 'js':
                $t = 'script';

                break;

            default:
                $t = 'script';

                break;
        }

        foreach (['head', 'body'] as $i) {
            if (!$htmldom->getElementsByTagName($i)->length) continue;

            $container = $htmldom->getElementsByTagName($i)->item(0);

            $minifier = $this->{$type . 'Minifier'}->init();
            $to_remove = [];

            foreach ($container->getElementsByTagName($t) as $e) {
                if ($type == 'js') {
                    if (!$e->getAttribute('src')) {
                        if ($e->textContent && is_null(json_decode($e->textContent, true))) {
                            $minifier->add(str_replace(['<!--', '//-->', '-->'], '', $e->textContent));

                            $to_remove[] = $e;
                        }
                    }
                }

                if ($type == 'css') {
                    $minifier->add($e->textContent);

                    $to_remove[] = $e;
                }
            }

            foreach ($to_remove as $e) $e->parentNode->removeChild($e);

            $m = $minifier->minify();

            if ($m) {
                $i = $htmldom->createElement($t);
                $i->appendChild($htmldom->createTextNode($m));

                $container->appendChild($i);
            }
        }

        $this->html = $this->domparser->saveHTML($htmldom);

        if ($this->doLog) {
            $this->timelog['inline ' . $type] = [round((microtime(true) - $start), 4) . 'μs', $osize, strlen($this->html)];
        }
    }

    /**
     * Minify (non-minified) and combine all referenced JS- and CSS-files (except external links).
     *
     * @param string (optional) $type Type of files of processed tags (js/css)
     */
    private function minRefByType(string $type = 'js'): void {
        if ($this->doLog) {
            $start = microtime(true);
        }

        $htmldom = $this->domparser->loadHTML($this->html);

        switch ($type) {
            case 'css':
                $t = 'link';

                break;

            case 'js':
                $t = 'script';

                break;

            default:
                $t = 'script';

                break;
        }

        foreach (['head', 'body'] as $i) {
            if (!$htmldom->getElementsByTagName($i)->length) continue;

            $container = $htmldom->getElementsByTagName($i)->item(0);
            $files = [];
            $to_remove = [];

            foreach ($container->getElementsByTagName($t) as $e) {
                if ($type == 'js' && $e->getAttribute('src')) {
                    $attr = 'src';
                } elseif ($type == 'css' && $e->getAttribute('href')) {
                    if ($e->getAttribute('media') == 'print' || $e->getAttribute('media') == 'speech') continue;

                    $attr = 'href';
                } else {
                    continue;
                }

                $fname = $e->getAttribute($attr);

                if (filter_var($fname, FILTER_VALIDATE_URL)) continue;
                $parsed_url = parse_url($fname);
                if (isset($parsed_url['scheme']) || isset($parsed_url['host'])) continue;
                if (!is_file($fname)) continue;

                if ($this->doLog) {
                    $osize = isset($osize) ? $osize + filesize($fname) : filesize($fname);
                }

                $finfo = pathinfo($fname);

                if (($type == 'js' && $this->doCombineJs) || ($type == 'css' && $this->doCombineCss)) {
                    $files[] = $fname;
                    $to_remove[] = $e;
                } else {
                    $parts = explode('.', $finfo['filename']);
                    if (end($parts) == 'min') continue;

                    $mfile = $this->getMfile($fname);

                    if (!is_file($mfile)) {
                        $minifier = $this->{$type . 'Minifier'}->init();
                        $minifier->add($fname);
                        $minifier->minify($mfile);
                    }

                    if ($this->doLog) {
                        $msize = isset($msize) ? $msize + filesize($mfile) : filesize($mfile);
                    }

                    $e->setAttribute($attr, $mfile);
                }
            }

            if ($files && (($type == 'js' && $this->doCombineJs) || ($type == 'css' && $this->doCombineCss))) {
                $mfile = $this->getMfile($this->checksum($files) . '.min.' . $type);

                if (!is_file($mfile)) {
                    $minifier = $this->{$type . 'Minifier'}->init();

                    foreach ($files as $file) {
                        if ($this->doLog) {
                            $osize = isset($osize) ? $osize + filesize($file) : filesize($file);
                        }

                        $minifier->add($file);
                    }

                    $minifier->minify($mfile);
                }

                if ($this->doLog) {
                    $msize = isset($msize) ? $msize + filesize($mfile) : filesize($mfile);
                }

                $insert = $htmldom->createElement($t);
                $insert->setAttribute($attr, $mfile);

                if ($type == 'css') {
                    $insert->setAttribute('rel', 'stylesheet');
                }

                $container->appendChild($insert);

                foreach ($to_remove as $e) $e->parentNode->removeChild($e);
            }
        }

        $this->html = $this->domparser->saveHTML($htmldom);

        if ($this->doLog) {
            $this->timelog['*.' . $type] = [round((microtime(true) - $start), 4) . 'μs', $osize ?? '', $msize ?? ''];
        }
    }

    /**
     * Returns path to minified file  by source file name.
     *
     * @param string $file Name or full path of a file to be minified
     *
     * @return string Minified file path
     */
    private function getMfile(string $file): string {
        $finfo = pathinfo($file);

        if (preg_match('/[0-9a-f]{32}/i', explode('.', $finfo['filename'])[0])) {
            $finfo['filename'] = explode('.', $finfo['filename'])[0];
            $tmpl = '.min.' . $finfo['extension'];
        } else {
            $tmpl = '_' . filemtime($file) . '.min.' . $finfo['extension'];
        }

        return $this->assetDir . $finfo['filename'] . $tmpl;
    }

    /**
     * @param string[] $files File list
     *
     * @return string MD5 hash
     */
    private function checksum(array $files): string {
        $checksum = '';

        foreach ($files as $file) {
            $checksum .= $file . filemtime($file);
        }

        return md5($checksum);
    }
}
