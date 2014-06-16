<?php

class FileModel {
	private $db;
	
	private $error;
	
	public function __construct($db) {
		$this->db = $db;
	}
	
	public function get($id) {
		$q = $this->db->prepare ( 'SELECT * FROM file WHERE id=?' );
		$q->bind_param ( 'i', $id );
		if ($q->execute () !== TRUE) {
			$this->error = $this->db->error;
			return FALSE;
		}
		$r = $q->get_result ();
		$record = $r->fetch_assoc ();
		$q->close();
		return $record;
	}
	
	public function put($record, $file_bits) {
		$q = $this->db->prepare ( 'DELETE FROM file WHERE id=?' );
		$q->bind_param( 'i', $record['id'] );
		$q->execute();
		$q = $this->db->prepare ( 'INSERT INTO file SET id=?,type=?,size=?,bits=?' );
		$q->bind_param( 'isis', $record[id], $record['type'], $record['size'], $file_bits );
		if ( $q->execute() !== TRUE ) {
			$this->error = $this->db->error;
			return FALSE;
		}
		$q->close();
		return TRUE;
	}
	
	public function delete($id) {
		$q = $this->db->prepare('DELETE FROM file WHERE id=?');
		$q->bind_param('i', $id);
		if ( $q->execute() !== TRUE ) {
			$this->error = $this->db->error;
			return FALSE;
		}
		$q->close();
		return TRUE;
	}
}
