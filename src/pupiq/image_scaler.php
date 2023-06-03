<?php
namespace Pupiq;

use \Imagick, \ImagickPixel, \Files;

class ImageScaler {

	protected $_Orientation = null; // 0,1,2,3 (i.e. 0, 90, 180, 270 degrees clockwise)

	protected $_MimeType;
	protected $_FileName;
	protected $_ImageWidth;
	protected $_ImageHeight;

	protected $_Options; // scaleTo() options
	protected $_Imagick;

	protected $_AfterScaleFilters = [];

	protected $_AfterSaveFilters = [];

	function __construct($filename){
		if(!file_exists($filename)){
			throw new \Exception("Pupiq\ImageScaler: file does not exist ($filename)");
		}

		if(!($size = $this->_getimagesize($filename))){
			throw new \Exception("Pupiq\ImageScaler: file is not image ($filename)");
		}

		$this->_MimeType = $size[2];
		$this->_FileName = $filename;
		$this->_ImageWidth = $size[0];
		$this->_ImageHeight = $size[1];

		if(!class_exists("Imagick")){
			throw new \Exception("Pupiq\ImageScaler: dependency not met: class Imagick doesn't exist");
		}
		if(!defined("Imagick::ALPHACHANNEL_REMOVE")){
			throw new \Exception("Pupiq\ImageScaler: dependency not met: constant Imagick::ALPHACHANNEL_REMOVE doesn't exist");
		}
	}

	function getMimeType(){
		return $this->_MimeType;
	}

	function getFileName(){
		return $this->_FileName;
	}

	function saveTo($filename){
		if(is_null($this->_Imagick)){
			$this->scaleTo($this->getImageWidth(),$this->getImageHeight());
		}

		$this->_Imagick->writeImage($filename);

		foreach($this->_AfterSaveFilters as $filter){
			$filter->process($filename,$this->_Options);
		}
	}

	function saveToString(){
		$filename = \Files::GetTempFilename();
		$this->saveTo($filename);
		$out = \Files::GetFileContent($filename);
		\Files::Unlink($filename);
		return $out;
	}

	function setOrientation($orientation){
		$orientation = (int)$orientation;
		$orientation = abs($orientation % 4);
		$this->_Orientation = $orientation;
	}

	function getOrientation(){
		if(is_null($this->_Orientation)){
			$this->_Orientation = $this->_determineOrientation();
		}
		return $this->_Orientation;
	}

	protected function _determineOrientation(){
		$orientation = 0;

		if(!preg_match('/^image\/jpe?g/',$this->getMimeType())){ // only jpegs have exif data
			return $orientation;
		}

		if(!function_exists("exif_read_data")){ return $orientation; }

		$exif = @exif_read_data($this->getFileName()); // Sometimes an error occurs (e.g. Illegal IFD size)
		if($exif){
			$_orient = 0;
			isset($exif["IFD0"]["Orientation"]) && ($_orient = $exif["IFD0"]["Orientation"]);
			isset($exif["Orientation"]) && ($_orient = $exif["Orientation"]);
			switch($_orient){
				case 3: // 180 rotate left
					$orientation = 2;
					break;
				case 6: // 90 rotate right
					$orientation = 1;
					break;
				case 8: // 90 rotate left
					$orientation = 3;
					break;		
			}
		}

		return $orientation;
	}

	function getImageWidth($orientation = null){
		if(is_null($orientation)){ $orientation = $this->getOrientation(); }
		if($orientation % 2){
			return $this->_ImageHeight;
		}
		return $this->_ImageWidth;
	}

	function getImageHeight($orientation = null){
		if(is_null($orientation)){ $orientation = $this->getOrientation(); }
		if($orientation % 2){
			return $this->_ImageWidth;
		}
		return $this->_ImageHeight;
	}

	function appendAfterScaleFilter($filter){
		if(!is_a($filter,"\Pupiq\ImageScaler\AfterScaleFilter")){
			throw new \InvalidArgumentException("\Pupiq\ImageScaler::appendAfterScaleFilter(): filter must be a \Pupiq\ImageScaler\AfterScaleFilter");
		}
		$this->_AfterScaleFilters[] = $filter;
	}

