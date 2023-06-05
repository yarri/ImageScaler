# Change Log

All notable changes to \\Pupuq\\ImageScaler will be documented in this file.

## [0.7] - 2023-06-06

* Added support for HEIC images

## [0.6] - 2022-09-08

* 2561430 - Added support for AVIF images

## [0.5] - 2021-10-23

- Added method ImageScaler::saveToString()

## [0.4] - 2021-10-10

- If just the width or just the height is given, the other size is automatically calculated accordingly
- Automatic detection of an image orientation from exif data

## [0.3] - 2021-03-03

- Filters moved into namespace Pupiq\ImageScaler (originally they were in namespace Pupiq) - BC BREAK!
- Added support for webp images
- Added method ImageScaler::prepareScalingData()
- GrayscaleFilter fixed

## [0.2.1] - 2021-03-02

- WatermarkFilter fixed

## [0.2] - 2021-03-01

- Added after scale filter WatermarkFilter

## [0.1] - 2020-01-12

First official release.
