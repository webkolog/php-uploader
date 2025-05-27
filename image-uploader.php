<?php
/*
W-PHP Image Uploader
=====================
File: image-uploader.php
Author: Ali Candan [Webkolog] <webkolog@gmail.com> 
Homepage: http://webkolog.net
GitHub Repo: https://github.com/webkolog/php-image-uploader
Last Modified: 2016-02-03
Compatibility: PHP 7+
@version     1.0.2

Copyright (C) 2015 Ali Candan
Licensed under the MIT license http://mit-license.org

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
include("uploader.php");

class image-uploader extends uploader {

    public $maxWidth = 1024;
    public $maxHeight = 768;
    public $minWidth = 0;
    public $minHeight = 0;
    public $fileWidth = null;
    public $fileHeight = null;
	protected $lang = [];
	
    function __construct(array $config = []) {
        parent::__construct($config);
		$this->loadLanguage('image-uploader', $config['language'] ?? 'en');
		$allowedFileTypes = ["image/gif", "image/jpeg", "image/pjpeg", "image/png", "image/x-png", "image/x-icon", "image/bmp"];
		$allowedFileExtensions = ["gif", "jpg", "jpeg", "png", "bmp", "ico"];
		$this->allowFileTypes = $this->findAllowed($this->allowFileTypes, $allowedFileTypes);
		$this->allowFileExtensions = $this->findAllowed($this->allowFileExtensions, $allowedFileExtensions);
    }
	
	function __construct(array $config = []) {
        parent::__construct($config);
		$imageUploaderLangCode = $config['language'] ?? 'en';
        $langFile = __DIR__ . '/lang-image-uploader/' . $imageUploaderLangCode . '.php';
        if (file_exists($langFile)) {
            $this->lang = array_merge($this->lang, include $langFile);
        } else {
            $defaultLangFile = __DIR__ . '/lang-image-uploader/en.php';
            if (file_exists($defaultLangFile))
                $this->lang = array_merge($this->lang, include $defaultLangFile);
            else
                die("Error: Default image-uploader language file not found!");
        }
		
		$allowedFileTypes = ["image/gif", "image/jpeg", "image/pjpeg", "image/png", "image/x-png", "image/x-icon", "image/bmp"];
		$allowedFileExtensions = ["gif", "jpg", "jpeg", "png", "bmp", "ico"];
		$this->allowFileTypes = $this->findAllowed($this->allowFileTypes, $allowedFileTypes);
		$this->allowFileExtensions = $this->findAllowed($this->allowFileExtensions, $allowedFileExtensions);
    }
	
	protected function loadLanguage($prefix, $langCode) {
        $langFile = __DIR__ . '/lang-' . $prefix . '/' . $langCode . '.php';
        if (file_exists($langFile)) {
            $this->lang = array_merge($this->lang, include $langFile); // Üst sınıfın dilleriyle birleştir
        } else {
            $defaultLangFile = __DIR__ . '/lang-' . $prefix . '/en.php';
            if (file_exists($defaultLangFile))
                $this->lang = array_merge($this->lang, include $defaultLangFile);
            else
                die("Error: Default " . $prefix . " language file not found!");
        }
    }

	protected function getTranslation($key) {
        return $this->lang[$key] ?? $key;
    }
	
	private function findAllowed($allowedTypes,$types) {
		if (count($allowedTypes) > 0) {
			$newTypes = array();
			foreach ($allowedTypes as $allowedType)
				if (in_array($allowedType, $types))
					array_push($newTypes, $allowedType);
			return $newTypes;
		}
		return $types;
	}

    protected function checkFile() {
        $checkFileResult = parent::checkFile();
        if ($checkFileResult) {
			if (!in_array($this->fileType, $this->allowFileTypes)) {
                $this->errorMessages[] = $this->getTranslation('invalid_image_type'); // Yeni dil anahtarı
            } else {
                list($this->fileWidth, $this->fileHeight) = getimagesize($this->file["tmp_name"]);
                if ($this->fileWidth > $this->maxWidth)
					$this->errorMessages[] = $this->getTranslation('image_width_too_long', ['max' => $this->maxWidth]);
                else if ($this->fileWidth < $this->minWidth)
					$this->errorMessages[] = $this->getTranslation('image_width_too_short', ['min' => $this->minWidth]);
                if ($this->fileHeight > $this->maxHeight)
					$this->errorMessages[] = $this->getTranslation('image_height_too_long', ['max' => $this->maxHeight]);
                else if ($this->fileHeight < $this->minHeight)
					$this->errorMessages[] = $this->getTranslation('image_height_too_short', ['min' => $this->minHeight]);
            }
			return count($this->errorMessages) == 0;
        }
        return false;
    }
	
	private function permission() {
		return (count($this->errorMessages) === 0) && ($this->fileChecked || $this->checkFile());
	}

    public function upload() {
        if ($this->permission()) {
			$fileName = $this->newFileName ?? $this->fullName;
            $tmpName = rtrim($this->dir, '/') . '/' . $fileName;
            if (file_exists($tmpName)) {
                $this->errorMessages[] = $this->getTranslation('file_exists');
                return false;
            }
            if (move_uploaded_file($this->file["tmp_name"], $tmpName)) {
                $this->tmpName = $tmpName;
                return true;
            } else {
                $this->errorMessages[] = $this->getTranslation('upload_failed');
                return false;
            }
        }
        return false;
    }

}
