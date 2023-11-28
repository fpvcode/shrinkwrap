<?php

declare(strict_types=1);

namespace fpvcode\ShrinkWrap;

use MatthiasMullie\Minify;

/**
 * [Description MinifyJS].
 */
class MinifyJS extends ShrinkWrap {
    /**
     * @var array
     */
    private $available_engines = ['matthiasmullie', 'regex'];

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
     * @param string $engine
     * @param array  $options
     */
    public function __construct(string $engine = 'matthiasmullie', array $options = []) {
        in_array($engine, $this->available_engines) ? $this->engine = $engine : $this->engine = 'matthiasmullie';
        $this->options = $options;
    }

    /**
     * @param null|string $data
     *
     * @return null|mixed
     */
    public function init(string $data = null) {
        switch ($this->engine) {
            case 'matthiasmullie':
                $this->minifier = new Minify\JS($data);

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
            case 'matthiasmullie':
                return $this->minifier->minify($data);
        }
    }

    /**
     * @param string $data
     *
     * @return null|mixed
     */
    public function add(string $data) {
        switch ($this->engine) {
            case 'matthiasmullie':
                return $this->minifier->add($data);
        }
    }
}
