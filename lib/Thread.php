<?php

require_once 'Message.php';
require_once "../../lib/RBUtilities.php";

class Thread {

    /**
     * @var integer
     * thread ID
     */
    private $tid;

    /**
     * @var UserBean
     * users of messages thread
     */
    private $user1;
    private $user2;

    /**
     * @var array
     * list of messages in thread
     */
    private $messages;

    /**
     * @var Message
     * newer message in thread
     */
    private $last_message;
    
    /**
     * source of data
     * @var MySQLDataLoader
     */
    private $datasource;

	/**
	 * 
	 * @param integer $tid
	 * @param UserBean $u1
	 * @param UserBean $u2
	 * @param DataLoader $ds
	 */

    public function __construct($tid, UserBean $u1, UserBean $u2, DataLoader $ds){
        $this->tid = $tid;
        $this->user1 = $u1;
        $this->user2 = $u2;
        $this->datasource = $ds;
        $this->loadMessages();
        $this->last_message = null;
    }
    
    public function restoreThread(DataLoader $dl){
    	$this->datasource = $dl;
    }
    
    public function getOtherUser($uid){
    	if ($this->user1->getUid() == $uid){
    		return $this->user2;
    	}
    	return $this->user1;
    }
    
    public function getMe($uid){
    	if ($this->user1->getUid() == $uid){
    		return $this->user1;
    	}
    	return $this->user2;
    }
    
    public function getOtherUserType(){
    	$ot = null;
    	if ($this->user1->getUid() == $uid){
    		$ot = $this->user2;
    	}
    	else {
    		$ot = $this->user1;
    	}
    	if ($ot instanceof SchoolUserBean){
    		$sender_type = "school";
    	}
    	else if ($ot instanceof ParentBean){
    		$sender_type = "parent";
    	}
    	else {
    		$sender_type = "student";
    	}
    	return $sender_type;
    }
    
    public function getMessage($mid){
    	return $this->messages[$mid];
    }

    public function getMessages(){
        return $this->messages;
    }
    
    public function setMessages($messages){
    	$this->messages = $messages;
    }
    
    public function getUser1(){
    	return $this->user1;
    }
    
    public function getUser2(){
    	return $this->user2;
    }
    
    public function getUserType(){
    	$sender_type = "";
    	$target_type = "";
    	if ($this->user1 instanceof SchoolUserBean){
    		$sender_type = "school";
    	}
    	else if ($this->user1 instanceof ParentBean){
    		$sender_type = "parent";
    	}
    	else {
    		$sender_type = "student";
    	}
    	if ($this->user2 instanceof SchoolUserBean){
    		$target_type = "school";
    	}
    	else if ($this->user2 instanceof ParentBean){
    		$target_type = "parent";
    	}
    	else {
    		$target_type = "student";
    	}
    	return array($sender_type, $target_type);
    }
    
    private function loadMessages(){
    	$messages = $this->datasource->executeQuery("SELECT * FROM rb_com_messages WHERE tid = {$this->tid} ORDER BY mid DESC");
    	if ($messages != null){
	    	foreach ($messages as $message){
	    		$sender = $target = null;
	    		if ($message['sender'] == $this->user1->getUid()){
	    			$sender = $this->user1;
	    			$target = $this->user2;
	    		}
	    		else {
	    			$sender = $this->user2;
	    			$target = $this->user1;
	    		}
	    		$msg = new Message($message['mid'], $this->tid, $sender, $target, $this->datasource, $message);
	    		$this->messages[$message['mid']] = $msg;
	    	}
    	}
    }
    
    public function getLastMessage(){
    	$msg = $this->messages;
    	return array_shift($msg);
    }
    
    public function getMessagesCount(){
    	return count ($this->messages);
    }

    public function getUnreadMessages(UserBean $user){
    	$unread = array();
		foreach ($this->messages as $msg){
			if ($user->getUid() == $msg->getTo()->getUid()){
				if ($msg->getReadTimestamp() == "" || $msg->getReadTimestamp() == null){
					$unread[$msg->getID()] = $msg;
				}
			}
		}
		if (count($unread) > 0){
			return $unread;
		}
		
		return null;
    }

    public function getUnreadMessagesCount($user){
		
    }
    
    public function addMessage(Message $msg){
    	$this->messages[$msg->getID()] = $msg;
    	$this->last_message = $msg;
    	krsort($this->messages);
    	$this->datasource->executeUpdate("UPDATE rb_com_threads SET last_message = {$msg->getID()} WHERE tid = {$this->tid}");
    }
    
