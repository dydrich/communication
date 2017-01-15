<?php
/**
 * Created by PhpStorm.
 * User: riccardo
 * Date: 1/12/17
 * Time: 11:57 AM
 */

namespace eschool\comm;

require_once "../../lib/RBUtilities.php";
require_once "../../lib/RBTime.php";
require_once "../../lib/ScheduleModule.php";
require_once "../../lib/ScheduleModuleDay.php";
require_once "./lib/Notice.php";

class Assembly
{
	private $id;
	private $date;
	private $startTime;
	private $endTime;
	private $where;
	private $unions;
	private $timetablesChanges;
	private $datasource;

	/**
	 * Assembly constructor.
	 * @param $id
	 * @param $date
	 * @param $startTime
	 * @param $endTime
	 * @param $where
	 * @param $unions
	 */
	public function __construct($id, $date, $startTime, $endTime, $where, $unions, $db) {
		$this->id = $id;
		$this->date = $date;
		$this->setStartTime($startTime);
		$this->setEndTime($endTime);
		$this->where = $where;
		$this->unions = $unions;
		if($db instanceof \MySQLDataLoader) {
			$this->datasource = $db;
		}
		else {
			$this->datasource = new \MySQLDataLoader($db);
		}
		$this->loadTimetableChanges();
	}

	private function loadTimetableChanges() {
		$rb = \RBUtilities::getInstance($this->datasource);

		$sel_date = "SELECT DATE(inizio) FROM rb_com_assemblee WHERE id = {$this->id}";
		$date = $this->datasource->executeCount($sel_date);
		$day = date("N", strtotime($date));

		$sel = "SELECT id_classe, anno_corso, sezione, ordine_di_scuola 
				FROM rb_classi 
				ORDER BY ordine_di_scuola, anno_corso, sezione";

		$res = $this->datasource->executeQuery($sel);
		$variazioni = [];
		foreach ($res as $re) {
			$ordine = 'scuola primaria';
			if($re['ordine_di_scuola'] == 1) {
				$ordine = 'scuola secondaria';
			}
			$variazioni[$re['id_classe']] = ['classe' => $re['anno_corso'].$re['sezione'], 'id_record' => 0, 'ordine' => $ordine, 'ingresso' => '', 'uscita' => '', 'avviso' => ''];
			/*
			 * orario di ingresso e uscita normali
			 */
			$cls = $rb->loadClassFromClassID($re['id_classe']);
			$schedule = $cls->get_modulo_orario();
			$sc_day = $schedule->getDay($day);
			$start = $sc_day->getEnterTime();
			$end = $sc_day->getExitTime();
			$variazioni[$re['id_classe']]['ingresso'] = $start->toString(\RBTime::$RBTIME_SHORT);
			$variazioni[$re['id_classe']]['uscita'] = $end->toString(\RBTime::$RBTIME_SHORT);
			$variazioni[$re['id_classe']]['ingresso_previsto'] = $start->toString(\RBTime::$RBTIME_SHORT);
			$variazioni[$re['id_classe']]['uscita_prevista'] = $end->toString(\RBTime::$RBTIME_SHORT);
			/*
			 * variazioni registrate
			 */
			$val = null;
			$changed = $this->datasource->executeQuery("SELECT * FROM rb_com_modifiche_orarie_assemblea WHERE classe = {$re['id_classe']} AND id_assemblea = {$this->id}");
			if($changed != null && $changed) {
				foreach ($changed as $val) {
					$variazioni[$re['id_classe']]['id_record'] = $val['id'];
					if($val['ingresso'] != '' && $val['ingresso'] != null) {
						$variazioni[$re['id_classe']]['ingresso'] = substr($val['ingresso'], 0, 5);
					}
					if($val['uscita'] != '' && $val['uscita'] != null) {
						$variazioni[$re['id_classe']]['uscita'] = substr($val['uscita'], 0, 5);
					}
					if($val['id_avviso'] != '' && $val['id_avviso'] != null) {
						$variazioni[$re['id_classe']]['avviso'] = $val['id_avviso'];
					}
				}
			}
		}
		$this->timetablesChanges = $variazioni;
	}

	public function updateClassTimetableChange($cls, $field, $value) {
		return $this->insertClassTimetableChange($cls, $field, $value);
	}

