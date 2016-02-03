<?php
// Ensure the base Uploader class is included before extending it
include_once("uploader.php");

/**
 * ImageUploader handles specialized image validation constraints 
 * such as dimensions (width/height) and restrictive image MIME types.
 */
class ImageUploader extends Uploader {
    // Image-specific constraint boundaries (Default resolution: 1024x768)
    public $maxWidth = 1024;
    public $maxHeight = 768;
    public $minWidth = 0;
    public $minHeight = 0;
    
    // Dynamically populated properties after getimagesize() executes
    public $fileWidth = null;
    public $fileHeight = null;
    
    /**
     * Re-initializes configurations, merges specific image localization strings, 
     * and establishes strict fallback lists for allowed image types/extensions.
     */
    public function __construct(array $config = []) {
        // Execute the parent constructor to map the basic file structure
        parent::__construct($config);
        
        $imageUploaderLangCode = $config['language'] ?? 'en';
        $this->loadImageLanguage('image-uploader', $imageUploaderLangCode);
        
        // Hardcoded standard safe image formats allowed by default
        $allowedFileTypes = ["image/gif", "image/jpeg", "image/pjpeg", "image/png", "image/x-png", "image/x-icon", "image/bmp"];
        $allowedFileExtensions = ["gif", "jpg", "jpeg", "png", "bmp", "ico"];
        
        // Intersection check: Merges custom user-defined criteria with the hardcoded list
        $this->allowFileTypes = $this->findAllowed($this->allowFileTypes, $allowedFileTypes);
        $this->allowFileExtensions = $this->findAllowed($this->allowFileExtensions, $allowedFileExtensions);
    }
    
    /**
     * Merges image-specific translation keys into the inherited $this->lang array
     * without overriding the base translations populated by the parent class.
     */
    protected function loadImageLanguage($prefix, $langCode) {
        $langFile = __DIR__ . '/lang-' . $prefix . '/' . $langCode . '.php';
        if (file_exists($langFile)) {
            $this->lang = array_merge($this->lang, include $langFile);
        } else {
            $defaultLangFile = __DIR__ . '/lang-' . $prefix . '/en.php';
            if (file_exists($defaultLangFile)) {
                $this->lang = array_merge($this->lang, include $defaultLangFile);
            }
        }
    }
    
    /**
     * Helper filter to calculate the intersection between custom runtime configuration 
     * options and class-level allowed default formats.
     */
    private function findAllowed($allowedTypes, $types) {
        if (count($allowedTypes) > 0) {
            $newTypes = array();
            foreach ($allowedTypes as $allowedType) {
                if (in_array($allowedType, $types)) {
                    array_push($newTypes, $allowedType);
                }
            }
            return $newTypes;
        }
        return $types;
    }

    /**
     * CRITICAL OVERRIDE: Extends the base checkFile execution loop.
     * Runs basic file validation checks first, then opens the binary image header 
     * using getimagesize() to enforce strict spatial resolution boundaries.
     */
    public function checkFile() {
        // Run standard checks first (size, base extension, upload status)
        $checkFileResult = parent::checkFile();
        
        if ($checkFileResult) {
            // Verify if parsed type is present inside the safe image whitelist
            if (!in_array($this->fileType, $this->allowFileTypes)) {
                $this->errorMessages[] = $this->getTranslation('invalid_image_type');
            } else {
                // Read physical dimensions directly from the temporary target file
                list($this->fileWidth, $this->fileHeight) = getimagesize($this->file["tmp_name"]);
                
                // Validate horizontal width thresholds
                if ($this->fileWidth > $this->maxWidth)
                    $this->errorMessages[] = $this->getTranslation('image_width_too_long', ['max' => $this->maxWidth]);
                else if ($this->fileWidth < $this->minWidth)
                    $this->errorMessages[] = $this->getTranslation('image_width_too_short', ['min' => $this->minWidth]);
                
                // Validate vertical height thresholds
                if ($this->fileHeight > $this->maxHeight)
                    $this->errorMessages[] = $this->getTranslation('image_height_too_long', ['max' => $this->maxHeight]);
                else if ($this->fileHeight < $this->minHeight)
                    $this->errorMessages[] = $this->getTranslation('image_height_too_short', ['min' => $this->minHeight]);
            }
            // Return true only if no dimensional or structural anomalies were found
            return count($this->errorMessages) == 0;
        }
        return false;
    }
}