	function appendAfterSaveFilter($filter){
		if(!is_a($filter,"\Pupiq\ImageScaler\AfterSaveFilter")){
			throw new \InvalidArgumentException("\Pupiq\ImageScaler::appendAfterSaveFilter(): filter must be a \Pupiq\ImageScaler\AfterSaveFilter");
		}
		$this->_AfterSaveFilters[] = $filter;
	}

	/**
	 * Determines missing height and fulfills all the scaling options.
	 *
	 *	list($width,$height,$options) = $scaler->prepareScalingData(100);
	 *	list($width,$height,$options) = $scaler->prepareScalingData(800,600,["crop" => true]);
	 */
	function prepareScalingData($width = null,$height = null,$options = array()){
		if(is_array($width)){
			$options = $width;
			$width = null;
		}
		if(is_array($height)){
			$options = $height;
			$height = null;
		}
		$options += array(
			"orientation" => $this->getOrientation(), // 0,1,2,3 (i.e. 0, 90, 180, 270 degrees clockwise)
		);
		$orientation = $options["orientation"];

		if(is_null($width) && is_null($height)){
			$width = $this->getImageWidth($orientation);
			$height = $this->getImageHeight($orientation);
		}elseif(is_null($width) || is_null($height)){
			$ratio = $this->getImageWidth($orientation) / $this->getImageHeight($orientation);
			if(is_null($width)){
				$width = round($height * $ratio);
			}else{
				$height = round($width / $ratio);
			}
		}
		
		$output_formats = array(
			"image/jpeg" => "jpeg",
			"image/jpg" => "jpeg",
			"image/png" => "png",
			"image/gif" => "gif",
			"image/webp" => "webp",
			"image/avif" => "avif",
			"image/heic" => "heic",
			"image/heif" => "heic",
		);

		$mime_type = $this->getMimeType();

		$options += array(
			// odkud z originalu budeme rezat
			"x" => 0,
			"y" => 0,
			"width" => $this->getImageWidth($orientation),
			"height" => $this->getImageHeight($orientation),

			// keep_aspect a crop se vylycuji - nema cenu nastavat obe na true
			"keep_aspect" => false,
			"crop" => null, // pokud bude "auto" (nebo "top", "bottom"), prepise tyto hodnoty v $options: x,y,width,height

			"strip_meta_data" => true,
			"sharpen_image" => null, // true, false, null (auto)
			"compression_quality" => 85,
			"auto_convert_cmyk_to_rgb" => true,

			"output_format" => isset($output_formats[$mime_type]) ? $output_formats[$mime_type] : "jpeg", // "jpeg", "png", "webp", "avif"
		);

		// gif -> png
		$options["output_format"] = in_array($options["output_format"],["png","gif"]) ? "png" : $options["output_format"];

		// jpg -> jpeg
		$options["output_format"] = $options["output_format"]=="jpg" ? "jpeg" : $options["output_format"];

		$options += array(
			"background_color" => in_array($options["output_format"],array("png","webp","avif")) ? "transparent" : "#ffffff"
		);


		if($options["keep_aspect"]){
			$current_width = $this->getImageWidth($orientation);
			$current_height = $this->getImageHeight($orientation);

			$ratio = (float)$width / (float)$current_width;
			$new_width = $width;
			$new_height = floor($current_height * $ratio);

			if($new_height>$height){
				$ratio = (float)$height / (float)$current_height;
				$new_height = $height;
				$new_width = floor($current_width * $ratio);
			}

			unset($options["keep_aspect"]);
			return $this->prepareScalingData((int)$new_width,(int)$new_height,$options);
		}

		if($options["crop"]){
			$image_width = $this->getImageWidth();
			$image_height = $this->getImageHeight();

			$wished_ratio = $width / $height;
			$current_ratio = $image_width / $image_height;

			$crop_width = $image_width;
			$crop_height = $image_height;

			$x = $y = 0;

			// pozadovany obrazek je sirsi
			if($wished_ratio>$current_ratio){
				$crop_height = $image_height * ($current_ratio / $wished_ratio);

				$y = floor((($image_height - $crop_height) / 2) - 1);

				if($options["crop"]=="top"){
					$y = 0;
				}

				if($options["crop"]=="bottom"){
					$y = $image_height - $crop_height;
				}
			}

			// obrazek je vyssi
			if($wished_ratio<$current_ratio){
				$crop_width = $image_width * ($wished_ratio / $current_ratio);
				$x = floor((($image_width - $crop_width) / 2) - 1);
			}

			$options["width"] = $crop_width;
			$options["height"] = $crop_height;
			$options["x"] = $x;
			$options["y"] = $y;

			unset($options["crop"]);

			//$options["x"] = 0;
			//$options["y"] = 504;

			/*
			Header("Content-Type: text/plain");
			echo "\n\n\n";
			var_dump("$width x $height");
			var_dump("$image_width x $image_height");
			var_dump($options);
			exit;// */


			return $this->prepareScalingData($width,$height,$options);
		}

		if(!isset($options["sharpen_image"])){
			// automaticke urceni toho, zda se bude ostrit
			// pokud se zmensuje o vice nez 20% v obou rozmerech -> ostri se
			$threshold_percent = 20;
			$options["sharpen_image"] = 
				$width<=(($options["width"] / 100.0) * (100.0 - $threshold_percent)) &&
				$height<=(($options["height"] / 100.0) * (100.0 - $threshold_percent));
		}

		return array($width,$height,$options);
	}

