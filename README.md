ImageScaler
===========

Handy tool for image resizing. Produces well optimized images for web.

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

    // checking thre result
    $scaler_out = new Pupiq\ImageScaler("/path/to/output_file.jpg");
    $scaler_out->getImageWidth(); // 300
    $scaler_out->getImageHeight(); // 169

Scaling options
---------------

There are plenty of options in method scaleTo(). Some of them are against each other. So they should not be used together.

    $scaler->scaleTo($width,$height,[
      "orientation" => 0, // automatically detected 

      // this is the source area on the original image
      "x" => 0,
      "y" => 0,
      "width" => $image_width,
      "height" => $image_height,

      "keep_aspect" => false,

      "crop" => null, // null, "auto", "top", "bottom"

      "strip_meta_data" => true,
      "sharpen_image" => null, // true, false, null (auto)
      "compression_quality" => 85,
      "auto_convert_cmyk_to_rgb" => true,

      "output_format" => "jpeg", // "jpeg", "png"

      "background_color" => "#ffffff", // by default it is "transparent" for png images
    ]);

Typical usages
--------------

    // scale image to 200px width
    $scaler->scaleTo(200);

    // scale image to size 200x200
    // original image will be inscribed into 200x200 box
    // some parts of the output image may be padded with the background_color
    $scaler->scaleTo(200,200);

    // transform original image into max 200px width and max 200px height
    // so the final width or the final height may be lower than 200px
    $scaler->scaleTo(200,200,["keep_aspect" => true]);

    // transform and crop the original image into 200x200 box
    $scaler->scaleTo(200,200,["crop" => true]);

    // transform and crop the original image into 200x200 box
    // and preserve the top part of the image
    // this is a great option e.g. for magazine covers
    $scaler->scaleTo(200,200,["crop" => "top"]);

Filters
-------

Image processing can be affected using filters. There are two types of filters.

* **After scale filters**<br>
  These filters are executed right after the scaling. The Imagick object is passed to them.<br>
  After scaling filter must be an instance of *Pupiq\ImageScaler\AfterScaleFilter*
* **After save filters**<br>
  These filters are executed right after the saving image to to the output file. The filename is passed to them.<br>
  After saving filters must be an instance of *Pupiq\ImageScaler\AfterSaveFilter*

In both types of filter, details about the desired transformation are also passed to them.

This package comes with several filters that can be used instantly.

#### Grayscale filter

Grayscale filter converts the currently processed image to grayscale. It is an after scale filter.

    $scaler = new Pupiq\ImageScaler("/path/to/image.jpg");
    $scaler->appendAfterScaleFilter(new Pupiq\ImageScaler\GrayscaleFilter());

    $scaler->scaleTo(300,300);
    $scaler->saveTo("/path/to/output_image.jpg"); // grayscale

#### Pngquant Optimizer filter

For png images there is Pngquant Optimizer filter. It can significantly reduce size of the final PNG image. It is required that the binary pngquant is installed in the system.

Pngquant Optimizer filter is an after save filter.

    $scaler = new Pupiq\ImageScaler("/path/to/image.png");
    $scaler->appendAfterSaveFilter(new Pupiq\ImageScaler\PngquantOptimizer([
      "pngquant_binary" => "/usr/bin/pngquant",
      "quality_range" => "70-90"
    ]));

    $scaler->scaleTo(300,300);
    $scaler->saveTo("/path/to/output_image.png");

For jpeg images, this filter simply does nothing.

#### Watermark filter

This filter places the given watermark into the currently processed image.

    $scaler = new Pupiq\ImageScaler("/path/to/image.jpg");
    $scaler->appendAfterScaleFilter(new Pupiq\ImageScaler\WatermarkFilter("/path/to/watermak_image.png",[
      "opacity" => 50, // 50%
      "position" => "center", // "center", "left-top" "left-bottom", "right-top", "right-bottom"
    ]);

    $scaler->scaleTo(300,300);
    $scaler->saveTo("/path/to/output_image.jpeg"); // watermaked image

Of course, the filters can be combined. They are processed in the given order.

    $scaler = new Pupiq\ImageScaler("/path/to/image.jpg");

    $scaler->appendAfterScaleFilter(new Pupiq\ImageScaler\WatermarkFilter("/path/to/watermak_image.png"));
    $scaler->appendAfterScaleFilter(new Pupiq\ImageScaler\GrayscaleFilter());

    $scaler->scaleTo(300,300);
    $scaler->saveTo("/path/to/output_image.jpeg"); // watermaked and grayscaled image

Installation
------------

Just use the Composer:

    composer require pupiq/image-scaler

Testing
-------

Install required dependencies for development:

    composer update --dev

Run tests:

    cd test
    ../vendor/bin/run_unit_tests

License
-------

ImageScaler is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)

[//]: # ( vim: set ts=2 et: )
