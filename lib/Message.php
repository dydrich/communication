<?php

/**
 * 
 * User: cravenroad17@gmail.com
 * Date: 18/07/13
 * Time: 23:00
 * 
 */

class Message {

	/**
	 * 
	 * @var Thread
	 * thread the message belong to
	 */
    private $thread;

    /**
     * 
     * @var integer
     * message id
     */
    private $ID;

    /**
     * 
     * @var UserBean
     * sender
     */
    private $from;
    /**
     * target
     * @var UserBean
     */
    private $to;

    /**
     * message'stext
     * @var string
     */
    private $text;

    /**
     * date and time of sending
     * @var timestamp
     */
    private $send_timestamp;
	/**
	 * date and time of reading
	 * @var timestamp
	 */
    private $read_timestamp;
    
    /**
     * source of data
     * @var MYSQLDataLoader
     */
    private $datasource;
	
	/**
	 * 
	 * @param integer $tid
	 * @param UserBean $sender
	 * @param UserBean $target
	 * @param DataLoader $ds
	 */
    public function __construct($mid, $tid, UserBean $sender, UserBean $target, DataLoader $ds, $data){
		$this->thread = $tid;
		$this->from = $sender;
		$this->to = $target;
		$this->datasource = $ds;
		$this->ID = $mid;
		if ($data != null){
			$this->send_timestamp = $data['send_timestamp'];
			if ($data['read_timestamp'] != ""){
				$this->read_timestamp = $data['read_timestamp'];
			}
			else {
				$this->read_timestamp = null;
			}
			$this->text = utf8_encode($data['text']);
		}
    }

    public function getThread(){
		return $this->thread;
    }

    public function getID(){
		return $this->ID;
    }

    public function setID($id){
		$this->ID = $id;
    }

    public function getTo(){
		return $this->to;
    }

    public function getFrom(){
		return $this->from;
    }

    public function getText(){
		return $this->text;
    }

    public function setText($txt){
		$this->text = $txt;
    }

    public function setSendTimestamp($tm){
		$this->send_timestamp = $tm;
    }

    public function getSendTimestamp(){
		return $this->send_timestamp;
    }

    public function setReadTimestamp($tm){
		$this->read_timestamp = $tm;
    }

    public function getReadTimestamp(){
		return $this->read_timestamp;
    }

    public function isRead(){
        return $this->read_timestamp != null;
    }

    public function send(){
    	$text = $this->text;
    	$this->ID = $this->datasource->executeUpdate("INSERT INTO rb_com_messages (tid, sender, target, text) VALUES ({$this->getThread()}, {$this->from->getUid()}, {$this->to->getUid()}, '{$text}')");
    	$this->send_timestamp = date("Y-m-d H:i:s");
    }
    
    public function read(){
    	$this->read_timestamp = date("Y-m-d H:i:s");
    	$this->datasource->executeUpdate("UPDATE rb_com_messages SET read_timestamp = NOW() WHERE tid = {$this->getThread()} AND mid = {$this->getID()}");
    }
    
    public function delete(){
    	if ($this->isRead()){
    		return false;
    	}
    }

}