	public function deleteClassTimetableChange($cls, $field) {
		$newValue = ($field == 'ingresso') ? $this->timetablesChanges[$cls]['ingresso_previsto'] : $this->timetablesChanges[$cls]['uscita_prevista'];
		$response = ['campo' => $field, 'valore' => $newValue];
		/*
		 * unica variazione per la classe o ne esiste un'altra?
		 */
		$checkField = ($field == 'uscita') ? "ingresso" : "uscita";
		$scheduledTime = ($field == 'uscita') ? "ingresso_previsto" : "uscita_prevista";
		$deleteRecord = false;
		if (substr($this->timetablesChanges[$cls][$checkField], 0, 5) == substr($this->timetablesChanges[$cls][$scheduledTime], 0, 5)) {
			$deleteRecord = true;
			$response['valore2'] = $this->timetablesChanges[$cls][$scheduledTime];
		}
		$response['delete'] = $deleteRecord;

		if($deleteRecord) {
			$this->datasource->executeUpdate("DELETE FROM rb_com_avvisi WHERE id = ".$this->timetablesChanges[$cls]['avviso']);
			$this->datasource->executeUpdate("DELETE FROM rb_com_modifiche_orarie_assemblea WHERE id_assemblea = {$this->id} AND classe = {$cls}");
			$this->timetablesChanges[$cls]['ingresso'] = $this->timetablesChanges[$cls]['ingresso_previsto'];
			$this->timetablesChanges[$cls]['uscita'] = $this->timetablesChanges[$cls]['uscita_prevista'];
			$this->timetablesChanges[$cls]['avviso'] = '';
		}
		else {
			$this->timetablesChanges[$cls][$field] = $newValue;
			$this->datasource->executeUpdate("UPDATE rb_com_modifiche_orarie_assemblea SET {$field} = '{$newValue}' WHERE id_assemblea = {$this->id} AND classe = {$cls}");
			$noticeID = $this->createNotice($checkField, $cls);
			$response['valore2'] = "";
			$response['notice'] = $noticeID;
		}
		return $response;
	}

	public function insertClassTimetableChange($cls, $field, $value) {
		/*
		 *
		*/
		$actualVal = $this->timetablesChanges[$cls][$field];
		$referVal = ($field == 'ingresso') ? "ingresso_previsto" : "uscita_prevista";
		if ($actualVal == $value) {
			/*
			 * update with no change
			 */
			return 0;
		}
		else if ($value == $referVal) {
			/*
			 * update value to default: delete
			 */
			$this->deleteClassTimetableChange($cls, $field);
			return 0;
		}
		$this->timetablesChanges[$cls][$field] = $value;
		$statement = "INSERT INTO rb_com_modifiche_orarie_assemblea (id_assemblea, classe, {$field}) 
					  VALUES ({$this->id}, $cls, '{$value}') 
					  ON DUPLICATE KEY UPDATE {$field} = '{$value}' ";
		$ret = $this->datasource->executeUpdate($statement);
		$this->createNotice($field, $cls);
		return $ret;
	}

	private function createNotice($f, $cls) {
		$rb = \RBUtilities::getInstance($this->datasource->getSource());
		$myClass = $rb->loadClassFromClassID($cls);
		setlocale(LC_TIME, "it_IT.UTF-8");
		$giorno_str = lcfirst(strftime("%A %d %B ", strtotime($this->date)));
		$order = 'scuola secondaria';
		if($myClass->getSchoolOrder() == 2) {
			$order = 'scuola primaria';
		}

		/*
		 * controllo su doppia variazione: ingresso e uscita
		 */
		if (($this->timetablesChanges[$cls]['ingresso'] != $this->timetablesChanges[$cls]['ingresso_previsto'])
			&& ($this->timetablesChanges[$cls]['uscita'] != $this->timetablesChanges[$cls]['uscita_prevista'])) {
			$f = 'all';
		}

		$st = $this->timetablesChanges[$cls][$f];
		if ($f == 'ingresso') {
			$text = "Avviso di ingresso ritardato per assemblea sindacale, classe ".$myClass->get_anno().$myClass->get_sezione()." della $order. Si comunica che il giorno " . $giorno_str .
				"la classe entrerà alle ore " . substr($st, 0, 5);
		}
		else if ($f == 'uscita') {
			$text = "Avviso di uscita anticipata per assemblea sindacale, classe ".$myClass->get_anno().$myClass->get_sezione()." della $order. Si comunica che il giorno " . $giorno_str .
				"la classe uscirà alle ore " . substr($st, 0, 5);
		}
		else {
			/*
			 * variati sia ingresso che uscita
			 */
			$st = $this->timetablesChanges[$cls]['ingresso'];
			$et = $this->timetablesChanges[$cls]['uscita'];
			$text = "Avviso di variazione oraria per assemblea sindacale, classe ".$myClass->get_anno().$myClass->get_sezione()." della $order. " .
					"Si comunica che il giorno " . $giorno_str . "la classe entrerà alle ore " . substr($st, 0, 5) . " e uscirà alle ore " . substr($et, 0, 5);
		}
		$noticeID = $this->timetablesChanges[$cls]['avviso'] == '' ? 0 : $this->timetablesChanges[$cls]['avviso'];
		$notice = new Notice(3, $this->date, $text, 8, $myClass->getSchoolOrder(), $cls, $noticeID, $this->datasource);
		$notice->delete();
		$notice = new Notice(3, $this->date, $text, 8, $myClass->getSchoolOrder(), $cls, 0, $this->datasource);
		$id = $notice->insert();
		$this->timetablesChanges[$cls]['avviso'] = $id;
		$this->datasource->executeUpdate("UPDATE rb_com_modifiche_orarie_assemblea SET id_avviso = $id WHERE id_assemblea = {$this->id} AND classe = {$cls}");
		return $id;
	}

