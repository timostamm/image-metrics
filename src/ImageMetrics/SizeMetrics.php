<?php

namespace TS\Media\ImageMetrics;


/**
 * @author Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class SizeMetrics extends Size
{


    public const RATIO_PRESERVE = 1;
    public const RATIO_IGNORE = 2;

    public const UPSIZE_ALLOW = 4;
    public const UPSIZE_PREVENT = 8;

    public const RESIZE_STRETCH = 16;
    public const RESIZE_CONTAIN = 32;
    public const RESIZE_COVER = 64;


    /**
     * Create a new metrics for the given size.
     *
     * @param Size $size
     * @return self
     */
    public static function createFromSize(Size $size):SizeMetrics
    {
        return new SizeMetrics($size->width, $size->height);
    }

    /**
     * Create a new metrics with the given dimensions.
     *
     * @param number $width
     * @param number $height
     * @return self
     */
    public static function create($width, $height):SizeMetrics
    {
        return new SizeMetrics($width, $height);
    }

    /**
     * @param number $width
     * @param number $height
     */
    public function __construct($width, $height)
    {
        parent::__construct($width, $height);
    }


    /**
     * Adjusts the ratio of the size, cropping one axis.
     *
     * @param number $ratio
     * @param string $align
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropToRatio($ratio, $align = "center"):Rect
    {
        $size = $ratio > 1
            ? new Size($this->width, $this->width / $ratio)
            : new Size($this->height * $ratio, $this->height);
        $offset = Alignment::parse($align)->resolve($this, $size);
        return new Rect($offset->x, $offset->y, $size->width, $size->height);
    }


    /**
     * Crop the left side of the size by an absolute or relative length.
     *
     * @param mixed $length
     * @throws \InvalidArgumentException
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropLeft($length):Rect
    {
        $v = Length::eval($length, $this->width);
        if ($v > $this->width) {
            $msg = sprintf('Unable to crop the left side by %s because the width %s is too small.', Length::parse($length), $this->width);
            throw new \InvalidArgumentException($msg);
        }
        return new Rect($v, 0, $this->width - $v, $this->height);
    }


    /**
     * Crop the right side of the size by an absolute or relative length.
     *
     * @param mixed $length
     * @throws \InvalidArgumentException
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropRight($length):Rect
    {
        $v = Length::eval($length, $this->width);
        if ($v > $this->width) {
            $msg = sprintf('Unable to crop the right side by %s because the width %s is too small.', Length::parse($length), $this->width);
            throw new \InvalidArgumentException($msg);
        }
        return new Rect(0, 0, $this->width - $v, $this->height);
    }


    /**
     * Crop the top side of the size by an absolute or relative length.
     *
     * @param mixed $length
     * @throws \InvalidArgumentException
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropTop($length):Rect
    {
        $v = Length::eval($length, $this->height);
        if ($v > $this->height) {
            $msg = sprintf('Unable to crop the right side by %s because the height %s is too small.', Length::parse($length), $this->height);
            throw new \InvalidArgumentException($msg);
        }
        return new Rect(0, $v, $this->width, $this->height - $v);
    }


    /**
     * Crop the bottom side of the size by an absolute or relative length.
     *
     * @param mixed $length
     * @throws \InvalidArgumentException
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropBottom($length):Rect
    {
        $v = Length::eval($length, $this->height);
        if ($v > $this->height) {
            $msg = sprintf('Unable to crop the right side by %s because the height %s is too small.', Length::parse($length), $this->height);
            throw new \InvalidArgumentException($msg);
        }
        return new Rect(0, 0, $this->width, $this->height - $v);
    }


    /**
     * Crop to the intersection with the given rectangle.
     *
     * @param number $x
     * @param number $y
     * @param number $width
     * @param number $height
     * @throws \InvalidArgumentException
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropRect($x, $y, $width, $height):Rect
    {
        $r1 = new Rect(0, 0, $this->width, $this->height);
        $r2 = $r1->getIntersection($x, $y, $width, $height);
        if ($r2 == null) {
            $msg = sprintf('Unable to crop to %s because the rectangle is outside of the current size.', $r1);
            throw new \InvalidArgumentException($msg);
        }
        return $r2;
    }


    /**
     * Crop the width to the given length.
     *
     * @param mixed $length
     * @param string $horizontalAlign
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropWidth($length, $horizontalAlign = 'center'):Rect
    {
        $alignment = Alignment::parseH($horizontalAlign);
        $v = Length::eval($length, $this->width);
        if ($v > $this->width) {
            $msg = sprintf('Unable to crop the width to %s because the width %s is too small.', Length::parse($length), $this->width);
            throw new \InvalidArgumentException($msg);
        }
        $cropped = new Size($v, $this->height);
        $aligned = $alignment->resolve($this, $cropped);
        return new Rect($aligned->x, $aligned->y, $cropped->width, $cropped->height);
    }


    /**
     * Crop the width by substracting the given length.
     *
     * @param mixed $length
     * @param string $horizontalAlign
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropWidthBy($length, $horizontalAlign = 'center'):Rect
    {
        $alignment = Alignment::parseH($horizontalAlign);
        $v = Length::eval($length, $this->width);
        if ($v > $this->width) {
            $msg = sprintf('Unable to crop the width by %s because the width %s is too small.', Length::parse($length), $this->width);
            throw new \InvalidArgumentException($msg);
        }
        $cropped = new Size($this->width - $v, $this->height);
        $aligned = $alignment->resolve($this, $cropped);
        return new Rect($aligned->x, $aligned->y, $cropped->width, $cropped->height);

    }


    /**
     * Crop the height to the given length.
     *
     * @param mixed $length
     * @param string $verticalAlign
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropHeight($length, $verticalAlign = 'center'):Rect
    {
        $alignment = Alignment::parseV($verticalAlign);
        $v = Length::eval($length, $this->height);
        if ($v > $this->height) {
            $msg = sprintf('Unable to crop the height to %s because the height %s is too small.', Length::parse($length), $this->height);
            throw new \InvalidArgumentException($msg);
        }

        $cropped = new Size($this->width, $v);
        $aligned = $alignment->resolve($this, $cropped);
        return new Rect($aligned->x, $aligned->y, $cropped->width, $cropped->height);
    }


    /**
     * Crop the height by substracting the given length.
     *
     * @param mixed $length
     * @param string $verticalAlign
     * @throws \InvalidArgumentException
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropHeightBy($length, $verticalAlign = 'center'):Rect
    {
        $alignment = Alignment::parseV($verticalAlign);
        $v = Length::eval($length, $this->height);
        if ($v > $this->height) {
            $msg = sprintf('Unable to crop the height by %s because the height %s is too small.', Length::parse($length), $this->height);
            throw new \InvalidArgumentException($msg);
        }
        $cropped = new Size($this->width, $this->height - $v);
        $aligned = $alignment->resolve($this, $cropped);
        return new Rect($aligned->x, $aligned->y, $cropped->width, $cropped->height);
    }


    /**
     * Crop to the given size.
     * @param mixed $width
     * @param mixed $height
     * @param string $align
     * @return \TS\Media\ImageMetrics\Rect
     */
    public function cropToSize($width, $height, $align = 'center'):Rect
    {
        $alignment = Alignment::parse($align);
        $w = Length::eval($width, $this->width);
        $h = Length::eval($height, $this->height);
        $cropped = new Size($w, $h);
        $aligned = $alignment->resolve($this, $cropped);
        return new Rect($aligned->x, $aligned->y, $cropped->width, $cropped->height);
    }


    /**
     * Crop each side individually.
     *
     * @param mixed $left
     * @param mixed $top
     * @param mixed $right
     * @param mixed $bottom
     * @return Rect
     */
    public function cropSides($left = 0, $top = 0, $right = 0, $bottom = 0):Rect
    {
        $l = Length::eval($left, $this->width);
        $t = Length::eval($top, $this->height);
        $r = Length::eval($right, $this->width);
        $b = Length::eval($bottom, $this->height);
        return new Rect($l, $t, $this->width - $l - $r, $this->height - $t - $b);
    }


    /**
     * This method behaves like CSS background-size:cover;
     * @see https://developer.mozilla.org/en-US/docs/Web/CSS/background-size
     *
     * To check whether a cover operation upsizes pixels, check
     * the "upsized" property of the result object.
     *
     * @param number|string $width
     * @param number|string $height
     * @param string $align
     * @param int $upsize
     * @return Resize
     */
    public function cover($width, $height, $align = 'center', $upsize = self::UPSIZE_ALLOW):Resize
    {

        $alignment = Alignment::parse($align);
        $requestSize = Size::parse($width, $height);

        $r = Size::compareMax($this, $requestSize);

        if (self::parsePreventUpsize($upsize, false) && ($r > 1)) {
            $targetSize = new Size($this->width, $this->height);
        } else {
            $targetSize = new Size($this->width * $r, $this->height * $r);
        }

        $targetOffset = $alignment->resolve($requestSize, $targetSize);
        $sourceRect = new Rect(0, 0, $this->width, $this->height);
        $targetRect = new Rect($targetOffset->x, $targetOffset->y, $targetSize->width, $targetSize->height);
        return new Resize($sourceRect, $targetRect);
    }


    /**
     * This method behaves like CSS background-size:contain;
     * @see https://developer.mozilla.org/en-US/docs/Web/CSS/background-size
     *
     * To check whether a cover operation upsizes pixels, check
     * the "upsized" property of the result object.
     *
     * To prevent upsizing, set $upsize to SizeMetrics::UPSIZE_PREVENT.
     * This guarantees that the source pixels are never scaled up.
     *
     * @param number|string $width
     * @param number|string $height
     * @param string $align
     * @param int $upsize
     * @return Resize
     */
    public function contain($width, $height, $align = 'center', $upsize = self::UPSIZE_ALLOW):Resize
    {

        $alignment = Alignment::parse($align);
        $requestSize = Size::parse($width, $height);

        $r = Size::compareMin($this, $requestSize);
        if (self::parsePreventUpsize($upsize, false)) {
            $r = min(1, $r);
        }

        $resized = new Size($this->width * $r, $this->height * $r);
        $targetOffset = $alignment->resolve($requestSize, $resized);

        $sourceRect = new Rect(0, 0, $this->width, $this->height);
        $targetRect = new Rect($targetOffset->x, $targetOffset->y, $resized->width, $resized->height);

        return new Resize($sourceRect, $targetRect);
    }


    /**
     * Resize to the given dimensions, ignoring aspect ratio by default
     * and upsizing if necessary.
     *
     * To keep the aspect ratio, set the flag RATIO_PRESERVE.
     * To prevent upsizing, set the flag UPSIZE_PREVENT.
     * In both cases, the resulting size may be smaller than
     * the requested size. The argument "align" will be used
     * to align the resulting size.
     *
     * Please note that the operations "cover" and "contain"
     * are better suited for common image operations like
     * generation of thumbnails.
     *
     * @param number|string $width
     * @param number|string $height
     * @param int $flags
     * @param string $align
     * @throws \InvalidArgumentException
     * @return Resize
     */
    public function resize($width, $height, $flags = self::RESIZE_STRETCH | self::UPSIZE_ALLOW | self::RATIO_IGNORE, $align = 'center'):Resize
    {

        $requestSize = Size::parse($width, $height);
        $alignment = Alignment::parse($align);

        $cover = ($flags & self::RESIZE_COVER) === self::RESIZE_COVER;
        $contain = ($flags & self::RESIZE_CONTAIN) === self::RESIZE_CONTAIN;
        $stretch = ($flags & self::RESIZE_STRETCH) === self::RESIZE_STRETCH;
        if ($cover + $contain + $stretch > 1) {
            throw new InvalidArgumentException("Multiple resize flags set.");
        }

        if ($cover) {

            return $this->cover($width, $height, $align, $flags);

        } else if ($contain) {

            return $this->contain($width, $height, $align, $flags);

        } else {
            $rx = (float)$requestSize->width / (float)$this->width;
            $ry = (float)$requestSize->height / (float)$this->height;
            if (self::parsePreventUpsize($flags, false)) {
                $rx = min(1, $rx);
                $ry = min(1, $ry);
            }

            if (self::parseKeepRatio($flags, false)) {
                $rx = $ry = min($rx, $ry);
            }

            $resized = new Size($this->width * $rx, $this->height * $ry);
            $targetOffset = $alignment->resolve($requestSize, $resized);
            $sourceRect = new Rect(0, 0, $this->width, $this->height);
            $targetRect = new Rect($targetOffset->x, $targetOffset->y, $resized->width, $resized->height);
            return new Resize($sourceRect, $targetRect);
        }
    }


    /**
     * Change the width to the given length. By default, the new
     * height is adjusted to keep the proportions.
     *
     * @param number|string $width
     * @param int $flags
     * @return Size
     */
    public function resizeWidth($width, $flags = self::RATIO_PRESERVE | self::UPSIZE_ALLOW):Size
    {
        $w = Length::eval($width, $this->width);

        $preventUp = ($flags & self::UPSIZE_PREVENT) === self::UPSIZE_PREVENT;
        if ($preventUp) {
            $w = min($this->width, $w);
        }

        $keepRatio = ($flags & self::RATIO_PRESERVE) === self::RATIO_PRESERVE;
        $h = $keepRatio ? ($w / $this->getRatio()) : $this->height;

        return new Size($w, $h);
    }


    /**
     * Change the height to the given length. By default, the new
     * width is adjusted to keep the proportions.
     *
     * @param number|string $height
     * @param int $flags
     * @return Size
     */
    public function resizeHeight($height, $flags = self::RATIO_PRESERVE | self::UPSIZE_ALLOW):Size
    {
        $h = Length::eval($height, $this->height);

        $preventUp = ($flags & self::UPSIZE_PREVENT) === self::UPSIZE_PREVENT;
        if ($preventUp) {
            $h = min($this->height, $h);
        }

        $keepRatio = ($flags & self::RATIO_PRESERVE) === self::RATIO_PRESERVE;
        $w = $keepRatio ? ($h * $this->getRatio()) : $this->width;

        return new Size($w, $h);
    }


    private static function parseUpsizeAllow(int $flags, bool $default):bool
    {
        $allow = ($flags & self::UPSIZE_ALLOW) === self::UPSIZE_ALLOW;
        $prevent = ($flags & self::UPSIZE_PREVENT) === self::UPSIZE_PREVENT;
        if ($allow && $prevent) {
            throw new InvalidArgumentException("Cannot set both UPSIZE_ALLOW and UPSIZE_PREVENT.");
        }
        if ($allow) {
            return true;
        }
        if ($prevent) {
            return false;
        }
        return $default;
    }


    private static function parsePreventUpsize(int $flags, bool $default):bool
    {
        $allow = ($flags & self::UPSIZE_ALLOW) === self::UPSIZE_ALLOW;
        $prevent = ($flags & self::UPSIZE_PREVENT) === self::UPSIZE_PREVENT;
        if ($allow && $prevent) {
            throw new InvalidArgumentException("Cannot set both UPSIZE_ALLOW and UPSIZE_PREVENT.");
        }
        if ($allow) {
            return false;
        }
        if ($prevent) {
            return true;
        }
        return $default;
    }


    private static function parseKeepRatio(int $flags, bool $default):bool
    {
        $keep = ($flags & self::RATIO_PRESERVE) === self::RATIO_PRESERVE;
        $ignore = ($flags & self::RATIO_IGNORE) === self::RATIO_IGNORE;
        if ($keep && $ignore) {
            throw new InvalidArgumentException("Cannot set both RATIO_PRESERVE and RATIO_IGNORE.");
        }
        if ($keep) {
            return true;
        }
        if ($ignore) {
            return false;
        }
        return $default;
    }


}

