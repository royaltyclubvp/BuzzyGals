<?php

/**
 * Message Service Class
 * 
 * @package         ElleFab
 * @subpackage      Model Service Layer
 * @author          Jarrod Placide-Raymond <royaltyclubvp@royalty-club.com>
 * @version         0.1
 */
class Service_Message extends Service_Base_Foundation {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Adds New Message
     * 
     * @param   integer     $user           Sending User's ID
     * @param   array | int $recipients     Recipient's ID
     * @param   char        $type           Message Type ['n' => New, 'f' => Forward , 'r' => Reply]
     * @param   integer     $ref            Reference Message
     * @param   string      $subject        Message Subject
     * @param   string      $content        Message Content
     * @return  array | bool
     */
     public function addNew($user, $recipients, $type = 'n', $ref = NULL, $subject = null, $content = '') {
         $new = new Model_Messages();
         $new->fromArray(array(
            'sender' => $user,
            'date' => date('Y-m-d H:i:s'),
            'type' => $type,
            'content' => $content,
            'reference' => $ref,
            'subject' => $subject
         ));
         if(is_array($recipients)) {
             $x = 0;
             foreach($recipients as $recipient) {
                $recipients = new Model_Recipient();
                $recipients->user = $recipient; 
                $new->Recipients[$x++] = $recipients;
             }
         }
        else {
            $recipient = new Model_Recipient();
            $recipient->user = $recipients;
            $new->Recipients[0] = $recipient;
        }
        try {
            $new->save();
        }
        catch (Doctrine_Exception $e) {
            return $e->getMessage();
        }
        return $new->toArray();
     }
     
     /**
      * Retrieves Message
      * 
      * @param  integer     $message        Message ID
      * @param  integer     $user           User's ID
      * @return bool | array
      */ 
     public function fetchMessage($message, $user) {
         $query = Doctrine_Query::create()
                ->from('Model_Messages m')
                ->innerJoin('m.Sender s')
                ->innerJoin('s.Profile sp')
                ->innerJoin('m.Recipients r')
                ->innerJoin('r.User u')
                ->innerJoin('u.Profile up')
                ->where('m.id = ?',$message)
                ->andWhere('m.sender = ? OR r.user = ?', array($user, $user));
         try {
             $messages = $query->fetchArray();
         }
         catch (Doctrine_Exception $e) {
             return $e->getMessage();
         }
         if(!count($messages)) return false;
         return $messages[0];
     }
     
     /**
      * Changes Message Status To Deleted
      * 
      * @param  integer     $message        Message ID
      * @param  integer     $user           User ID
      * @return bool
      */
     public function deleteMessage($message, $user) {
         $query = Doctrine_Query::create()
                ->update('Model_Recipient')
                ->set('deleted',1)
                ->where('message = ?',$message)
                ->andWhere('user = ?',$user);
         try {
             $result = $query->execute();
         }
         catch (Doctrine_Exception $e) {
             return $e->getMessage();
         }
         return $result;
     }
     
     /**
      * Fetches Messages Received By User
      * 
      * @param  integer     $user           User ID
      * @param  integer     $page           Page Number Desired | Default = 1
      * @param  integer     $noPerPage      Results Per Page | Default = 20
      * @return array | bool
      */
     public function fetchReceived($user, $page = 1, $noPerPage =  20) {
         $pager = new Doctrine_Pager(
            Doctrine_Query::create()->from('Model_Messages m')
                            ->innerJoin('m.Recipients r')
                            ->innerJoin('m.SenderProfile p')
                            ->where('r.user = ?', $user)
                            ->andWhere('r.deleted = ?', 0)
                            ->orderby('m.date DESC'),
            $page,
            $noPerPage
         );
         try {
            $results = $pager->execute();
         }
         catch (Doctrine_Exception $e) {
            return $e->getMessage();
         }
         return $results->toArray();
     }
     
     /**
      * Fetches Messages Sent By User
      * 
      * @param  integer     $user           User ID
      * @param  integer     $page           Page Number Desired | Default = 1
      * @param  integer     $noPerPage      Results Per Page | Default = 20
      * @return array | bool
      */
     public function fetchSent($user, $page = 1, $noPerPage = 20) {
         $pager = new Doctrine_Pager(
            Doctrine_Query::create()
                            ->from('Model_Messages m')
                            ->innerJoin('m.Recipients r')
                            ->innerJoin('r.User u')
                            ->innerJoin('u.Profile p')
                            ->where('m.sender = ?', $user)
                            ->orderby('m.date DESC'),
            $page,
            $noPerPage
         );
         try {
            $results = $pager->execute();
         }
         catch (Doctrine_Exception $e) {
            return $e->getMessage();
         }
         return $results->toArray();
     }
     
     /**
      * Changes Message Status to Read
      * 
      * @param  integer     $message           Message ID
      * @param  integer     $user              User ID
      * @param  integer     $status            New Read Status
      * @return bool
      */
     public function toggleRead($message, $user, $status) {
         $query = Doctrine_Query::create()
                ->update('Model_Recipient')
                ->set('seen',$status)
                ->where('message = ?',$message)
                ->andWhere('user = ?',$user);
         try {
             $result = $query->execute();
         }
         catch (Doctrine_Exception $e) {
             return $e->getMessage();
         }
         return $result;
     }
     
     /**
      * Retrieves Number of New Messages
      * 
      * @param  integer     $user               User ID
      * @return bool | array
      */
     public function messageCount($user) {
         $query = Doctrine_Query::create()->from('Model_Messages m')
                ->innerJoin('m.Recipients r')
                ->innerJoin('m.SenderProfile p')
                ->where('r.user = ?', $user)
                ->andWhere('r.seen = ?', 0);
         try {
             $result = $query->count();
         }
         catch (Doctrine_Exception $e) {
             return $e->getMessage();
         }
         return $result;
     }
}
