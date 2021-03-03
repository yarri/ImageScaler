<?php
namespace Pupiq\ImageScaler;

class WatermarkFilter extends AfterScaleFilter {

	protected $watermark_image_filename;
	protected $opacity;
	protected $position;

	function __construct($watermark_image_filename,$options = array()){
		$options += array(
			"opacity" => 50,
			"position" => "center", // "center", "left-top" "left-bottom", "right-top", "right-bottom"
		);

		$this->watermark_image_filename = $watermark_image_filename;
		$this->opacity = $options["opacity"];
		$this->position = $options["position"];
	}

	function getWatermarkImageFilename(){
		return $this->watermark_image_filename;
	}

	function getOpacity(){
		return $this->opacity;
	}

	function getPosition(){
		return $this->position;
	}

	function process($imagick,$options){
		$width = $imagick->getImageWidth();
		$height = $imagick->getImageHeight();

		$wi_filename = $this->getWatermarkImageFilename();
		$wi = new \Pupiq\ImageScaler($wi_filename);
		$wi->setOrientation(0);
		$wi_width = $wi->getImageWidth();
		$wi_height = $wi->getImageHeight();

		$watermark = new \Imagick();
		$watermark->readImage($wi_filename);
		$watermark->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE);
		$watermark->evaluateImage(\Imagick::EVALUATE_MULTIPLY, $this->getOpacity()/100.0, \Imagick::CHANNEL_ALPHA);

		switch($this->getPosition()){
			case "right-top":
				$x = $width - $wi_width;
				$y = 0;
				break;
			case "left-bottom":
				$x = 0;
				$y = $height - $wi_height;
				break;
			case "right-bottom":
				$x = $width - $wi_width;
				$y = $height - $wi_height;
				break;
			case "center":
				$x = ($width / 2.0) - ($wi_width / 2.0);
				$y = ($height / 2.0) - ($wi_height / 2.0);
				break;	
			case "left-top":
			default:
				$x = 0;
				$y = 0;
		}
		$x = round($x);
		$y = round($y);

		$imagick->compositeImage($watermark, \Imagick::COMPOSITE_OVER, $x, $y);
	}
}	
