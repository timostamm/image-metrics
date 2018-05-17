<?php

namespace TS\Media\ImageMetrics;


/**
 *
 * @author Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class Resize
{
    /**
     *
     * @var Rect
     */
    public $fromRectInput;

    /**
     *
     * @var Rect
     */
    public $toRectInput;

    /**
     *
     * @var Rect
     */
    public $fromRect;

    /**
     *
     * @var Rect
     */
    public $toRect;

    /**
     *
     * @var bool
     */
    public $upsized;


    public function __construct(Rect $sourceRect, Rect $targetRect)
    {
        $this->fromRectInput = $sourceRect;
        $this->toRectInput = $targetRect;
        $this->upsized = $targetRect->width > $this->fromRectInput->width || $targetRect->height > $this->fromRectInput->height;
        $this->normalizeToRect();
    }

    /**
     * Adjusts negative offsets in the target.
     */
    private function normalizeToRect():void
    {
        $rx = $this->fromRectInput->width / $this->toRectInput->width;
        $ry = $this->fromRectInput->height / $this->toRectInput->height;
        $from = clone $this->fromRectInput;
        $to = clone $this->toRectInput;

        $toDx = $toDy = $fromDx = $fromDy = 0;
        if ($this->toRectInput->x < 0) {
            $toDx = abs($this->toRectInput->x);
            $fromDx = $toDx * $rx;
        }
        if ($this->toRectInput->y < 0) {
            $toDy = abs($this->toRectInput->y);
            $fromDy = $toDy * $ry;
        }
        $from->moveBy($fromDx, $fromDy);
        $from->resizeBy(-$fromDx, -$fromDy);
        $to->moveBy($toDx, $toDy);
        $to->resizeBy(-$toDx, -$toDy);

        $this->fromRect = $from;
        $this->toRect = $to;
    }

}

