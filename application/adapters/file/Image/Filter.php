<?php

/**
 * Image Filtering Class
 * 
 * @package     BuzzyGals
 * @subpackage  File_Transfer
 * @version     1
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * 
 * 
 */
class File_Adapter_Image_Filter {
    
    protected $_savePath;
    
    protected $_filter;
    
    protected $_imageType;
    
    public function __construct($path) {
        $this->_savePath = $path;
        $this->_filter = new Polycast_Filter_ImageSize();
        $this->_filter->setOutputPathBuilder(new Polycast_Filter_ImageSize_PathBuilder_Standard($path));
    }
    
    public function setConfig($height, $width, $quality, $type, $crop = TRUE) {
        $this->_filter->getConfig()
                ->setHeight($height)
                ->setWidth($width)
                ->setQuality($quality)
                ->setOverwriteMode(Polycast_Filter_ImageSize::OVERWRITE_ALL);
        if($type = 'jpeg') {
            $this->_filter->getConfig()->setOutputImageType(Polycast_Filter_ImageSize::TYPE_JPEG);
            $this->_imageType = 'Content-Type: image/jpeg';
        }
        if($crop) $this->_filter->getConfig()->setStrategy(new Polycast_Filter_ImageSize_Strategy_Crop());
    }
    
    public function generateThumb($original) {
        $output = $this->_filter->filter($original);
    }
}
