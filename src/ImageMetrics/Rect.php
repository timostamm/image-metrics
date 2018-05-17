<?php

namespace TS\Media\ImageMetrics;


/**
 * @author Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class Rect
{


    public $x;
    public $y;
    public $left;
    public $top;
    public $width;
    public $height;
    public $right;
    public $bottom;


    public function __construct($x, $y, $width, $height)
    {
        if (!is_numeric($x)) {
            $msg = sprintf('Expected argument $x to be numeric, but got a %s', gettype($x));
            throw new \UnexpectedValueException($msg);
        }
        if (!is_numeric($y)) {
            $msg = sprintf('Expected argument $y to be numeric, but got a %s', gettype($y));
            throw new \UnexpectedValueException($msg);
        }
        if (!is_numeric($width)) {
            $msg = sprintf('Expected argument $width to be numeric, but got a %s', gettype($width));
            throw new \UnexpectedValueException($msg);
        }
        if (!is_numeric($height)) {
            $msg = sprintf('Expected argument $height to be numeric, but got a %s', gettype($height));
            throw new \UnexpectedValueException($msg);
        }
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
        $this->updateLeftTopRightBottom();
    }


    public function normalize()
    {
        if ($this->width < 0) {
            $this->x = $this->x + $this->width;
            $this->width = abs($this->width);
        }
        if ($this->height < 0) {
            $this->y = $this->y + $this->height;
            $this->height = abs($this->height);
        }
        $this->updateLeftTopRightBottom();
    }


    protected function updateLeftTopRightBottom()
    {
        if ($this->width < 0) {
            $this->left = $this->x + $this->width;
        } else {
            $this->left = $this->x;
        }
        if ($this->height < 0) {
            $this->top = $this->y + $this->height;
        } else {
            $this->top = $this->y;
        }
        $this->right = $this->left + abs($this->width);
        $this->bottom = $this->top + abs($this->height);
    }


    public function getPosition()
    {
        return new Point($this->x, $this->y);
    }


    public function getSize()
    {
        return new Size($this->width, $this->height);
    }


    public function getIntersection($x, $y, $width, $height)
    {
        if (false === $this->intersects($x, $y, $width, $height)) {
            return null;
        }

        $r1 = clone $this;
        $r2 = new Rect($x, $y, $width, $height);
        $r1->normalize();
        $r2->normalize();

        $left = max($r1->left, $r2->left);
        $top = max($r1->top, $r2->top);
        $right = min($r1->right, $r2->right);
        $bottom = min($r1->bottom, $r2->bottom);

        return new Rect($left, $top, $right - $left, $bottom - $top);
    }


    public function intersects($x, $y, $width, $height)
    {

        $r1 = clone $this;
        $r2 = new Rect($x, $y, $width, $height);
        $r1->normalize();
        $r2->normalize();

        return !($r2->left > $r1->right ||
            $r2->right < $r1->left ||
            $r2->top > $r1->bottom ||
            $r2->bottom < $r1->top);

    }


    public function moveBy($dx, $dy)
    {
        $this->x += $dx;
        $this->y += $dy;
        $this->updateLeftTopRightBottom();
    }


    public function resizeBy($dx, $dy)
    {
        $this->width += $dx;
        $this->width += $dy;
        $this->updateLeftTopRightBottom();
    }


    public function floor()
    {
        $this->x = floor($this->x);
        $this->y = floor($this->y);
        $this->width = floor($this->width);
        $this->height = floor($this->height);
        $this->updateLeftTopRightBottom();
        return $this;
    }

    public function ceil()
    {
        $this->x = ceil($this->x);
        $this->y = ceil($this->y);
        $this->width = ceil($this->width);
        $this->height = ceil($this->height);
        $this->updateLeftTopRightBottom();
        return $this;
    }

    public function round()
    {
        $this->x = round($this->x);
        $this->y = round($this->y);
        $this->width = round($this->width);
        $this->height = round($this->height);
        $this->updateLeftTopRightBottom();
        return $this;
    }


    public function __toString()
    {
        return sprintf('(x=%s y=%s width=%s height=%s)', $this->x, $this->y, $this->width, $this->height);
    }


}

