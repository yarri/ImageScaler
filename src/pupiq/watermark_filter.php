<?php
namespace Pupiq;

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
		$width = $options["width"];
		$height = $options["height"];

		/*
		$wi = $wd->getWatermarkImage();
		switch($wd->getSize()){
			case "contain":
				$geom = "{$width}x{$height}";
				break;
			case "auto":
			default:
				$geom = PUPIQ_MAX_SERVED_IMAGE_WIDTH."x".PUPIQ_MAX_SERVED_IMAGE_HEIGHT;
		}
		$wi_url = $wi->getUrl($geom,$wi_width,$wi_height);
		$uf = new UrlFetcher($wi_url);
		$wi_content = $uf->getContent();
		if(!$wi_content){
			throw new \Exception("Unable to download watermark image ($wi_url): ".$uf->getErrorMessage());
		}
		$wi_filename = Files::WriteToTemp($wi_content);
		*/

		$wi_filename = $this->getWatermarkImageFilename();
		$wi = new ImageScaler($wi_filename);
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
