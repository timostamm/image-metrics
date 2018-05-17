<?php


use PHPUnit\Framework\TestCase;
use TS\Media\ImageMetrics\Rect;
use TS\Media\ImageMetrics\Size;
use TS\Media\ImageMetrics\SizeMetrics;


class SizeMetricsTest extends TestCase
{

    public function test_createFromSize()
    {
        $s = SizeMetrics::createFromSize(new Size(150, 100));
        $this->assertSame(150, $s->width);
        $this->assertSame(100, $s->height);
    }


    public function test_create()
    {
        $s = SizeMetrics::create(150, 100);
        $this->assertSame(150, $s->width);
        $this->assertSame(100, $s->height);
    }


    public function test_construct()
    {
        $s = new SizeMetrics(150, 100);
        $this->assertSame(150, $s->width);
        $this->assertSame(100, $s->height);
    }


    public function test_cropToRatio()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropToRatio($s->getRatio(), 'center');
        $this->assertEquals($s->width, $r->width);
        $this->assertEquals($s->height, $r->height);

        $r = $s->cropToRatio(1 / 1, 'center');
        $this->assertEquals(100, $r->width);
        $this->assertEquals(100, $r->height);

        $r = $s->cropToRatio(2 / 3, 'center');
        $this->assertEquals(66.66666666666666, $r->width);
        $this->assertEquals(100, $r->height);
    }


    public function test_cropLeft()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropLeft(90);
        $this->assertSame(60, $r->width);
        $this->assertSame(90, $r->left);
        $this->assertSame(100, $s->height);
    }

    public function test_cropLeftPercent()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropLeft('50%');
        $this->assertEquals(75, $r->width);
        $this->assertEquals(75, $r->left);
        $this->assertSame(100, $s->height);
    }

    public function test_cropLeftTooMuch()
    {
        $s = new SizeMetrics(150, 100);
        $this->expectException(InvalidArgumentException::class);
        $s->cropLeft('110%');
    }


    public function test_cropRight()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropRight(90);
        $this->assertSame(60, $r->width);
        $this->assertSame(0, $r->left);
        $this->assertSame(100, $s->height);
    }

    public function test_cropRightPercent()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropRight('50%');
        $this->assertEquals(75, $r->width);
        $this->assertSame(0, $r->left);
        $this->assertSame(100, $s->height);
    }

    public function test_cropRightTooMuch()
    {
        $s = new SizeMetrics(150, 100);
        $this->expectException(InvalidArgumentException::class);
        $s->cropRight('110%');
    }


    public function test_cropTop()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropTop(20);
        $this->assertSame(80, $r->height);
        $this->assertSame(20, $r->top);
        $this->assertSame(0, $r->left);
        $this->assertSame(150, $s->width);
    }

    public function test_cropTopPercent()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropTop('20%');
        $this->assertEquals(80, $r->height);
        $this->assertEquals(20, $r->top);
        $this->assertSame(0, $r->left);
        $this->assertSame(150, $s->width);
    }

    public function test_cropTopTooMuch()
    {
        $s = new SizeMetrics(150, 100);
        $this->expectException(InvalidArgumentException::class);
        $s->cropTop('110%');
    }


    public function test_cropBottom()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropBottom(20);
        $this->assertSame(80, $r->height);
        $this->assertEquals(0, $r->top);
        $this->assertEquals(80, $r->bottom);
        $this->assertSame(0, $r->left);
        $this->assertSame(150, $s->width);
    }

    public function test_cropBottomPercent()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropBottom('20%');
        $this->assertEquals(80, $r->height);
        $this->assertEquals(0, $r->top);
        $this->assertEquals(80, $r->bottom);
        $this->assertSame(0, $r->left);
        $this->assertSame(150, $s->width);
    }

    public function test_cropBottomTooMuch()
    {
        $s = new SizeMetrics(150, 100);
        $this->expectException(InvalidArgumentException::class);
        $s->cropBottom('110%');
    }


    public function test_cropRect()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropRect(10, 20, 75, 50);
        $this->assertEquals(10, $r->left);
        $this->assertEquals(20, $r->top);
        $this->assertEquals(75, $r->width);
        $this->assertEquals(50, $r->height);
    }


    public function test_cropWidth()
    {
        $s = new SizeMetrics(150, 100);

        $r = $s->cropWidth('50%', 'right');
        $this->assertEquals(75, $r->left);
        $this->assertEquals(75, $r->width);

        $r = $s->cropWidth('50%', '50%');
        $this->assertEquals(37.5, $r->left);
        $this->assertEquals(75, $r->width);

        $r = $s->cropWidth('50%', '10px');
        $this->assertEquals(10, $r->left);
        $this->assertEquals(75, $r->width);
    }

    public function test_cropWidthTooLarge()
    {
        $s = new SizeMetrics(150, 100);
        $this->expectException(InvalidArgumentException::class);
        $s->cropWidth('110%');
    }


    public function test_cropHeight()
    {
        $s = new SizeMetrics(150, 100);

        $r = $s->cropHeight('50%', 'top');
        $this->assertEquals(0, $r->top);
        $this->assertEquals(50, $r->height);

        $r = $s->cropHeight('50%', 'center');
        $this->assertEquals(25, $r->top);
        $this->assertEquals(50, $r->height);

        $r = $s->cropHeight('50%', '10px');
        $this->assertEquals(10, $r->top);
        $this->assertEquals(50, $r->height);
    }

    public function test_cropHeightTooLarge()
    {
        $s = new SizeMetrics(150, 100);
        $this->expectException(InvalidArgumentException::class);
        $s->cropHeight('110%');
    }


    public function test_cropWidthBy()
    {
        $s = new SizeMetrics(150, 100);

        $r = $s->cropWidthBy('10px', 'right');
        $this->assertEquals(10, $r->left);
        $this->assertEquals(140, $r->width);

        $r = $s->cropWidthBy('10px', 'left');
        $this->assertEquals(0, $r->left);
        $this->assertEquals(140, $r->width);
    }


    public function test_cropHeightBy()
    {
        $s = new SizeMetrics(150, 100);

        $r = $s->cropHeightBy('10px', 'top');
        $this->assertEquals(0, $r->top);
        $this->assertEquals(90, $r->height);

        $r = $s->cropHeightBy('10px', 'bottom');
        $this->assertEquals(10, $r->top);
        $this->assertEquals(90, $r->height);
    }


    public function test_cropToSize()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropToSize(75, 50, 'center');
        $this->assertEquals(37.5, $r->left);
        $this->assertEquals(75, $r->width);
        $this->assertEquals(25, $r->top);
        $this->assertEquals(50, $r->height);
    }


    public function test_cropSides()
    {
        $s = new SizeMetrics(150, 100);
        $r = $s->cropSides('20px', '30px', '10px', '10px');
        $this->assertEquals(20, $r->left);
        $this->assertEquals(30, $r->top);
        $this->assertEquals(120, $r->width);
        $this->assertEquals(60, $r->height);
    }


    public function test_cover()
    {
        $s = new SizeMetrics(150, 100);
        $c = $s->cover(50, 50, 'center');

        $this->assertEquals(new Rect(0, 0, 150, 100), $c->fromRectInput);
        $this->assertEquals(new Rect(-12.5, 0, 75, 50), $c->toRectInput);
        $this->assertFalse($c->upsized);
    }


    public function test_cover_upsizing()
    {
        $s = new SizeMetrics(150, 100);
        $c = $s->cover(200, 200, 'center');

        $this->assertEquals(new Rect(0, 0, 150, 100), $c->fromRectInput);
        $this->assertEquals(new Rect(-50, 0, 300, 200), $c->toRectInput);
        $this->assertTrue($c->upsized);
    }


    public function test_cover_no_upsizing()
    {
        $s = new SizeMetrics(150, 100);
        $c = $s->cover(200, 200, 'center', SizeMetrics::UPSIZE_PREVENT);

        $this->assertEquals(new Rect(0, 0, 150, 100), $c->fromRectInput);
        $this->assertEquals(new Rect(25, 50, 150, 100), $c->toRectInput);
        $this->assertFalse($c->upsized);
    }


    public function test_contain()
    {
        $s = new SizeMetrics(200, 100);

        $c = $s->contain(50, 50, 'center');
        $this->assertFalse($c->upsized);
        $this->assertEquals(new Rect(0, 0, 200, 100), $c->fromRectInput);
        $this->assertEquals(new Rect(0, 12.5, 50, 25), $c->toRectInput);

        $c = $s->contain(50, 50, 'bottom');
        $this->assertEquals(25, $c->toRectInput->top);
    }


    public function test_contain_upsizing()
    {
        $s = new SizeMetrics(200, 100);

        $c = $s->contain(500, 500, 'center', SizeMetrics::UPSIZE_ALLOW);

        $this->assertTrue($c->upsized);
        $this->assertEquals(new Rect(0, 0, 200, 100), $c->fromRectInput);
        $this->assertEquals(new Rect(0, 125, 500, 250), $c->toRectInput);
    }


    public function test_contain_no_upsizing()
    {
        $s = new SizeMetrics(200, 100);

        $c = $s->contain(500, 500, 'bottom right', SizeMetrics::UPSIZE_PREVENT);

        $this->assertFalse($c->upsized);
        $this->assertEquals(new Rect(0, 0, 200, 100), $c->fromRectInput);
        $this->assertEquals(new Rect(300, 400, 200, 100), $c->toRectInput);
    }


    public function test_resizeWidth()
    {
        $s = new SizeMetrics(200, 100);

        $r = $s->resizeWidth(100);
        $this->assertEquals(100, $r->width);
        $this->assertEquals(50, $r->height);

        $r = $s->resizeWidth(100, SizeMetrics::RATIO_IGNORE);
        $this->assertEquals(100, $r->width);
        $this->assertEquals(100, $r->height);

        $r = $s->resizeWidth(400);
        $this->assertEquals(400, $r->width);
        $this->assertEquals(200, $r->height);

        $r = $s->resizeWidth(400, SizeMetrics::RATIO_IGNORE);
        $this->assertEquals(400, $r->width);
        $this->assertEquals(100, $r->height);

        $r = $s->resizeWidth(400, SizeMetrics::UPSIZE_PREVENT);
        $this->assertEquals(200, $r->width);
        $this->assertEquals(100, $r->height);
    }


    public function test_resizeHeight()
    {
        $s = new SizeMetrics(200, 100);

        $r = $s->resizeHeight(50);
        $this->assertEquals(new Size(100, 50), $r);

        $r = $s->resizeHeight(50, SizeMetrics::RATIO_IGNORE);
        $this->assertEquals(new Size(200, 50), $r);

        $r = $s->resizeHeight(200);
        $this->assertEquals(new Size(400, 200), $r);

        $r = $s->resizeHeight(200, SizeMetrics::RATIO_IGNORE);
        $this->assertEquals(new Size(200, 200), $r);

        $r = $s->resizeHeight(200, SizeMetrics::UPSIZE_PREVENT);
        $this->assertEquals(200, $r->width);
        $this->assertEquals(100, $r->height);
    }


    public function test_resize()
    {
        $s = new SizeMetrics(200, 100);

        $r = $s->resize(50, 75);
        $this->assertEquals(50, $r->toRectInput->width);
        $this->assertEquals(75, $r->toRectInput->height);
    }


    public function test_resize_upsize()
    {
        $s = new SizeMetrics(200, 100);

        $r = $s->resize(500, 500);

        $this->assertEquals(new Rect(0, 0, 200, 100), $r->fromRectInput);
        $this->assertEquals(new Rect(0, 0, 500, 500), $r->toRectInput);
    }

    public function test_resize_keepRatio()
    {
        $s = new SizeMetrics(200, 100);

        $r = $s->resize(500, 500, SizeMetrics::RATIO_PRESERVE);

        $this->assertEquals(new Rect(0, 0, 200, 100), $r->fromRectInput);
        $this->assertEquals(new Rect(0, 125, 500, 250), $r->toRectInput);
    }

    public function test_resize_prevent_upsize()
    {
        $s = new SizeMetrics(200, 100);

        $r = $s->resize(500, 500, SizeMetrics::UPSIZE_PREVENT);

        $this->assertEquals(new Rect(0, 0, 200, 100), $r->fromRectInput);
        $this->assertEquals(new Rect(150, 200, 200, 100), $r->toRectInput);
    }


    public function test_resize_contain()
    {
        $s = new SizeMetrics(200, 100);

        $c = $s->resize(50, 50, SizeMetrics::RESIZE_CONTAIN, 'center');
        $this->assertFalse($c->upsized);
        $this->assertEquals(new Rect(0, 0, 200, 100), $c->fromRectInput);
        $this->assertEquals(new Rect(0, 12.5, 50, 25), $c->toRectInput);

        $c = $s->resize(50, 50, SizeMetrics::RESIZE_CONTAIN, 'bottom');
        $this->assertEquals(25, $c->toRectInput->top);
    }


    public function test_resize_cover()
    {
        $s = new SizeMetrics(150, 100);
        $c = $s->resize(50, 50, SizeMetrics::RESIZE_COVER, 'center');

        $this->assertFalse($c->upsized);
        $this->assertEquals(new Rect(0, 0, 150, 100), $c->fromRectInput);
        $this->assertEquals(new Rect(-12.5, 0, 75, 50), $c->toRectInput);
    }


}
