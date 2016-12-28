<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 12/26/16
 * Time: 2:39 PM
 */

namespace eschool\comm;


class Notice
{
	private $type;
	private $date;
	private $text;
	private $groups;
	private $schoolLevel;
	private $classes = [];
	private $id;

	private $datasource;

	public static $STRIKE_NOTICE = 2;

	/**
	 * Notice constructor.
	 * @param $type
	 * @param $date
	 * @param $text
	 * @param $groups
	 * @param $schoolLevel
	 * @param $classes
	 * @param $id
	 */
	public function __construct($type, $date, $text, $groups, $schoolLevel = null, $classes = null, $id, \MySQLDataLoader $dl) {
		$this->type = $type;
		$this->date = new \DateTime($date);
		$this->groups = $groups;
		$this->setSchoolLevel($schoolLevel);
		$this->setClasses($classes);
		$this->id = $id;
		$this->datasource = $dl;
		$this->setText($text);
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return \DateTime
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param \DateTime $date
	 */
	public function setDate($date) {
		$this->date = $date;
	}

	/**
	 * @return mixed
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * @param mixed $text
	 */
	public function setText($text) {
		if ($this->type != 2) {
			$this->text = $text;
		}
		else {
			$formatted_text = "";
			setlocale(LC_TIME, "it_IT.utf8");
			$str_date = strftime("%A %e %B", $this->date->getTimestamp());
			$t = explode("-----", $text);
			if (count($t) == 2) {
				$formatted_text = $t[0].$str_date.$t[1];
			}
			$this->text = $formatted_text;
		}
	}

	/**
	 * @return mixed
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 * @param mixed $groups
	 */
	public function setGroups($groups) {
		$this->groups = $groups;
	}

	/**
	 * @return null
	 */
	public function getSchoolLevel() {
		return $this->schoolLevel;
	}

	/**
	 * @param null $schoolLevel
	 */
	public function setSchoolLevel($schoolLevel) {
		if ($schoolLevel == 0) {
			$schoolLevel = null;
		}
		$this->schoolLevel = $schoolLevel;
	}

	/**
	 * @return null
	 */
	public function getClasses() {
		return $this->classes;
	}

	/**
	 * @param null $classes
	 */
	public function setClasses($classes) {
		if($classes != "" && $classes != null) {
			$this->classes = explode(",", $classes);
		}
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	public function insert() {
		$d = $this->date->format("Y-m-d");
		$cls = "";
		if(count($this->classes) > 0) {
			$cls = implode(",", $this->classes);
		}
		$statement = "INSERT INTO rb_com_avvisi (data_scadenza, data_inserimento, testo, gruppi, ordine_di_scuola, classe, tipo) 
					  VALUES ('{$d}', NOW(), '{$this->text}', {$this->groups}, ".field_null($this->schoolLevel, false).", ".field_null($cls, true).", {$this->type})";
		$this->id = $this->datasource->executeUpdate($statement);
		return $this->id;
	}

	public function update() {
		$d = $this->date->format("Y-m-d");
		$cls = "";
		if(count($this->classes) > 0) {
			$cls = implode(",", $this->classes);
		}
		$statement = "UPDATE rb_com_avvisi 
					  SET data_scadenza = '{$d}', 
					  testo = '{$this->text}', 
					  ordine_di_scuola = ".field_null($this->schoolLevel, false).", 
					  classe = ".field_null($cls, true)." 
					  WHERE id = ".$this->id;
		$this->datasource->executeUpdate($statement);
	}

	public function delete() {
		$statement = "DELETE FROM rb_com_avvisi WHERE id = ".$this->id;
		$this->datasource->executeUpdate($statement);
	}

}