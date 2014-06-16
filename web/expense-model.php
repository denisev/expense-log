<?php
class ExpenseModel {
    private $db;
    private $error;
    public function __construct($db) {
        $this->db = $db;
    }
    public function index() {
        $r = $this->db->query ( '
                SELECT 
                    expense.*, file.size AS img_size, file.type AS img_type 
                FROM 
                    expense 
                LEFT JOIN 
                    file ON expense.id = file.id 
                ORDER BY 
                    tr_date DESC' );
        if ($r === FALSE) {
            $this->error = $this->db->error;
            return FALSE;
        }
        return $r->fetch_all ( MYSQLI_ASSOC );
    }
    public function get($id) {
        $q = $this->db->prepare ( '
                SELECT 
                    expense.*, file.size AS img_size, file.type AS img_type 
                FROM 
                    expense 
                LEFT JOIN 
                    file ON expense.id = file.id 
                WHERE 
                    expense.id=?' );
        if ($q === FALSE) {
            $this->error = $this->db->error;
            return FALSE;
        }
        $q->bind_param ( 'i', $id );
        if ($q->execute () === FALSE) {
            $this->error = $this->db->error;
            return FALSE;
        }
        $r = $q->get_result ();
        $record = $r->fetch_assoc ();
        
        return $record;
    }
    public function put($record) {
        if (isset ( $record ['id'] )) {
            // save existing
            $q = $this->db->prepare ( 'UPDATE expense SET recipient=?, note=?, amount=?, tr_date=? WHERE id=?' );
            if ($q === FALSE) {
                $this->error = $this->db->error;
                return FALSE;
            }
            $q->bind_param ( 'ssdsi', $record ['recipient'], $record ['note'], $record ['amount'], $record ['date'], $record ['id'] );
            if ($q->execute () !== TRUE) {
                $this->error = $this->db->error;
                return FALSE;
            }
            return $record ['id'];
        }
        else {
            // create new
            $q = $this->db->prepare ( 'INSERT INTO expense (recipient,note,amount,tr_date) VALUES (?,?,?,?)' );
            if ($q === FALSE) {
                $this->error = $this->db->error;
                return FALSE;
            }
            $q->bind_param ( 'ssds', $record ['recipient'], $record ['note'], $record ['amount'], $record ['date'] );
            if ($q->execute () !== TRUE) {
                $this->error = $this->db->error;
                return FALSE;
            }
            return $this->db->insert_id;
        }
    }
    public function delete($id) {
        $q = $this->db->prepare ( 'DELETE FROM expense WHERE id=?' );
        if ($q === FALSE) {
            $this->error = $this->db->error;
            return FALSE;
        }
        $q->bind_param ( 'i', $id );
        if ($q->execute () !== TRUE) {
            $this->error = $this->db->error;
            return FALSE;
        }
        $q->close ();
        
        return TRUE;
    }
    public function getError() {
        if (! empty ( $this->error )) {
            return $this->error;
        }
        else {
            return '';
        }
    }
}