	function scaleTo($width = null,$height = null,$options = array()){
		list($width,$height,$options) = $this->prepareScalingData($width,$height,$options);

		$orientation = $this->getOrientation();

		$this->_Imagick = null;
		$this->_Options = $options;

		$filename = $this->getFileName();
		
		$image_ar = $this->_getimagesize($filename);

		if(!$image_ar){
			throw new \Exception("Pupiq\ImageScaler: file is not image ($filename)");
		}

		$src_width = $image_ar[0];
		$src_height = $image_ar[1];

		// sudo apt-get install php-imagick
		//myAssert(class_exists("Imagick"));

		$background_pixel = new ImagickPixel($options["background_color"]);

		$background = new Imagick();
		$background->setBackgroundColor($background_pixel);
		$background->newImage($width,$height,$background_pixel);

		$imagick = new Imagick();
		$imagick->readImage($filename);

		$imagick->setImageBackgroundColor($background_pixel);

		// https://www.php.net/manual/en/imagick.flattenimages.php
		//$imagick = $imagick->flattenImages();
		$imagick->setImageAlphaChannel(constant("Imagick::ALPHACHANNEL_REMOVE"));
		$imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);

		if($orientation>0){
			$imagick->rotateImage($background_pixel,$orientation * 90);
			//$background->rotateImage($background_pixel,$orientation * 90);
		}

		if($options["auto_convert_cmyk_to_rgb"] && $imagick->getImageColorspace()==Imagick::COLORSPACE_CMYK){
			// Info o ICC profilech:
			// http://www.pdynet.net/clanky/imagick-prevod-palety-cmyk-na-rgb-v-php.html

			if(method_exists($imagick,'getImageProfiles')){ // starsi verze image magicku toto nemaji
				$profiles = $imagick->getImageProfiles('*', false); // Nacteni profilu v obrazku.
				if(!in_array("icc",$profiles)){
					# CMYK obrazek nema pripojeny CMYK profil. Pouzijeme nejaky standardni
					$icc_cmyk = \Files::GetFileContent(__DIR__.'/icc_profiles/USWebCoatedSWOP.icc');
					$imagick->profileImage('icc', $icc_cmyk);
				}
			}

			// Zmena barevne palety, CMYK -> SRGB.
			// Je treba pripojit jeste RGB profil
      $icc_rgb = \Files::GetFileContent(__DIR__.'/icc_profiles/sRGB_v4_ICC_preference.icc');
      $imagick->profileImage('icc', $icc_rgb);
			$imagick->setImageColorspace(Imagick::COLORSPACE_SRGB);
		}