    public function save(){
    	$sender_type = "";
    	$target_type = "";
    	if ($this->user1 instanceof SchoolUserBean){
    		$sender_type = "school";
    	}
    	else if ($this->user1 instanceof ParentBean){
    		$sender_type = "parent";
    	}
    	else {
    		$sender_type = "student";
    	}
    	if ($this->user2 instanceof SchoolUserBean){
    		$target_type = "school";
    	}
    	else if ($this->user2 instanceof ParentBean){
    		$target_type = "parent";
    	}
    	else {
    		$target_type = "student";
    	}
    	$this->tid = $this->datasource->executeUpdate("INSERT INTO rb_com_threads (user1, user2, user1_group, user2_group) VALUES ({$this->user1->getUid()}, {$this->user2->getUid()}, '{$sender_type}', '{$target_type}')");
    }
    
    public function getTid(){
    	return $this->tid;
    }
    
    public function getUsers(){
    	return array($this->user1, $this->user2);
    }
    
    public function readAll($user){
    	$this->datasource->executeUpdate("UPDATE rb_com_messages SET read_timestamp = NOW() WHERE read_timestamp IS NULL AND tid = {$this->tid} AND target = {$user->getUid()}");
    	foreach ($this->messages as $msg){
    		if (($msg->getTo()->getUid() == $user->getUid()) && ($msg->getReadTimestamp() == "" || $msg->getReadTimestamp() == null)){
    			$ts = date("Y-m-d H:i:s");
    			$msg->setReadTimestamp($ts);
    		}
    	}
    	return $ts;
    }
    
    public function isRead($user){
    	foreach ($this->messages as $msg){
    		if (($msg->getTo()->getUid() == $user->getUid()) && ($msg->getReadTimestamp() == "" || $msg->getReadTimestamp() == null)){
    			return false;
    		}
    	}
    	return true;
    }
    
    public function getText(){
    	return $this->text;
    }
    
    public function checkForUpdates(){
    	$news = array();
    	$new_messages = $this->datasource->executeQuery("SELECT * FROM rb_com_messages WHERE tid = {$this->getTid()} AND target = {$_SESSION['__user__']->getUid()} AND mid > {$this->getLastMessage()->getID()} ORDER BY send_timestamp ASC");
    	if ($new_messages){
    		foreach ($new_messages as $msg){
    			$message = new Message($msg['mid'], $this->getTid(), $this->getOtherUser($_SESSION['__user__']->getUid()), $this->getMe($_SESSION['__user__']->getUid()), $this->datasource, $msg);
    			$message->read();
    			$this->addMessage($message);
    			list($data, $time) = explode(" ", $message->getSendTimestamp());
    			if (date("Y-m-d") == $data){
    				$data = "Inviato oggi alle";
    			}
    			else {
    				$data = "Inviato il ".format_date($data, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ";
    			}
    			$data .= " ".substr($time, 0, 5);
    			
    			list($rdate, $rtime) = explode(" ", $message->getReadTimestamp());
    			if (date("Y-m-d") == $rdate){
    				$rdate = "oggi alle";
    				$rtime = substr($rtime, 0, 5);
    			}
    			else {
    				$rdate = "il ". format_date($date, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ";
    				$rtime = substr($rtime, 0, 5);
    			}
    			array_unshift($news, array("type" => "new", "mid" => $msg['mid'], "send" => $data, "read" => $rdate.$rtime, "text" => $msg['text']));
    		}
    	}
    	/*
    	 * read timestamp
    	 */
    	$unread = $this->getUnreadMessages($this->getOtherUser($_SESSION['__user__']->getUid()));
    	if ($unread != null){
    		foreach ($unread as $k => $row){
    			$reads = $this->datasource->executeCount("SELECT read_timestamp FROM rb_com_messages WHERE mid = {$row->getID()}");
    			if ($reads != null){
	    			list($rdate, $rtime) = explode(" ", $reads);
	    			$date = "oggi alle ".substr($rtime, 0, 5);
	    			array_unshift($news, array("type" => "read", "mid" => $row->getID(), "read" => $date));
	    			$message = $this->getMessage($k);
	    			$message->setReadTimestamp($reads);
	    			$this->addMessage($message);
    			}
    		}
    	}   		
    	if (count($news) > 0){
    		return $news;
    	}
    	
    	return null;
    }
}