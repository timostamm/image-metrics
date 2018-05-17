<?php

use PHPUnit\Framework\TestCase;
use TS\Media\ImageMetrics\Rect;
use TS\Media\ImageMetrics\Resize;

class ResizeTest extends TestCase
{

	public function testNormalization()
	{
		$r = new Resize(new Rect(0, 0, 150, 100), new Rect(-12.5, 0, 75, 50));
		$this->assertEquals(new Rect(25, 0, 125, 100), $r->fromRect);
		$this->assertEquals(new Rect(0, 0, 62.5, 50), $r->toRect);
	}

}
