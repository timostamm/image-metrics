<?php

namespace TS\Media\ImageMetrics;


/**
 *
 * Box alignment implementation.
 *
 * Supports pretty much every alignment that is supported by
 * CSS background-position.
 *
 *
 * @author Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class Alignment
{


    // TODO after PHP 5.6, use expression in constants to insert length-re


    const RE_horizontal_keyword_and_vertical_keyword
        = '/^(left|right|center)(?:\s+(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?))?\s+(top|bottom|center)(?:\s+(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?))?$/';

    const RE_vertical_keyword_and_horizontal_keyword
        = '/^(top|bottom|center)(?:\s+(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?))?\s+(left|right|center)(?:\s+(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?))?$/';


    const RE_horizontal_keyword_and_vertical_length
        = '/^(left|right|center)\s+(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?)$/';

    const RE_vertical_length_and_horizontal_keyword
        = '/^(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?)\s+(left|right|center)$/';


    const RE_horizontal_length_and_vertical_keyword
        = '/^(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?)\s+(top|bottom|center)$/';

    const RE_vertical_keyword_and_horizontal_length
        = '/^(top|bottom|center)\s+(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?)$/';


    const RE_horizontal_keyword
        = '/^(left|right|center)$/';

    const RE_vertical_keyword
        = '/^(top|bottom|center)$/';


    const RE_horizontal_any_and_vertical_any
        = '/^(left|right|center|-?[0-9]+(?:%|px)?)\s+(top|bottom|center|-?[0-9]+(?:%|px)?)$/';


    const RE_horizontal_any
        = '/^(left|right|center|-?[0-9]+(?:%|px)?)$/';


    /**
     * Parses an alignment string.
     *
     * Align with a single keyword: (other value becomes "center")
     * left
     * right
     * top
     * bottom
     *
     * Align with two keywords:
     * left top
     * top left
     *
     * Align with percentage values:
     * 25% 75% ( first value is horizontal, second value is vertical )
     *
     * Align with pixel values:
     * 10 20   ( first value is horizontal, second value is vertical )
     *
     * Mixing keyword and other values:
     * left 10 ( 10 px from the top )
     * 10 left ( also 10 px from the top )
     *
     * Edge offsets:
     * left 10 top 10
     * right bottom 10%
     *
     * @param string $str
     * @throws \Exception
     * @return Alignment
     */
    public static function parse($str)
    {

        preg_match(self::RE_horizontal_keyword_and_vertical_keyword, $str, $m);
        if (count($m) > 1) {

            $a = new Alignment($m[1], $m[3]);

            if (Length::tryParse(empty($m[2]) ? '' : $m[2], $ox)) {
                $a->offsetX = $ox;
                if ($a->x->percent === 100) {
                    $a->offsetX->changeValue(function ($v) {
                        return -$v;
                    });
                }
            }

            if (Length::tryParse(empty($m[4]) ? '' : $m[4], $oy)) {
                $a->offsetY = $oy;
                if ($a->y->percent === 100) {
                    $a->offsetY->changeValue(function ($v) {
                        return -$v;
                    });
                }
            }

            return $a;
        }

        preg_match(self::RE_vertical_keyword_and_horizontal_keyword, $str, $m);
        if (count($m) > 1) {

            $a = new Alignment($m[3], $m[1]);

            if (Length::tryParse(empty($m[4]) ? '' : $m[4], $ox)) {
                $a->offsetX = $ox;
                if ($a->x->percent === 100) {
                    $a->offsetX->changeValue(function ($v) {
                        return -$v;
                    });
                }
            }

            if (Length::tryParse(empty($m[2]) ? '' : $m[2], $oy)) {
                $a->offsetY = $oy;
                if ($a->y->percent === 100) {
                    $a->offsetY->changeValue(function ($v) {
                        return -$v;
                    });
                }
            }

            return $a;
        }


        preg_match(self::RE_horizontal_keyword_and_vertical_length, $str, $m);
        if (count($m) === 3) {
            return new Alignment($m[1], $m[2]);
        }

        preg_match(self::RE_vertical_length_and_horizontal_keyword, $str, $m);
        if (count($m) === 3) {
            return new Alignment($m[2], $m[1]);
        }


        preg_match(self::RE_horizontal_length_and_vertical_keyword, $str, $m);
        if (count($m) === 3) {
            return new Alignment($m[1], $m[2]);
        }

        preg_match(self::RE_vertical_keyword_and_horizontal_length, $str, $m);
        if (count($m) === 3) {
            return new Alignment($m[2], $m[1]);
        }


        preg_match(self::RE_horizontal_keyword, $str, $m);
        if (count($m) === 2) {
            return new Alignment($m[1], 'center');
        }

        preg_match(self::RE_vertical_keyword, $str, $m);
        if (count($m) === 2) {
            return new Alignment('center', $m[1]);
        }


        preg_match(self::RE_horizontal_any_and_vertical_any, $str, $m);
        if (count($m) === 3) {
            return new Alignment($m[1], $m[2]);
        }


        preg_match(self::RE_horizontal_any, $str, $m);
        if (count($m) === 2) {
            return new Alignment($m[1], 'center');
        }


        throw new \Exception('invalid alignment: ' . $str);
    }


    public static function tryParse($str, & $a)
    {
        try {
            $a = self::parse($str);
            return true;
        } catch (\Exception $ex) {
            $a = null;
            return false;
        }
    }


    const RE_horizontal_keyword_and_offset
        = '/^(left|right|center)(?:\s+(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?))?$/';


    /**
     * Parse a horizontal alignment string, use "center" for vertical alignment.
     *
     * @param string $str
     * @throws \Exception
     * @return Alignment
     */
    public static function parseH($str)
    {

        preg_match(self::RE_horizontal_keyword_and_offset, $str, $m);
        if (count($m) === 2) {

            $a = new Alignment($m[1], 'center');

            if (Length::tryParse(empty($m[2]) ? '' : $m[2], $ox)) {
                $a->offsetX = $ox;
                if ($a->x->percent === 100) {
                    $a->offsetX->changeValue(function ($v) {
                        return -$v;
                    });
                }
            }

            return $a;

        }

        if (Length::tryParse($str, $len)) {
            return new Alignment($str, 'center');
        }

        throw new \Exception('invalid alignment: ' . $str);
    }


    const RE_vertical_keyword_and_offset
        = '/^(top|bottom|center)(?:\s+(-?[0-9]+(?:\.[0-9]+)?(?:%|px)?))?$/';


    /**
     * Parse a horizontal alignment string, use "center" for horizontal alignment.
     *
     * @param string $str
     * @throws \Exception
     * @return Alignment
     */
    public static function parseV($str)
    {

        preg_match(self::RE_vertical_keyword_and_offset, $str, $m);
        if (count($m) > 1) {

            $a = new Alignment('center', $m[1]);

            if (Length::tryParse(empty($m[2]) ? '' : $m[2], $ox)) {
                $a->offsetY = $ox;
                if ($a->y->percent === 100) {
                    $a->offsetY->changeValue(function ($v) {
                        return -$v;
                    });
                }
            }

            return $a;

        }

        if (Length::tryParse($str, $len)) {
            return new Alignment('center', $str);
        }

        throw new \Exception('invalid alignment: ' . $str);
    }


    public $x;
    public $y;
    public $offsetX;
    public $offsetY;


    public function __construct($horizontal, $vertical, $horizontalOffset = null, $verticalOffset = null)
    {
        $this->x = $this->normalizeAxisLength($horizontal);
        $this->y = $this->normalizeAxisLength($vertical);
        $this->offsetX = $horizontalOffset === null ? new Length(0) : Length::parse($horizontalOffset);
        $this->offsetY = $verticalOffset === null ? new Length(0) : Length::parse($verticalOffset);
    }


    /**
     * Align a Size (or a Point) in a Size (or Rect).
     *
     * @param Size|Rect $container
     * @param Size|Point $child
     * @throws \Exception
     * @return Point
     */
    public function resolve($container, $child)
    {
        $outer = $this->rectFromSizeOrRect($container);
        $inner = $this->rectFromPointOrSize($child);
        if ($outer === null) {
            throw new \Exception('invalid container type');
        }
        if ($inner === null) {
            throw new \Exception('invalid child type');
        }

        $dx = $outer->width - $inner->width;
        $dy = $outer->height - $inner->height;

        $x = $this->x->resolve($dx)
            + $this->offsetX->resolve($dx)
            + $outer->x;

        $y = $this->y->resolve($dy)
            + $this->offsetY->resolve($dy)
            + $outer->y;

        return new Point($x, $y);
    }


    /**
     * Normalizes a alignment value for one axis to a pixel or a percent value.
     * Named alignment values like "middle" are translated to a percentage.
     *
     * The method always returns either a pixel value or a percent value and
     * sets the unused value to false.
     *
     * @param mixed $value the alignment value, for example "left", "bottom", 150, "75%"
     * @return array Array with pixel value and percent value.
     */
    private function normalizeAxisLength($value)
    {
        if ($value === 'left' || $value === 'top') {
            return new Length(0, false);
        }
        if ($value === 'right' || $value === 'bottom') {
            return new Length(false, 100);
        }
        if ($value === 'center') {
            return new Length(false, 50);
        }
        return Length::parse($value);
    }


    private function rectFromSizeOrRect($item)
    {
        if ($item instanceof Rect) {
            return $item;
        }
        if ($item instanceof Size) {
            return new Rect(0, 0, $item->width, $item->height);
        }
        return null;
    }


    private function rectFromPointOrSize($item)
    {
        if ($item instanceof Point) {
            return new Rect($item->x, $item->y, 0, 0);
        }
        if ($item instanceof Size) {
            return new Rect(0, 0, $item->width, $item->height);
        }
        return null;
    }

}

