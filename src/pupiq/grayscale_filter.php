<?php
namespace Pupiq;

class GrayscaleFilter {

	function __construct(){
	}

	function process($imagick,$options){
		//$imagick->modulateImage(100,0,100);
		$imagick->transformImageColorspace(\Imagick::COLORSPACE_GRAY);
	}
}
