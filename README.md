ImageScaler
===========

[![Build Status](https://app.travis-ci.com/yarri/ImageScaler.svg?branch=master)](https://app.travis-ci.com/yarri/ImageScaler)

Handy tool for image resizing. Produces well optimized images for web.

Supported input and output formats: **JPEG, PNG, GIF** (including animated), **WebP, AVIF, HEIC**.

Requirements
------------

* PHP >= 7.4
* ext-imagick >= 3.4
* pngquant (optional, required only for `PngquantOptimizer` filter)

Basic Usage
-----------

    $scaler = new Pupiq\ImageScaler("/path/to/image.jpg");

    // getting info about image
    $scaler->getImageWidth(); // e.g. 1920
    $scaler->getImageHeight(); // e.g. 1080
    $scaler->getOrientation(); // 0, 1, 2 or 3 (i.e. 0, 90, 180, 270 degrees clockwise)
    $scaler->getMimeType(); // "image/jpeg"

    // performing a transformation
    $scaler->scaleTo(300,200,["keep_aspect" => true]);

    // saving to output file
    $scaler->saveTo("/path/to/output_file.jpg");
    // or saving to string
    // $image = $scaler->saveToString();

    // checking the result
    $scaler_out = new Pupiq\ImageScaler("/path/to/output_file.jpg");
    $scaler_out->getImageWidth(); // 300
    $scaler_out->getImageHeight(); // 169

EXIF orientation is detected automatically for JPEG images. The image is rotated accordingly so that the output is always correctly oriented.

Scaling options
---------------

There are plenty of options in method scaleTo(). Some of them are mutually exclusive and should not be used together.

    $scaler->scaleTo($width, $height, [
      "orientation" => 0, // automatically detected from EXIF; 0, 1, 2 or 3 (i.e. 0, 90, 180, 270 degrees clockwise)

      // source crop area within the original image
      "x" => 0,
      "y" => 0,
      "width" => $image_width,
      "height" => $image_height,

      // keep_aspect and crop are mutually exclusive
      "keep_aspect" => false,
      "crop" => null, // null, true/"auto", "top", "bottom"

      "strip_meta_data" => true,
      "sharpen_image" => null, // true, false, null (auto: sharpens when downscaling by more than 20%)
      "compression_quality" => 85,
      "auto_convert_cmyk_to_rgb" => true,

      "output_format" => "jpeg", // "jpeg", "png", "gif", "webp", "avif", "heic"

      "background_color" => "#ffffff", // defaults to "transparent" for png, gif, webp and avif
    ]);

Typical usages
--------------

    // Scale image to 200px width, preserve aspect ratio.
    $scaler->scaleTo(200);

    // Scale image to 200px height, preserve aspect ratio.
    $scaler->scaleTo(null, 200);

    // Scale image to fit into a 200x200 box.
    // Aspect ratio is preserved; any remaining space is filled with background_color.
    $scaler->scaleTo(200, 200, ["background_color" => "#ffffff"]);

    // Scale image to fit within 200x200.
    // The output may be smaller than 200x200 in one dimension.
    $scaler->scaleTo(200, 200, ["keep_aspect" => true]);

    // Scale and crop the image to exactly 200x200.
    // The crop is centred.
    $scaler->scaleTo(200, 200, ["crop" => true]);

    // Scale and crop to 200x200, preserving the top part of the image.
    // Great for portrait photos, magazine covers, etc.
    $scaler->scaleTo(200, 200, ["crop" => "top"]);

    // Scale and crop to 200x200, preserving the bottom part of the image.
    $scaler->scaleTo(200, 200, ["crop" => "bottom"]);

Format conversion
-----------------

The output format can be changed independently of the input format. To convert a JPEG to WebP:

    $scaler = new Pupiq\ImageScaler("/path/to/image.jpg");
    $scaler->scaleTo(800, null, ["output_format" => "webp"]);
    $scaler->saveTo("/path/to/output_image.webp");

Any combination of supported input and output formats is accepted.

Filters
-------

Image processing can be extended with filters. There are two types of filters.

* **After scale filters**<br>
  Executed right after scaling. The `Imagick` object is passed to them.<br>
  Must be an instance of `Pupiq\ImageScaler\AfterScaleFilter`.
* **After save filters**<br>
  Executed right after the image is saved to the output file. The filename is passed to them.<br>
  Must be an instance of `Pupiq\ImageScaler\AfterSaveFilter`.

In both types the scaling options array is also passed to the filter.

This package comes with several built-in filters.

#### Grayscale filter

Converts the processed image to grayscale. It is an after scale filter.

    $scaler = new Pupiq\ImageScaler("/path/to/image.jpg");
    $scaler->appendAfterScaleFilter(new Pupiq\ImageScaler\GrayscaleFilter());

    $scaler->scaleTo(300, 300);
    $scaler->saveTo("/path/to/output_image.jpg"); // grayscale

#### Pngquant Optimizer filter

Significantly reduces the file size of PNG images using the [pngquant](https://pngquant.org/) binary. It is an after save filter and has no effect on non-PNG output formats.

    $scaler = new Pupiq\ImageScaler("/path/to/image.png");
    $scaler->appendAfterSaveFilter(new Pupiq\ImageScaler\PngquantOptimizer([
      "pngquant_binary" => "/usr/bin/pngquant", // default: "pngquant"
      "quality_range" => "70-90"                // default: "70-90"
    ]));

    $scaler->scaleTo(300, 300);
    $scaler->saveTo("/path/to/output_image.png");

#### Watermark filter

Places a watermark image over the processed image. It is an after scale filter.

    $scaler = new Pupiq\ImageScaler("/path/to/image.jpg");
    $scaler->appendAfterScaleFilter(new Pupiq\ImageScaler\WatermarkFilter("/path/to/watermark_image.png", [
      "opacity" => 50,          // percentage, default: 50
      "position" => "center",   // "center", "left-top", "left-bottom", "right-top", "right-bottom"; default: "center"
    ]));

    $scaler->scaleTo(300, 300);
    $scaler->saveTo("/path/to/output_image.jpg"); // watermarked image

#### Combining filters

Filters can be combined and are applied in the order they were added.

    $scaler = new Pupiq\ImageScaler("/path/to/image.jpg");

    $scaler->appendAfterScaleFilter(new Pupiq\ImageScaler\WatermarkFilter("/path/to/watermark_image.png"));
    $scaler->appendAfterScaleFilter(new Pupiq\ImageScaler\GrayscaleFilter());

    $scaler->scaleTo(300, 300);
    $scaler->saveTo("/path/to/output_image.jpg"); // watermarked and grayscaled

Installation
------------

Use the Composer:

    composer require pupiq/image-scaler

Testing
-------

Install required dependencies:

    composer update

Run tests:

    cd test
    ../vendor/bin/run_unit_tests

License
-------

ImageScaler is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)

[//]: # ( vim: set ts=2 et: )