	/**
	 * return notice ID for class
	 * @param integer $id
	 * @return integer
	 */
	public function getNotice($cls) {
		return $this->timetablesChanges[$cls]['avviso'];
	}

	/*
	 * insert a new record
	 */
	public function insert() {
		$start = $end = $this->getDate();
		$st = $this->getStartTime();
		$start.= " ".$st->toString(\RBTime::$RBTIME_LONG);
		$et = $this->getEndTime();
		$end .= " ".$et->toString(\RBTime::$RBTIME_LONG);
		$statement = "INSERT INTO rb_com_assemblee (sigle, inizio, fine, luogo) VALUES ('{$this->unions}', '{$start}', '{$end}', '{$this->where}')";
		$this->id = $this->datasource->executeUpdate($statement);

		return $this->id;
	}

	/*
	 * update a record
	 */
	public function update() {
		$start = $end = $this->getDate();
		$st = $this->getStartTime();
		$start.= " ".$st->toString(\RBTime::$RBTIME_LONG);
		$et = $this->getEndTime();
		$end .= " ".$et->toString(\RBTime::$RBTIME_LONG);
		$statement = "UPDATE rb_com_assemblee SET sigle = '{$this->unions}', inizio = '{$start}', fine = '{$end}', luogo = '{$this->where}' WHERE id = {$this->id}";
		$this->datasource->executeUpdate($statement);
	}

	/*
	 * delete a record
	 */
	public function delete() {
		/*
		 * delete all timetable changes
		 */
		$this->datasource->executeUpdate("DELETE FROM rb_com_modifiche_orarie_assemblea WHERE id_assemblea = {$this->id}");
		/*
		 * delete all notices
		 */
		$tc = $this->timetablesChanges;
		foreach ($tc as $k => $item) {
			if ($item['avviso'] != "" && $item['avviso'] != null) {
				$this->datasource->executeUpdate("DELETE FROM rb_com_avvisi WHERE id = {$item['avviso']}");
			}
		}
		$this->datasource->executeUpdate("DELETE FROM rb_com_assemblee WHERE id = {$this->id}");
	}

	public function toString() {
		$st = $this->getStartTime();
		$str = "Assemblea ".$this->unions." prevista per il giorno ".$this->date." alle ore ".$st->toString(\RBTime::$RBTIME_SHORT);
		return $str;
	}

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $id
	 * @return Assembly
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param mixed $date
	 * @return Assembly
	 */
	public function setDate($date) {
		$this->date = $date;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getStartTime() {
		return $this->startTime;
	}

	/**
	 * @param mixed $startTime
	 * @return Assembly
	 */
	public function setStartTime($startTime) {
		if ($startTime instanceof \RBTime) {
			$this->startTime = $startTime;
		}
		else {
			list($h, $m, $s) = explode(":", $startTime);
			$rt = new \RBTime($h, $m, $s);
			$this->startTime = $rt;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEndTime() {
		return $this->endTime;
	}

	/**
	 * @param mixed $endTime
	 * @return Assembly
	 */
	public function setEndTime($endTime) {
		if ($endTime instanceof \RBTime) {
			$this->endTime = $endTime;
		}
		else {
			list($h, $m, $s) = explode(":", $endTime);
			$rt = new \RBTime($h, $m, $s);
			$this->endTime = $rt;
		}

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getWhere() {
		return $this->where;
	}

	/**
	 * @param mixed $where
	 *
	 */
	public function setWhere($where) {
		$this->where = $where;
	}

	/**
	 * @return mixed
	 */
	public function getUnions() {
		return $this->unions;
	}

	/**
	 * @param mixed $unions
	 * @return Assembly
	 */
	public function setUnions($unions) {
		$this->unions = $unions;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTimetablesChanges() {
		return $this->timetablesChanges;
	}

	/**
	 * @param mixed $timetablesChanges
	 * @return Assembly
	 */
	public function setTimetablesChanges($timetablesChanges) {
		$this->timetablesChanges = $timetablesChanges;
		return $this;
	}

	/**
	 * @return \MySQLDataLoader
	 */
	public function getDatasource() {
		return $this->datasource;
	}

	/**
	 * @param \MySQLDataLoader $datasource
	 */
	public function setDatasource($datasource) {
		$this->datasource = $datasource;
	}
}