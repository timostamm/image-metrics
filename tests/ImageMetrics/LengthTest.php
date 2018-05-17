<?php 


namespace TS\Media\ImageMetrics;


use PHPUnit\Framework\TestCase;


class LengthTest extends TestCase
{


	public function testParsePx()
	{

		$l = Length::parse("10px");
		$this->assertSame(  false, $l->percent );
		$this->assertSame(   10.0, $l->pixel );

		$l = Length::parse("10");
		$this->assertSame(  false, $l->percent );
		$this->assertSame(   10.0, $l->pixel );

		$l = Length::parse("-10.75");
		$this->assertSame(  false, $l->percent );
		$this->assertSame( -10.75, $l->pixel );
		
	}
	
	
	public function testParsePercent()
	{
	
		$l = Length::parse("10%");
		$this->assertSame(   10.0, $l->percent );
		$this->assertSame(  false, $l->pixel );
	
	}
	


	public function testTryParse()
	{
		$ok = Length::tryParse("10%", $l);
		$this->assertTrue( $ok );
		$this->assertSame(   10.0, $l->percent );
		$this->assertSame(  false, $l->pixel );
	
		$ok = Length::tryParse("10 %", $l2);
		$this->assertFalse( $ok );
		$this->assertNull( $l2 );
	
	}
	
	
}
