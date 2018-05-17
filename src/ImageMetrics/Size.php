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
    public static function compareMax(Size $a, Size $b)
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
    public static function compareMin(Size $a, Size $b)
    {
        return min((float)$b->width / (float)$a->width, (float)$b->height / (float)$a->height);
    }

    public static function parse($width, $height)
    {
        $w = Length::parse($width);
        $h = Length::parse($height);
        if ($w->isRelative) {
            throw new \InvalidArgumentException(sprintf('Invalid width %s, value must be absolute.', $width));
        }
        if ($h->isRelative) {
            throw new \InvalidArgumentException(sprintf('Invalid width %s, value must be absolute.', $height));
        }
        return new Size($w->pixel, $h->pixel);
    }

    public function __construct($width, $height)
    {
        if (!is_numeric($width)) {
            $msg = sprintf('Expected argument $width to be numeric, but got a %s', gettype($width));
            throw new \UnexpectedValueException($msg);
        }
        if (!is_numeric($height)) {
            $msg = sprintf('Expected argument $height to be numeric, but got a %s', gettype($height));
            throw new \UnexpectedValueException($msg);
        }
        $this->width = $width;
        $this->height = $height;
    }

    public function getRatio()
    {
        return (float)$this->width / (float)$this->height;
    }

    public function getArea()
    {
        return $this->width * $this->height;
    }

    public function contains(Size $size)
    {
        return $this->width >= $size->width && $this->height >= $size->height;
    }

    public function floor()
    {
        $this->width = floor($this->width);
        $this->height = floor($this->height);
        return $this;
    }

    public function ceil()
    {
        $this->width = (int)ceil($this->width);
        $this->height = (int)ceil($this->height);
        return $this;
    }

    public function round()
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

