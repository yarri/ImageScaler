<?php
namespace Pupiq;

class GrayscaleFilter extends AfterScaleFilter {

	function __construct(){
	}

	function process($imagick,$options){
		// setting the initial colorspace
		$imagick->setImageColorspace(\Imagick::COLORSPACE_SRGB);

		//$imagick->modulateImage(100,0,100);
		$imagick->transformImageColorspace(\Imagick::COLORSPACE_GRAY);
	}
}
