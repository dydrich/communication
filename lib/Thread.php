<?php

require_once 'Message.php';
require_once "../../lib/RBUtilities.php";

class Thread {

    /**
     * @var integer
     * thread ID
     */
    protected $tid;

	private $users;

	private $name;

	private $systemThread;

	private $owner;

	private $type;

	private $creationDate;

	private $lastAccesses;

    /**
     * @var array
     * list of messages in thread
     */
	protected $messages;

    /**
     * @var Message
     * newer message in thread
     */
	protected $last_message;
    
    /**
     * source of data
     * @var MySQLDataLoader
     */
	protected $datasource;

	/**
	 * 
	 * @param integer $tid
	 * @param DataLoader $ds
	 */

    public function __construct($tid, DataLoader $ds, $cd, $name = null, $type = 'C', $users = null){
        $this->tid = $tid;
        $this->datasource = $ds;
	    $this->users = $users;
	    $this->lastAccesses = array();
        $this->loadMessages();
	    $this->loadUsers();
        $this->last_message = null;
	    $this->systemThread = 0;
	    $this->creationDate = $cd;
	    $this->type = $type;
	    $this->name = $name;
    }
    
    public function restoreThread(DataLoader $dl){
    	$this->datasource = $dl;
    }

	public function loadUsers() {
		$res = $this->datasource->executeQuery("SELECT utente, last_access FROM rb_com_utenti_thread WHERE thread = {$this->tid}");
		if ($res) {
			foreach ($res as $row) {
				$this->users[] = $row['utente'];
				$this->lastAccesses[$row['utente']] = $row['last_access'];
			}
		}
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

	/**
	 * @param mixed $creationDate
	 */
	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}

	/**
	 * @return mixed
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	/**
	 * @param UserBean $owner
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}

	/**
	 * @return UserBean
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * @param boolean $systemThread
	 */
	public function setSystemThread($systemThread) {
		$this->systemThread = $systemThread;
	}

	/**
	 * @return boolean
	 */
	public function isSystemThread() {
		return $this->systemThread;
	}

	/**
	 * @param null $lastAccesses
	 */
	public function setLastAccesses($lastAccesses) {
		$this->lastAccesses = $lastAccesses;
	}

	/**
	 * @return null
	 */
	public function getLastAccesses() {
		return $this->lastAccesses;
	}
    
    protected function loadMessages(){
	    $rb = \RBUtilities::getInstance($this->datasource->getSource());
	    $messages = $this->datasource->executeQuery("SELECT * FROM rb_com_messages WHERE tid = {$this->tid} ORDER BY mid DESC");
	    if ($messages != null){
		    foreach ($messages as $message){
			    $sender = $rb->loadUserFromUniqID($message['sender']);
			    $target = $this->tid;

			    $msg = new Message($message['mid'], $this->tid, $sender, $target, $this->datasource, $message);
			    $this->messages[$message['mid']] = $msg;
		    }
	    }
    }
    
    public function getLastMessage(){
    	$msg = $this->messages;
	    if (count($msg) < 1) {
		    return null;
	    }
    	return array_shift($msg);
    }
    
    public function getMessagesCount(){
    	return count ($this->messages);
    }

