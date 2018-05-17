<?php


use PHPUnit\Framework\TestCase;
use TS\Media\ImageMetrics\Rect;


class RectTest extends TestCase
{


    public function testMoveBy()
    {
        $r = new Rect(-12.5, 0, 75, 50);
        $r->moveBy(12.5, 0);
        $this->assertEquals(0, $r->x);
    }

    public function testIntersecsBetweenTwoRectangles()
    {
        $r1 = new Rect(0, 0, 100, 100);
        $r2 = new Rect(101, 101, 100, 100);

        $this->assertFalse($r1->intersects($r2->left, $r2->top, $r2->width, $r2->height));
    }


    public function testIntersectsWithNegativeWidth()
    {
        $r1 = new Rect(0, 0, 100, 100);
        $r2 = new Rect(110, 110, -20, -20);

        $this->assertTrue($r1->intersects($r2->left, $r2->top, $r2->width, $r2->height));
    }


    public function testIntersection()
    {
        $r1 = new Rect(0, 0, 100, 100);
        $this->assertSame(0, $r1->left);
        $this->assertSame(0, $r1->top);
        $this->assertSame(100, $r1->right);
        $this->assertSame(100, $r1->bottom);


        $r2 = new Rect(90, 90, 20, 20);
        $this->assertSame(90, $r2->left);
        $this->assertSame(90, $r2->top);
        $this->assertSame(110, $r2->right);
        $this->assertSame(110, $r2->bottom);
        $this->assertTrue($r1->intersects($r2->left, $r2->top, $r2->width, $r2->height));


        $i1 = $r1->getIntersection($r2->left, $r2->top, $r2->width, $r2->height);
        $this->assertSame($i1->x, $i1->left);
        $this->assertSame($i1->y, $i1->top);

        $this->assertSame(90, $i1->left);
        $this->assertSame(90, $i1->top);
        $this->assertSame(10, $i1->width);
        $this->assertSame(10, $i1->height);

    }


    public function testNegativeWidth()
    {

        $r = new Rect(10, 20, -10, -20);

        $this->assertEquals(10, $r->x);
        $this->assertEquals(20, $r->y);
        $this->assertEquals(-10, $r->width);
        $this->assertEquals(-20, $r->height);
        $this->assertEquals(0, $r->left);
        $this->assertEquals(10, $r->right);
        $this->assertEquals(0, $r->top);
        $this->assertEquals(20, $r->bottom);

    }


    public function testNormalizeNegativeWidth()
    {

        $r = new Rect(10, 20, -10, -20);
        $r->normalize();

        $this->assertEquals(0, $r->x);
        $this->assertEquals(0, $r->y);
        $this->assertEquals(10, $r->width);
        $this->assertEquals(20, $r->height);
        $this->assertEquals(0, $r->left);
        $this->assertEquals(10, $r->right);
        $this->assertEquals(0, $r->top);
        $this->assertEquals(20, $r->bottom);

    }


}
