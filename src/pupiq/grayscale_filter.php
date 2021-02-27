<?php
namespace Pupiq;

class GrayscaleFilter extends AfterScaleFilter {

	function __construct(){
	}

	function process($imagick,$options){
		//$imagick->modulateImage(100,0,100);
		$imagick->transformImageColorspace(\Imagick::COLORSPACE_GRAY);
	}
}