    public function getUnreadMessages(UserBean $user){
    	$unread = array();
	    if ($this->getMessagesCount() > 0) {
			foreach ($this->messages as $msg){
				if ($user->getUniqID() == $msg->getFrom()->getUniqID()){
					if ($this->type == 'C') {
						if ($msg->getReadTimestamp() == "" || $msg->getReadTimestamp() == null){
							$unread[$msg->getID()] = $msg;
						}
						else if ($msg->getSendTimestamp() > $this->lastAccesses[$user->getUniqID()]) {
							$unread[$msg->getID()] = $msg;
						}
					}
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
    
    public function getTid(){
    	return $this->tid;
    }
    
    public function getUsers(){
    	return $this->users;
    }
    
    public function readAll($user){
	    $ts = null;
	    if ($this->type == 'C') {
	        $this->datasource->executeUpdate("UPDATE rb_com_messages SET read_timestamp = NOW() WHERE read_timestamp IS NULL AND tid = {$this->tid} AND sender != {$user->getUniqID()}");
	        foreach ($this->messages as $msg){
	            if (($msg->getFrom()->getUniqID() != $user->getUniqID()) && ($msg->getReadTimestamp() == "" || $msg->getReadTimestamp() == null)){
	                $ts = date("Y-m-d H:i:s");
	                $msg->setReadTimestamp($ts);
	            }
	        }
	    }
    	return $ts;
    }
    
    public function isRead($user){
	    if (count($this->messages) == 0) {
		    return null;
	    }
	    foreach ($this->messages as $msg){
		    if ($this->type == 'C') {
	            if (($msg->getFrom()->getUniqID() != $user->getUniqID()) && ($msg->getReadTimestamp() == "" || $msg->getReadTimestamp() == null)){
	                return false;
	            }
		    }
		    else {
			    if ($msg->getSendTimestamp() > $this->lastAccesses[$user->getUniqID()]){
				    return false;
			    }
		    }
    	}
    	return true;
    }

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	public function checkForUpdates(){
		$news = array();
		$last_message = 0;
		if (count($this->messages) > 0) {
			$last_message = $this->getLastMessage()->getID();
		}
		$new_messages = $this->datasource->executeQuery("SELECT * FROM rb_com_messages WHERE tid = {$this->getTid()} AND sender <> {$_SESSION['__user__']->getUniqID()} AND mid > {$last_message} ORDER BY send_timestamp ASC");
		if ($new_messages){
			foreach ($new_messages as $msg){
				$rb = \RBUtilities::getInstance($this->datasource->getSource());
				$sender = $rb->loadUserFromUniqID($msg['sender']);
				$message = new Message($msg['mid'], $this->getTid(), $sender, $this->tid, $this->datasource, $msg);
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

				$rdate = $rtime = "";
				if ($message->getReadTimestamp() != "") {
					list($rdate, $rtime) = explode(" ", $message->getReadTimestamp());
					if (date("Y-m-d") == $rdate){
						$rdate = "oggi alle ";
						$rtime = substr($rtime, 0, 5);
					}
					else {
						$rdate = "il ". format_date($rdate, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ";
						$rtime = substr($rtime, 0, 5);
					}
				}
				$target_name = $this->getTargetName($_SESSION['__user__']->getUniqID());
				array_unshift($news, array("type" => "new", "mid" => $msg['mid'], "t_t" => $this->type, "target_name" => $target_name, "send" => $data, "read" => $rdate.$rtime, "text" => $msg['text']));
			}
		}
		/*
		 * read timestamp
		 */
		$unread = $this->getUnreadMessages($_SESSION['__user__']);
		//echo count($unread);
		if ($unread != null){
			if ($this->type == 'C') {
				foreach ($unread as $k => $row){
					$reads = $this->datasource->executeCount("SELECT read_timestamp FROM rb_com_messages WHERE mid = {$row->getID()}");
					if ($reads != null && $reads != false){
						list($rdate, $rtime) = explode(" ", $reads);
						$date = "oggi alle ".substr($rtime, 0, 5);
						array_unshift($news, array("type" => "read", "mid" => $row->getID(), "read" => $date));
						$message = $this->getMessage($k);
						$message->setReadTimestamp($reads);
						$this->addMessage($message);
					}
				}
			}
		}
		if (count($news) > 0){
			return $news;
		}

		return null;
	}

	public function save(){
		$ownerID = null;
		if ($this->owner != null) {
			$ownerID = $this->owner->getUniqID();
		}
		$this->tid = $this->datasource->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name) VALUES (".field_null($ownerID, false).", NULL, {$this->systemThread}, ".field_null($this->name, true).")");
		foreach ($this->users as $user) {
			$this->datasource->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$this->tid}, {$user})");
		}
	}

	public function getOtherUser($uid){
		if (count($this->users) == 2){
			$rb = \RBUtilities::getInstance($this->datasource->getSource());
			$id = 0;
			foreach ($this->users as $u) {
				if ($u != $uid) {
					$id = $u;
				}
			}
			return $rb->loadUserFromUniqID($id);
		}
		return $this;
	}

	public function getTargetName($uid) {
		if ($this->type == 'G') {
			return $this->name;
		}
		$oth = $this->getOtherUser($uid);
		if ($oth instanceof Thread) {
			return $oth->getName();
		}
		else {
			return $oth->getFullName();
		}
	}

	public function updateLastAccess($uid) {
		$this->lastAccesses[$uid] = date("Y-m-d H:i:s");
		$this->datasource->executeUpdate("UPDATE rb_com_utenti_thread SET last_access = NOW() WHERE thread = ".$this->tid." AND utente = ".$uid);
	}
}
