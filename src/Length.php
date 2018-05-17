<?php

namespace TS\Media\ImageMetrics;


class Length
{

    const RE_length = '/^(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?)$/';


    /**
     * Evaluate the length expression (string, integer or float)
     * against the
     *
     * @param $expr
     * @param $anchorSize
     * @return float
     */
    public static function eval($expr, $anchorSize):float
    {
        $l = self::parse($expr);
        return $l->resolve($anchorSize);
    }


    /**
     * Parse a length expression (integer, float or string)
     *
     * @param $expr
     * @return Length
     */
    public static function parse($expr):Length
    {
        if ($expr instanceof Length) {
            return $expr;
        }
        if (is_int($expr) || is_float($expr)) {
            return new Length($expr);
        }
        if (! is_string($expr)) {
            throw new InvalidArgumentException("invalid length value, expected a Length, int, float or string but got a " . gettype($expr));
        }
        if (preg_match(self::RE_length, $expr) !== 1) {
            throw new InvalidArgumentException("invalid length value: " . $expr);
        }
        if (strpos($expr, '%') !== false) {
            $p = substr($expr, 0, -1);
            return new Length(false, floatval($p));
        }
        return new Length(floatval($expr), false);
    }

    /**
     * Try to parse a length expression (integer, float or string)
     *
     * @param $expr
     * @param $len
     * @return bool
     */
    public static function tryParse($expr, & $len):bool
    {
        try {
            $len = self::parse($expr);
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
            throw new InvalidArgumentException("missing pixel or percent value");
        }
        if ($pixel !== false && $percent !== false) {
            throw new InvalidArgumentException("both pixel and percent given");
        }
        if ($pixel !== false && is_int($pixel) === false && is_float($pixel) === false) {
            throw new InvalidArgumentException("unexpected type for pixel: " . gettype($pixel));
        }
        if ($percent !== false && is_int($percent) === false && is_float($percent) === false) {
            throw new InvalidArgumentException("unexpected type for percent: " . gettype($percent));
        }
        $this->zero = $pixel === 0.0 || $pixel === 0 || $percent === 0.0 || $percent === 0;
        $this->pixel = $pixel;
        $this->percent = $percent;
        $this->isRelative = $percent !== false;
    }


    /**
     * Resolve the actual pixel value of this length,
     * which may be relative to some other dimension.
     *
     * @param mixed $anchorSize other dimension, must be numeric
     * @return float
     */
    public function resolve($anchorSize):float
    {
        if (is_int($anchorSize) === false && is_float($anchorSize) === false) {
            throw new InvalidArgumentException("unexpected type for anchor size: " . gettype($anchorSize));
        }
        if ($this->pixel !== false) {
            return $this->pixel;
        }
        return $this->percent * 0.01 * $anchorSize;
    }

    public function changeValue($fn):void
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

