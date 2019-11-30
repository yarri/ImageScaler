<?php
namespace Pupiq;

use \Imagick, \ImagickPixel, \Files;

defined("PNGQUANT_OPTIMALIZATION_ENABLED") || define("PNGQUANT_OPTIMALIZATION_ENABLED",false);


class ImageScaler {

	protected $_OriginalImageWidth;
	protected $_OriginalImageHeight;
	protected $_OriginalMimeType;

	protected $_MimeType;
	protected $_FileName;
	protected $_Content;
	protected $_FileSize;
	protected $_ImageWidth;
	protected $_ImageHeight;

	protected $_Scaled = false;
	
	function __construct($filename){
		$size = getimagesize($filename);
		$this->_OriginalImageWidth = $this->_ImageWidth = $size[0];
		$this->_OriginalImageHeight = $this->_ImageHeight = $size[1];
		$this->_MimeType = $this->_OriginalImageHeight = \Files::DetermineFileType($filename);
		$this->_Content = \Files::GetFileContent($filename);
	}

	function getFileName(){
		return $this->_FileName;
	}

	function getImageWidth($orientation = 0){
		if($orientation % 2){
			return $this->_ImageHeight;
		}
		return $this->_ImageWidth;
	}

	function getImageHeight($orientation = 0){
		if($orientation % 2){
			return $this->_ImageWidth;
		}
		return $this->_ImageHeight;
	}

	function getContent(){ return $this->_Content; }

	function scaleTo($width,$height = null,$options = array()){
		if(is_array($height)){
			$options = $height;
			$height = null;
		}
		if(!isset($height)){ $height = $width; }
		$options += array(
			"orientation" => 0, // 0,1,2,3 (i.e. 0, 90, 180, 270 degrees clockwise)
		);
		$orientation = $options["orientation"];
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

			"output_format" => "jpeg", // "jpeg", "png"

			"watermark_definition" => null, // WatermarkDefinition
		);

		// sanitize
		$options["output_format"] = in_array($options["output_format"],["png","gif"]) ? "png" : "jpeg";

		$options += array(
			"background_color" => $options["output_format"]=="png" ? "transparent" : "#ffffff"
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
			return $this->scaleTo((int)$new_width,(int)$new_height,$options);
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

			return $this->scaleTo($width,$height,$options);
		}

		if(!isset($options["sharpen_image"])){
			// automaticke urceni toho, zda se bude ostrit
			// pokud se zmensuje o vice nez 20% v obou rozmerech -> ostri se
			$threshold_percent = 20;
			$options["sharpen_image"] = 
				$width<=(($options["width"] / 100.0) * (100.0 - $threshold_percent)) &&
				$height<=(($options["height"] / 100.0) * (100.0 - $threshold_percent));
		}

		$filename = \Files::GetTempDir()."/ScalingImage_".posix_getpid()."_".rand(0,999999);
		$dest_filename = $filename."_dst";

		\Files::WriteToFile($filename,$this->getContent(),$error,$error_message);
		if($error){
			unlink($filename);
			return false;
		}

		if(!($image_ar = getimagesize($filename))){
			unlink($filename);
			return false;
		}

		$src_width = $image_ar[0];
		$src_height = $image_ar[1];

		// sudo apt-get install php-imagick
		//myAssert(class_exists("Imagick"));

		//$_stat = imagejpeg($dest_image,$dest_filename, 100);
		$background_pixel = new ImagickPixel($options["background_color"]);

		$background = new Imagick();
		$background->setBackgroundColor($background_pixel);
		$background->newImage($width,$height,$background_pixel);

		$imagick = new Imagick();
		$imagick->readImage($filename);

		$imagick->setImageBackgroundColor($background_pixel);

		// https://www.php.net/manual/en/imagick.flattenimages.php
		//$imagick = $imagick->flattenImages();
		if(defined("Imagick::ALPHACHANNEL_REMOVE")){
			$imagick->setImageAlphaChannel(constant("Imagick::ALPHACHANNEL_REMOVE"));
		}else{
			$imagick->setImageAlphaChannel(11);
		}
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

		$imagick->setImageFormat($options["output_format"]); // "jpeg", "png"
		$background->setImageFormat($options["output_format"]); // "jpeg", "png"

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

		if($wd = $options["watermark_definition"]){
			$this->_placeWatermark($wd,$background,$width,$height);
		}

		if($options["output_format"]=="jpeg"){
			$background->setImageCompression(Imagick::COMPRESSION_JPEG);
			$background->setImageCompressionQuality($options["compression_quality"]);
			$background->setInterlaceScheme(Imagick::INTERLACE_JPEG); // progressive jpeg
			// TODO: Pry neni vhodne pouzivat progressive scan pro obrazky mensi nez 10kb
		}

		$background->writeImage($dest_filename);

		unlink($filename);

		// Optimalizace velikosti obrazku PNG pomoci https://pngquant.org/
		if($options["output_format"]=="png" && PNGQUANT_OPTIMALIZATION_ENABLED){
			// prepinac --skip-if-larger nefungoval dobre na img.dumlatek.cz (nic se neulozilo a vratila se chyba 98)
			$cmd = PNGQUANT_BINARY." --quality ".PNGQUANT_QUALITY_RANGE." --force $dest_filename --output $dest_filename.optimized";
			exec($cmd,$output,$ret_val);
			if($ret_val){
				trigger_error("PNG optimization command execution ($cmd) ended with error $ret_val");
			}
			if(file_exists("$dest_filename.optimized") && filesize("$dest_filename.optimized")){
				\Files::MoveFile("$dest_filename.optimized","$dest_filename");
			}
		}

		$dest_content = \Files::GetFileContent($dest_filename,$error,$error_message);
		if($error){
			unlink($dest_filename);
			return false;
		}

		unlink($dest_filename);

		if(strlen($dest_content)==0){
			return false;
		}

		$this->_Scaled = true;
		$this->_OriginalImageWidth = $this->_ImageWidth;
		$this->_OriginalImageHeight = $this->_ImageHeight;
		$this->_OriginalMimeType = $this->_MimeType;
		$this->_ImageWidth = $width;
		$this->_ImageHeight = $height;
		$this->_Content = $dest_content;
		if($options["output_format"]=="png"){
			$this->_FileName = preg_match("/\\.png$/i",$this->getFileName()) ? $this->getFileName() : $this->getFileName().".png";
			$this->_MimeType = "image/png";
		}else{
			$this->_FileName = preg_match("/\\.jpe?g$/i",$this->getFileName()) ? $this->getFileName() : $this->getFileName().".jpg";
			$this->_MimeType = "image/jpeg";
		}

		return true;
	}
}
