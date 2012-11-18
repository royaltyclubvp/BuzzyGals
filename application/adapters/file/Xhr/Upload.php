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
class File_Adapter_Xhr_Upload {
    
    public function getName() {
        return $this->getRequest()->getParam('filename');
    }
    
    public function getSize() {
        if(isset($_SERVER['CONTENT_LENGTH']))
            return (int)$_SERVER['CONTENT_LENGTH'];
        else 
            throw new Exception('Content length is not available');
    }
    
    public function save($path, $userid) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $size = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if($size != $this->getSize()) 
            return false;
        
        $destination = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $destination);
        fclose($destination);
        
        return true;
    }
    
}
