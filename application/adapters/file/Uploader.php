<?php

/**
 * Public File Upload Class
 * 
 * @package     BuzzyGals
 * @subpackage  File_Transfer
 * @version     1
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 */
class File_Adapter_Uploader {
    protected $_allowedExtensions;
    
    protected $_sizeLimit;
    
    protected $_uploader;
    
    protected $_uploadName;
    
    public function __construct($uploadType, array $extensions = NULL, $sizeLimit = NULL) {
        if($extensions === NULL) 
            $extensions = array();
        if($sizeLimit === NULL)
            $sizeLimit = $this->_toBytes(Zend_Registry::get('uploadMaxSize'));
        $this->_allowedExtensions = array_map("strtolower", $extensions);
        $this->_sizeLimit = $this->_toBytes($sizeLimit);
        if(strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') === 0)
            $this->_uploader = new File_Adapter_Form_Upload($uploadType);
        else 
            $this->_uploader = new File_Adapter_Xhr_Upload($uploadType);
    }
    
    private function _toBytes($text) {
        $value = trim($text);
        $last = strtolower($text[strlen($text)-1]);
        switch($last) {
            case 'g': $value *= 1024;
            case 'm': $value *= 1024;
            case 'k': $value *= 1024;
        }
        return $value;
    }
    
    public function getUploadName() {
        return (isset($this->_uploadName)) ? $this->_uploadName : false;
    }
    
    public function getOriginalName() {
        return (is_object($this->_uploader)) ? $this->_uploader->getName() : false;
    }
    
    
    public function checkServerSettings() {
        $postSize = $this->_toBytes(ini_get('post_max_size'));
        $uploadSize = $this->_toBytes(ini_get('upload_max_filesize'));
        if($postSize < $this->_sizeLimit || $uploadSize < $this->_sizeLimit) {
            $size = max(1, $this->_sizeLimit/1024/1024).'M';
            return array('error' => 'increase post_max_size and upload_max_filesize to '.$size);
        }
        else
            return true;
    }
    
    public function handleUpload($path, $userid = 0) {
        if(!is_writable($path))
            return array('error' => "Upload Directory Isn't Writable");
        try {
            $size = $this->_uploader->getSize();   
        }
        catch (Exception $e) {
            return array('error' => "File Size Not Available");
        }
        if(!$size)
            return array('error' => "File is empty");
        else if($size > $this->_sizeLimit) {
            return array('error' => "File is too large");
        }
        $fileinfo = pathinfo($this->_uploader->getName());
        $extension = @$fileinfo['extension'];
        if($this->_allowedExtensions && !in_array(strtolower($extension), $this->_allowedExtensions)) {
            $these = implode(",", $this->_allowedExtensions);
            return array('error' => "File has an invalid extension : ".$extension.". It should be one of these: ".$these);
        }
        $extension = ($extension == "") ? $extension : '.'.$extension;
        if($filename = $this->_uploader->save($path, $extension, $userid))
            return array('success' => true, 'filename' => $filename);
        else 
            return array('error' => "There was an error. The upload was cancelled");
    }
}
