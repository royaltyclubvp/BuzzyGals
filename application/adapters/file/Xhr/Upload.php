<?php

/**
 * Handle File Uploads via XMLHttpRequest
 * 
 * @package     BuzzyGals
 * @subpackage  File_Transfer
 * @version     1
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * 
 * 
 */
class File_Adapter_Xhr_Upload extends File_Adapter_Base_Foundation {
    
    public function __construct($uploadType) {
        parent::__construct($uploadType);
    }
    
    public function getName() {
        return $this->getRequest()->getParam('filename');
    }
    
    public function getSize() {
        if(isset($_SERVER['CONTENT_LENGTH']))
            return (int)$_SERVER['CONTENT_LENGTH'];
        else 
            throw new Exception('Content length is not available');
    }
    
    public function save($path, $extension, $userid = 0) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $size = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if($size != $this->getSize()) 
            return false;
        $filename = $this->generateFileName($extension, $userid);
        $destination = fopen($path.$filename, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $destination);
        fclose($destination);
        
        return $filename;
    }
    
}
