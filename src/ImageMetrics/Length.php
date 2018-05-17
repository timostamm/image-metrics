<?php

namespace TS\Media\ImageMetrics;


class Length
{

    const RE_length = '/^(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?)$/';


    public static function eval($str, $anchorSize)
    {
        $l = self::parse($str);
        return $l->resolve($anchorSize);
    }

    public static function parse($str)
    {
        if ($str instanceof Length) {
            return $str;
        }
        if (is_int($str) || is_float($str)) {
            return new Length($str);
        }
        if (preg_match(self::RE_length, $str) !== 1) {
            throw new \Exception("invalid length value: " . $str);
        }
        if (strpos($str, '%') !== false) {
            $p = substr($str, 0, -1);
            return new Length(false, floatval($p));
        }
        return new Length(floatval($str), false);
    }

    public static function tryParse($str, & $len)
    {
        try {
            $len = self::parse($str);
            return true;
        } catch (\Exception $ex) {
            $len = null;
            return false;
        }
    }

    public $pixel = false;

    public $percent = false;

    public $zero = true;

    public $isRelative = false;

    public function __construct($pixel, $percent = false)
    {
        if ($pixel === false && $percent === false) {
            throw new \Exception("missing pixel or percent value");
        }
        if ($pixel !== false && $percent !== false) {
            throw new \Exception("both pixel and percent given");
        }
        if ($pixel !== false && is_int($pixel) === false && is_float($pixel) === false) {
            throw new \Exception("unexpected type for pixel: " . gettype($pixel));
        }
        if ($percent !== false && is_int($percent) === false && is_float($percent) === false) {
            throw new \Exception("unexpected type for percent: " . gettype($percent));
        }
        $this->zero = $pixel === 0.0 || $pixel === 0 || $percent === 0.0 || $percent === 0;
        $this->pixel = $pixel;
        $this->percent = $percent;
        $this->isRelative = $percent !== false;
    }

    public function resolve($anchorSize)
    {
        if (is_int($anchorSize) === false && is_float($anchorSize) === false) {
            throw new \Exception("unexpected type for anchor size: " . gettype($anchorSize));
        }
        if ($this->pixel !== false) {
            return $this->pixel;
        }
        return $this->percent * 0.01 * $anchorSize;
    }

    public function changeValue($fn)
    {
        if ($this->pixel !== false) {
            $this->pixel = $fn($this->pixel);
        } else if ($this->percent !== false) {
            $this->percent = $fn($this->percent);
        }
    }

    public function __toString()
    {
        if ($this->pixel !== false) {
            return $this->pixel . 'px';
        }
        return $this->percent . '%';
    }

}