		// Some HEIC images appeared in the YCbCr colorspace
		if($imagick->getImageColorspace()==Imagick::COLORSPACE_YCBCR){
			$imagick->transformImageColorspace(Imagick::COLORSPACE_SRGB);
		}

		$stat = $imagick->setImageFormat($options["output_format"]); // "jpeg", "png", "webp", "avif", "heic"
		if(!$stat){
			throw new Exception("ImageScaler: Unable to set image format to $options[output_format]");
		}
		$background->setImageFormat($options["output_format"]); // "jpeg", "png", "webp", "avif", "heic"

		// neni treba delat, pokud se kopiruje cely obrazek...
		if($options["x"]!=0 || $options["y"]!=0 || $options["width"]!=$this->getImageWidth($orientation) || $options["height"]!=$this->getImageHeight($orientation)){
			$imagick->cropImage($options["width"],$options["height"],$options["x"],$options["y"]);
		}

		$bestfit = false;
		if(abs(($width / $height) - ($imagick->getImageWidth() / $imagick->getImageHeight())) > 0.05){
			// Pokud je pomer stran prilis rozdilny, zapneme $bestfit
			$bestfit = true;
		}
		$imagick->scaleImage($width,$height,$bestfit);

		$_x = $_y = 0;
		if($imagick->getImageHeight()<$background->getImageHeight()){
			$_y = floor(($background->getImageHeight()-$imagick->getImageHeight()) / 2);
		}elseif($imagick->getImageWidth()<$background->getImageWidth()){
			$_x = floor(($background->getImageWidth()-$imagick->getImageWidth()) / 2);
		}
		$background->compositeImage($imagick->getImage(), Imagick::COMPOSITE_COPY, $_x, $_y);

		$options["strip_meta_data"] && $background->stripImage();

		$options["sharpen_image"] && $background->sharpenImage(1,1);

		if($options["output_format"]=="jpeg"){
			$background->setImageCompression(Imagick::COMPRESSION_JPEG);
			$background->setImageCompressionQuality($options["compression_quality"]);
			$background->setInterlaceScheme(Imagick::INTERLACE_JPEG); // progressive jpeg
			// TODO: Pry neni vhodne pouzivat progressive scan pro obrazky mensi nez 10kb
		}

		if($options["output_format"]=="webp"){
			$background->setImageCompressionQuality($options["compression_quality"]);
		}

		if($options["output_format"]=="avif"){
			$compression_quality = $options["compression_quality"] - 60; // 85 -> 25
			$compression_quality = max($compression_quality,20);
			$background->setCompressionQuality($compression_quality); // not setImageCompressionQuality :)
		}

		if($options["output_format"]=="heic"){
			$compression_quality = $options["compression_quality"] - 60; // 85 -> 25
			$compression_quality = max($compression_quality,20);
			$background->setCompressionQuality($compression_quality); // not setImageCompressionQuality :)
		}

		$this->_Imagick = $background;

		foreach($this->_AfterScaleFilters as $filter){
			$filter->process($this->_Imagick,$this->_Options);
		}

		return $this->_Imagick;
	}

	protected function _getimagesize($filename){
		$width = $height = $mime_type = null;

		if($size = getimagesize($filename)){
			$width = $size[0];
			$height = $size[1];
			$mime_type = \Files::DetermineFileType($filename);
		}else{
			$imagick = new Imagick();
			if($imagick->readImage($filename)){
				$width = $imagick->getImageWidth();
				$height = $imagick->getImageHeight();
				$mime_type = $imagick->getImageMimeType();
			}
			unset($imagick);
		}

		if(is_null($width)){
			return null;
		}

		if($mime_type == "image/x-webp"){
			$mime_type = "image/webp";
		}

		if($mime_type == "image/x-heic"){
			$mime_type = "image/heic";
		}

		if($mime_type == "image/x-heif"){
			$mime_type = "image/heif";
		}

		return array($width,$height,$mime_type);
	}
}
