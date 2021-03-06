<?php

/**
 * Handle File Uploads via Form POST
 * 
 * @package     BuzzyGals
 * @subpackage  File_Transfer
 * @version     1
 * @author      Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * 
 * 
 */
class File_Adapter_Form_Upload  extends File_Adapter_Base_Foundation {
    
    public function __construct($uploadType) {
        parent::__construct($uploadType);
    }
    
    public function getName() {
        return $_FILES['filename']['name'];
    }
    
    public function getSize() {
        return $_FILES['filename']['size'];
    }
    
    public function save($path, $extension, $userid = 0) {
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $adapter->setDestination($path);
        $files = $adapter->getFileInfo();
        foreach($files as $file => $info) {
            if(!$adapter->isUploaded($file)) {
                return false;
            }
            $filename = $this->generateFileName($extension, $userid);
            $adapter->addFilter('Rename', array('target' => $path.$filename, 'overwrite' => TRUE));
            if(!$adapter->receive($info['name']))
                return false;
        }
        $filename = $adapter->getFileName();
        $filename = basename($filename);
        return $filename;
    }
}
