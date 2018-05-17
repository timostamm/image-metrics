<?php

namespace TS\Media\ImageMetrics;



/**
 * @author Timo Stamm <ts@timostamm.de>
 * @license AGPLv3.0 https://www.gnu.org/licenses/agpl-3.0.txt
 */
class Point {
	
	
	public $x;
	public $y;
	
	
	public function __construct( $x, $y ) {
		if ( ! is_numeric( $x )) {
			$msg = sprintf('Expected argument $x to be numeric, but got a %s', gettype($x));
			throw new \UnexpectedValueException($msg);
		}
		if ( ! is_numeric( $y )) {
			$msg = sprintf('Expected argument $y to be numeric, but got a %s', gettype($y));
			throw new \UnexpectedValueException($msg);
		}
		$this->x = $x;
		$this->y = $y;
	}
	
	
	public function floor() {
		$this->x = floor( $this->x );
		$this->y = floor( $this->y );
	}

	public function ceil() {
		$this->x = ceil( $this->x );
		$this->y = ceil( $this->y );
	}
	
	public function round() {
		$this->x = round( $this->x );
		$this->y = round( $this->y );
	}
	
	
}

