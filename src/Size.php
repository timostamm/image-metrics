<?php

namespace TS\Media\ImageMetrics;


/**
 *
 * @author Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class Size
{

    public $width;

    public $height;

    /**
     * Compare the width and the height of both sizes and return the larger ratio.
     *
     * @param Size $a
     * @param Size $b
     * @return float
     */
    public static function compareMax(Size $a, Size $b):float
    {
        return max((float)$b->width / (float)$a->width, (float)$b->height / (float)$a->height);
    }

    /**
     * Compare the width and the height of both sizes and return the smaller ratio.
     *
     * @param Size $a
     * @param Size $b
     * @return float
     */
    public static function compareMin(Size $a, Size $b):float
    {
        return min((float)$b->width / (float)$a->width, (float)$b->height / (float)$a->height);
    }

    public static function parse($width, $height):Size
    {
        $w = Length::parse($width);
        $h = Length::parse($height);
        if ($w->isRelative) {
            throw new InvalidArgumentException(sprintf('Invalid width %s, value must be absolute.', $width));
        }
        if ($h->isRelative) {
            throw new InvalidArgumentException(sprintf('Invalid width %s, value must be absolute.', $height));
        }
        return new Size($w->pixel, $h->pixel);
    }

    public function __construct($width, $height)
    {
        if (!is_numeric($width)) {
            $msg = sprintf('Expected argument $width to be numeric, but got a %s', gettype($width));
            throw new InvalidArgumentException($msg);
        }
        if (!is_numeric($height)) {
            $msg = sprintf('Expected argument $height to be numeric, but got a %s', gettype($height));
            throw new InvalidArgumentException($msg);
        }
        $this->width = $width;
        $this->height = $height;
    }

    public function getRatio():float
    {
        return (float)$this->width / (float)$this->height;
    }

    public function getArea():float
    {
        return $this->width * $this->height;
    }

    public function contains(Size $size):bool
    {
        return $this->width >= $size->width && $this->height >= $size->height;
    }

    public function floor():self
    {
        $this->width = floor($this->width);
        $this->height = floor($this->height);
        return $this;
    }

    public function ceil():self
    {
        $this->width = (int)ceil($this->width);
        $this->height = (int)ceil($this->height);
        return $this;
    }

    public function round():self
    {
        $this->width = (int)round($this->width);
        $this->height = (int)round($this->height);
        return $this;
    }

    public function __toString()
    {
        return sprintf('(width=%s height=%s)', $this->width, $this->height);
    }

}

