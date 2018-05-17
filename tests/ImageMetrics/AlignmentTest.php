<?php


namespace TS\Media\ImageMetrics;

use PHPUnit\Framework\TestCase;


class AlignmentTest extends TestCase
{


    public function testParseOffsets()
    {

        $a = Alignment::parse("left bottom 10px");
        $this->assertEquals(0, $a->x->percent);
        $this->assertEquals(100, $a->y->percent);
        $this->assertEquals(-10, $a->offsetY->pixel);

        $a = Alignment::parse("left top 10");
        $this->assertEquals(0, $a->x->percent);
        $this->assertEquals(0, $a->y->percent);
        $this->assertEquals(10, $a->offsetY->pixel);

        $a = Alignment::parse("left bottom 10%");
        $this->assertEquals(0, $a->x->percent);
        $this->assertEquals(100, $a->y->percent);
        $this->assertEquals(-10, $a->offsetY->percent);

        $a = Alignment::parse("right 10% top 10px");
        $this->assertEquals(100, $a->x->percent);
        $this->assertEquals(-10, $a->offsetX->percent);
        $this->assertEquals(0, $a->y->percent);
        $this->assertEquals(10, $a->offsetY->pixel);

    }


    public function testAlignSingleValue()
    {

        $a = Alignment::parse("left bottom");
        $this->assertEquals(0, $a->x->percent);
        $this->assertEquals(100, $a->y->percent);

        $a = Alignment::parse("bottom left");
        $this->assertEquals(0, $a->x->percent);
        $this->assertEquals(100, $a->y->percent);


        $a = Alignment::parse("left 50%");
        $this->assertEquals(0, $a->x->percent);
        $this->assertEquals(50, $a->y->percent);

        $a = Alignment::parse("50% left");
        $this->assertEquals(0, $a->x->percent);
        $this->assertEquals(50, $a->y->percent);


        $a = Alignment::parse("50% bottom");
        $this->assertEquals(50, $a->x->percent);
        $this->assertEquals(100, $a->y->percent);

        $a = Alignment::parse("bottom 50%");
        $this->assertEquals(50, $a->x->percent);
        $this->assertEquals(100, $a->y->percent);


        $a = Alignment::parse("right");
        $this->assertEquals(100, $a->x->percent);
        $this->assertEquals(50, $a->y->percent);


        $a = Alignment::parse("bottom");
        $this->assertEquals(50, $a->x->percent);
        $this->assertEquals(100, $a->y->percent);


        $a = Alignment::parse("40% 100%");
        $this->assertEquals(40, $a->x->percent);
        $this->assertEquals(100, $a->y->percent);


        $a = Alignment::parseH("40%");
        $this->assertEquals(40, $a->x->percent);
        $this->assertEquals(50, $a->y->percent);

        $a = Alignment::parseV("40%");
        $this->assertEquals(50, $a->x->percent);
        $this->assertEquals(40, $a->y->percent);


        $a = Alignment::parse("40%");
        $this->assertEquals(40, $a->x->percent);
        $this->assertEquals(50, $a->y->percent);
        $a = Alignment::parse("right");
        $this->assertEquals(100, $a->x->percent);
        $this->assertEquals(50, $a->y->percent);

    }


    public function testAlignCenter()
    {
        $stage = new Size(640, 480);
        $child = new Size(200, 100);

        $p = Alignment::parse("center 50%")->resolve($stage, $child);
        $this->assertEquals(220, $p->x);
        $this->assertEquals(190, $p->y);

        $p = Alignment::parse("50% center")->resolve($stage, $child);
        $this->assertEquals(220, $p->x);
        $this->assertEquals(190, $p->y);

        $p = Alignment::parse("50% 50%")->resolve($stage, $child);
        $this->assertEquals(220, $p->x);
        $this->assertEquals(190, $p->y);

        $p = Alignment::parse("50%")->resolve($stage, $child);
        $this->assertEquals(220, $p->x);
        $this->assertEquals(190, $p->y);
    }


    public function testAlignRightBottom()
    {
        $stage = new Size(640, 480);
        $child = new Size(200, 100);

        $p = Alignment::parse("right bottom")->resolve($stage, $child);
        $this->assertEquals(440, $p->x);
        $this->assertEquals(380, $p->y);
    }


    public function testAlignLeftBottom()
    {
        $stage = new Size(640, 480);
        $child = new Size(200, 100);

        $p = Alignment::parse("0 bottom")->resolve($stage, $child);
        $this->assertEquals(0, $p->x);
        $this->assertEquals(380, $p->y);

        $p = Alignment::parse("0% 100%")->resolve($stage, $child);
        $this->assertEquals(0, $p->x);
        $this->assertEquals(380, $p->y);

        $p = Alignment::parse("left bottom")->resolve($stage, $child);
        $this->assertEquals(0, $p->x);
        $this->assertEquals(380, $p->y);
    }


    public function testAlignPoint()
    {
        $stage = new Size(640, 480);
        $child = new Point(0, 0);

        $p = Alignment::parse("center")->resolve($stage, $child);
        $this->assertEquals(320, $p->x);
        $this->assertEquals(240, $p->y);
    }


    public function testAlignRect()
    {
        $stage = new Rect(100, 200, 640, 480);
        $child = new Point(0, 0);

        $p = Alignment::parse("center")->resolve($stage, $child);
        $this->assertEquals(320 + 100, $p->x);
        $this->assertEquals(240 + 200, $p->y);
    }


    public function testAlignSizeInRect()
    {
        $stage = new Rect(100, 200, 640, 480);
        $child = new Size(30, 20);

        $p = Alignment::parse("right bottom")->resolve($stage, $child);
        $this->assertEquals(640 + 100 - 30, $p->x);
        $this->assertEquals(480 + 200 - 20, $p->y);
    }


}
