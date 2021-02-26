ImageScaler
===========

Handy tool for image resizing. Produces well optimized images for web.

Basic Usage
-----------

    $scaler = new \Pupiq\ImageScaler("/path/to/image.jpg");
    $scaler->getImageWidth(); // e.g. 1920
    $scaler->getImageHeight(); // e.g. 1080
    $scaler->getOrientation(); // 0, 1, 2 or 3 (i.e. 0, 90, 180, 270 degrees clockwise)

    $scaler->scaleTo(300,200,["keep_aspect" => true]);
    $scaler->saveTo("/path/to/output_file.jpg");

    $scaler_out = new \Pupiq\ImageScaler("/path/to/output_file.jpg");
    $scaler_out->getImageWidth(); // 300
    $scaler_out->getImageHeight(); // 169

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
