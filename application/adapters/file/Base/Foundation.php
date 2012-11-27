<?php

/**
 * Foundation File Upload Class
 * 
 * @package     BuzzyGals
 * @subpackage  File_Transfer
 * @version     1
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * 
 * 
 */
abstract class File_Adapter_Base_Foundation {
    
    protected $_uploadType;
    
    public function __construct($uploadType) {
        $this->_uploadType = $uploadType;
    }
    
    public function generateFilename($ext, $userid = 0) {
        if($this->_uploadType == "image")    
            return time().$userid.mt_rand().$ext;
    }
}
