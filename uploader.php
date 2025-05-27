<?php
/*
W-PHP Uploader
=====================
File: uploader.php
Author: Ali Candan [Webkolog] <webkolog@gmail.com> 
Homepage: http://webkolog.net
GitHub Repo: https://github.com/webkolog/php-uploader
Last Modified: 2016-02-03
Compatibility: PHP 7+
@version     1.1

Copyright (C) 2015 Ali Candan
Licensed under the MIT license http://mit-license.org

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
class uploader {

    public $file = null;
    public $newFileName = null;
	public $fileNameWithExtension = null;
    public $allowFileTypes = array();
    public $allowFileExtensions = array();
    public $maxSize = 50000;
    public $minSize = 0;
    public $dir = null;
    public $errorMessages = Array();
	public $activeLanguage = 'en';
    protected $fileName = null;
    protected $fileSize = null;
    protected $fileExtension = null;
    protected $fileType = null;
    protected $fullName = null;
    protected $tmpName = null;
    protected $fileChecked = false;
	protected $lang = [];
	
	function __construct(array $config = []) {
		$this->loadLanguage($config['language'] ?? 'en');
		$this->file = $config['file'] ?? null;
	}
	
	protected function loadLanguage($langCode) {
        $langFile = __DIR__ . '/lang-uploader/' . $langCode . '.php';
        if (file_exists($langFile)) {
            $this->lang = include $langFile;
        } else {
            $defaultLangFile = __DIR__ . '/lang-uploader/en.php';
            if (file_exists($defaultLangFile))
                $this->lang = include $defaultLangFile;
            else
                die("Error: Default language file not found!");
        }
    }
	
	protected function getTranslation($key) {
        return $this->lang[$key] ? $this->lang[$key] : $key;
    }

    public function checkError() {
        return count($this->errorMessages) > 0;
    }

    public function countErrors() {
        return count($this->errorMessages);
    }

    protected function checkFile() {
        if (@is_uploaded_file($this->file["tmp_name"])) {
            $this->fullName = $this->file["name"];
            $parts = explode('.', $this->fullName);
            $parts_count = count($parts);
			$this->fileExtension = $parts_count > 1 ? end($parts) : null;
			$this->fileNameWithExtension = $this->newFileName . "." . $this->fileExtension;
            $extension_len = strlen($this->fileExtension) + 1;
            $this->fileName = substr($this->fullName, 0, -$extension_len);
            $this->fileSize = $this->file["size"];
            $this->fileType = $this->file["type"];
            $this->tmpName = $this->file["tmp_name"];
			if (!in_array($this->fileExtension, $this->allowFileExtensions))
				$this->errorMessages[] = $this->getTranslation('invalid_extension');
			if (!in_array($this->fileType, $this->allowFileTypes)) 
				$this->errorMessages[] = $this->getTranslation('invalid_type');
			if ($this->fileSize < $this->minSize)
				$this->errorMessages[] = $this->getTranslation('file_too_small');
			if ($this->fileSize > $this->maxSize)
				$this->errorMessages[] = $this->getTranslation('file_too_large');
			return count($this->errorMessages) == 0;
        } else {
			$this->errorMessages[] = $this->getTranslation('no_file_selected');
			return false;
		}
    }

     private function permission() {
        return empty($this->errorMessages) && ($this->fileChecked || $this->checkFile());
    }

    public function upload() {
        if ($this->permission()) {
			$fileName = $this->newFileName != null ? $this->newFileName . "." . $this->fileExtension : $this->fullName;
            $tmpName = (substr($this->dir, -1) == "/" ? $this->dir : $this->dir . "/") . $fileName;
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