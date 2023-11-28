<?php

declare(strict_types=1);

namespace fpvcode\ShrinkWrap;

use MatthiasMullie\Minify;

class MinifyJS extends ShrinkWrap {
    private $available_engines = ['matthiasmullie', 'regex'];

    private $engine;
    private $options;

    private $minifier;

    public function __construct(string $engine = 'matthiasmullie', array $options = []) {
        in_array($engine, $this->available_engines) ? $this->engine = $engine : $this->engine = 'matthiasmullie';
        $this->options = $options;
    }

    public function init(string $data = null) {
        switch ($this->engine) {
            case 'matthiasmullie':
                $this->minifier = new Minify\JS($data);

                return $this->minifier;
        }
    }

    public function minify(string $data = null) {
        switch ($this->engine) {
            case 'matthiasmullie':
                return $this->minifier->minify($data);
        }
    }

    public function add(string $data) {
        switch ($this->engine) {
            case 'matthiasmullie':
                return $this->minifier->add($data);
        }
    }
}
