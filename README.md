# W-PHP Uploader

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

**Version:** 1.1.1 (Uploader) / 1.0.2 (Image Uploader)

**Last Updated:** 2016-02-03

**Compatibility:** PHP 7+

**Created By:** Ali Candan ([@webkolog](https://github.com/webkolog))

**Website:** [http://webkolog.net](http://webkolog.net)

**Copyright:** (c) 2015 Ali Candan

**License:** MIT License ([http://mit-license.org](http://mit-license.org))

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Uploader Class Usage](#uploader-class-usage)
  - [Basic Usage](#basic-usage-uploader)
  - [Setting File Properties Individually](#setting-file-properties-individually-uploader)
  - [Handling Multiple Errors](#handling-multiple-errors-uploader)
  - [Setting Custom File Name](#setting-custom-file-name-uploader)
  - [Restricting File Types and Extensions](#restricting-file-types-and-extensions-uploader)
  - [Setting File Size Limits](#setting-file-size-limits-uploader)
  - [Specifying Upload Directory](#specifying-upload-directory-uploader)
  - [Using a Different Language](#using-a-different-language-uploader)
- [Image Uploader Class Usage](#image-uploader-class-usage)
  - [Basic Usage](#basic-usage-image-uploader)
  - [Setting Image Dimensions](#setting-image-dimensions-image-uploader)
  - [Combining All Options](#combining-all-options-image-uploader)
  - [Checking Image Dimensions After Upload Attempt](#checking-image-dimensions-after-upload-attempt-image-uploader)
  - [Using a Different Language](#using-a-different-language-image-uploader)
- [Language Support](#language-support)
- [Error Handling](#error-handling)
- [Properties (Public)](#properties-public)
  - [Uploader](#uploader-properties)
  - [Image Uploader](#image-uploader-properties)
- [Methods (Public)](#methods-public)
  - [Uploader](#uploader-methods)
  - [Image Uploader](#image-uploader-methods)
- [License](#license)
- [Contributing](#contributing)
- [Support](#support)

## Introduction

W-PHP Uploader is a simple and easy-to-use PHP class designed for handling file uploads. It provides basic functionality for uploading various file types with size and extension restrictions, and supports internationalization for error messages.

W-PHP Image Uploader extends the W-PHP Uploader class and adds specific features for handling image uploads, including checks for valid image MIME types and extensions, as well as image dimensions (width and height).

## Installation

1.  Download the `uploader.php` and `image-uploader.php` files.
2.  Create a directory named `lang-uploader` and another named `lang-image-uploader` in the same directory as the class files.
3.  Download the English language files (`en.php`) and place them in their respective language directories:
    - `lang-uploader/en.php`
    - `lang-image-uploader/en.php`
    (You can add more language files later for internationalization).

## Uploader Class Usage

```php
include("uploader.php");
```

<a name="basic-usage-uploader"></a>

## Basic Usage
This example demonstrates the most basic way to use the Uploader class.

```php
$upl = new Uploader(['file' => $_FILES["file"], 'language' => 'en']);
$upl->dir = "uploaded-files";

if ($upl->upload()) {
    echo "The file was uploaded successfully!";
} else {
    echo "Upload failed:<br>";
    foreach($upl->errorMessages as $errorMessage) {
        echo $errorMessage . "<br>";
    }
}
```

## Setting File Properties Individually
You can set the uploader properties after instantiation.
```php
$upl = new Uploader(['language' => 'en']);
$upl->file = $_FILES["document"];
$upl->newFileName = "report_".date("YmdHis");
$upl->dir = "documents";

if ($upl->upload()) {
    echo "Document uploaded successfully as " . $upl->fileNameWithExtension;
} else {
    echo "Document upload failed:<br>";
    foreach($upl->errorMessages as $errorMessage) {
        echo $errorMessage . "<br>";
    }
}
```

## Handling Multiple Errors
The $errorMessages property is an array, allowing you to display all encountered errors.

```php
$upl = new Uploader(['file' => $_FILES["archive"], 'language' => 'en']);
$upl->allowFileExtensions = ["zip", "rar"];
$upl->maxSize = 100000;
$upl->dir = "archives";
$upl->upload();

if ($upl->checkError()) {
    echo "Upload encountered the following errors:<br>";
    foreach($upl->errorMessages as $error) {
        echo "- " . $error . "<br>";
    }
} else {
    echo "Archive uploaded successfully!";
}
```

## Setting Custom File Name
Use the $newFileName property to specify a new name for the uploaded file (without the extension).
```php
$upl = new Uploader(['file' => $_FILES["image"], 'language' => 'en']);
$upl->newFileName = "profile_picture";
$upl->dir = "images";
$upl->upload();

if (!$upl->checkError()) {
    echo "Image uploaded as " . $upl->fileNameWithExtension;
}
```

## Restricting File Types and Extensions
Control which file types and extensions are allowed.
```php
$upl = new Uploader(['file' => $_FILES["audio"], 'language' => 'en']);
$upl->allowFileTypes = ["audio/mpeg", "audio/ogg"];
$upl->allowFileExtensions = ["mp3", "ogg"];
$upl->dir = "audio";
$upl->upload();

if ($upl->checkError()) {
    // Handle errors related to file type or extension
}
```

## Setting File Size Limits
Define the maximum and minimum allowed file sizes in bytes.
```php
$upl = new Uploader(['file' => $_FILES["large_file"], 'language' => 'en']);
$upl->maxSize = 2000000; // 2 MB
$upl->minSize = 1000;    // 1 KB
$upl->dir = "large_files";
$upl->upload();

if ($upl->checkError()) {
    // Handle errors related to file size
}
```

## Specifying Upload Directory
The $dir property is crucial and must be set to the directory where you want to save the uploaded file.
```php
$upl = new Uploader(['file' => $_FILES["misc"], 'language' => 'en']);
$upl->dir = "/path/to/your/upload/directory"; // Ensure this path is writable
$upl->upload();
```

## Using a Different Language
To use a language other than English, ensure the corresponding language file exists in the lang-uploader/ directory and specify the language code in the constructor.
```php
// Assuming lang-uploader/fr.php exists
$upl = new Uploader(['file' => $_FILES["document"], 'language' => 'fr']);
$upl->dir = "documents";
$upl->upload();

if ($upl->checkError()) {
    foreach($upl->errorMessages as $errorMessage) {
        echo $errorMessage . "<br>"; // Errors will be in French
    }
}
```

## Image Uploader Class Usage
```php
include("image-uploader.php");
```

## Basic Usage
This example shows the basic usage of the ImageUploader class for uploading images.
```php
$iu = new ImageUploader(['file' => $_FILES["image"], 'language' => 'en']);
$iu->dir = "uploaded-images";

if ($iu->upload()) {
    echo "The image was uploaded successfully!";
} else {
    echo "Image upload failed:<br>";
    foreach($iu->errorMessages as $errorMessage) {
        echo $errorMessage . "<br>";
    }
}
```

## Setting Image Dimensions
You can specify the maximum and minimum allowed width and height for uploaded images.
```php
$iu = new ImageUploader(['file' => $_FILES["photo"], 'language' => 'en']);
$iu->dir = "photos";
$iu->maxWidth = 1200;
$iu->maxHeight = 800;
$iu->minWidth = 300;
$iu->minHeight = 200;

if ($iu->upload()) {
    echo "Photo uploaded successfully!";
} else {
    echo "Photo upload failed due to dimension restrictions:<br>";
    foreach($iu->errorMessages as $errorMessage) {
        echo $errorMessage . "<br>";
    }
}
```

#Combining All Options
This example demonstrates setting various properties for image uploading.
```php
$iu = new ImageUploader(['file' => $_FILES["artwork"], 'language' => 'en']);
$iu->newFileName = "final_artwork";
$iu->dir = "art";
$iu->maxSize = 3000000; // 3 MB
$iu->maxWidth = 1920;
$iu->maxHeight = 1080;
$iu->allowFileTypes = ["image/jpeg", "image/png"];
$iu->allowFileExtensions = ["jpg", "jpeg", "png"];

if ($iu->upload()) {
    echo "Artwork uploaded successfully as " . $iu->fileNameWithExtension;
} else {
    echo "Artwork upload failed:<br>";
    foreach($iu->errorMessages as $errorMessage) {
        echo $errorMessage . "<br>";
    }
}
```

## Checking Image Dimensions After Upload Attempt
After a successful (or failed) upload attempt, the $fileWidth and $fileHeight properties will hold the dimensions of the uploaded image (if it was a valid image type).
```php
$iu = new ImageUploader(['file' => $_FILES["banner"], 'language' => 'en']);
$iu->dir = "banners";
$iu->upload();

if (!$iu->checkError() && $iu->fileWidth && $iu->fileHeight) {
    echo "Banner uploaded. Width: " . $iu->fileWidth . "px, Height: " . $iu->fileHeight . "px";
} else {
    echo "Banner upload failed:<br>";
    foreach($iu->errorMessages as $errorMessage) {
        echo $errorMessage . "<br>";
    }
}
```

## Using a Different Language
Similar to the Uploader class, you can specify a language for the ImageUploader. Ensure the language file exists in the lang-image-uploader/ directory.
```php
// Assuming lang-image-uploader/es.php exists
$iu = new ImageUploader(['file' => $_FILES["portrait"], 'language' => 'es']);
$iu->dir = "portraits";
$iu->maxWidth = 600;
$iu->maxHeight = 800;
$iu->upload();

if ($iu->checkError()) {
    foreach($iu->errorMessages as $errorMessage) {
        echo $errorMessage . "<br>"; // Errors will be in Spanish
    }
}
```

## Language Support
The uploaders support multiple languages for error messages. To use a specific language, ensure the corresponding language file (lang-uploader/xx.php or lang-image-uploader/xx.php) exists and pass the 'language' key in the $config array when instantiating the class. The default language is English ('en').

## Error Handling
The upload() method returns true on successful upload and false on failure. Error messages are stored in the $errorMessages public property as an array. You can check for errors using the checkError() method (returns true if there are errors) or by checking if the $errorMessages array is not empty. The countErrors() method returns the number of error messages.

## Uploader
* $file: (array|null) The $_FILES array for the uploaded file.
* $newFileName: (string|null) The new name to assign to the uploaded file (without the extension). If not set, the original name is used.
* $fileNameWithExtension: (string|null) The final file name with its extension after processing.
* $allowFileTypes: (array) An array of allowed MIME types.
* $allowFileExtensions: (array) An array of allowed file extensions.
* $maxSize: (int) The maximum allowed file size in bytes.
* $minSize: (int) The minimum allowed file size in bytes.
* $dir: (string|null) The directory where the uploaded file will be saved. **This property must be set before calling** upload().
* $errorMessages: (array) An array containing error messages that occurred during the upload process.
* $activeLanguage: (string) The currently active language code.

## Image Uploader
* $maxWidth: (int) The maximum allowed width for the image in pixels.
* $maxHeight: (int) The maximum allowed height for the image in pixels.
* $minWidth: (int) The minimum allowed width for the image in pixels.
* $minHeight: (int) The minimum allowed height for the image in pixels.
* $fileWidth: (int|null) The width of the uploaded image file in pixels (after successful check).
* $fileHeight: (int|null) The height of the uploaded image file in pixels (after successful check).

## Methods (Public)
**Uploader**
* __construct(array $config = []): Initializes the uploader with optional configuration, including 'file' (the $_FILES array) and 'language' (the language code).
* checkError(): Returns true if the $errorMessages array is not empty, false otherwise.
* countErrors(): Returns the number of error messages in the $errorMessages array.
* upload(): Starts the file upload process. Returns true on success, false on failure.

**Image Uploader**
* __construct(array $config = []): Initializes the image uploader, extending the parent constructor and allowing for optional 'file' and 'language' configuration.
* upload(): Starts the image upload process, including all checks from the parent Uploader class and additional checks for valid image types and dimensions. Returns true on success, false on failure.

## License
This project is licensed under the MIT License - see the [LICENSE](http://mit-license.org) file for details.
```
MIT License

Copyright (c) 2015 Ali Candan

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## Contributing
Contributions are welcome! If you find any bugs or have suggestions for improvements, please `feel free to open an issue or submit a pull request on the GitHub repository.`

## Support
For any questions or support regarding the W-PHP Uploader, you can refer to the project's GitHub repository or contact the author.
