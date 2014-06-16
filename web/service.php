<?php

require_once '../config.php';
require_once 'expense-model.php';
require_once 'file-model.php';

session_start ();

class ExpenseService {
	
	private static $db;
	
	public static function dispatch($method) {
		
		global $config;
		
		self::$db = new mysqli ( $config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name'] );
	
		if (self::$db->errno) {
			print 'Connection error: ' . self::$db->error;
			exit();
		}

		self::$method();
		
		self::$db->close();
	}
	
	private static function GET() {
		$expense_model = new ExpenseModel(self::$db);
		if (isset ( $_GET ['recordId'] )) {
			
			$record = $expense_model->get($_GET['recordId']);
			
			if ( $record === FALSE ) {
				print 'Error: ' . $expense_model->getError();
				return FALSE;
			}
			print json_encode($record);
		} else {
			$list = $expense_model->index();
			
			if ( $list === FALSE ) {
				print 'Error: ' . $expense_model->getError();
				return FALSE;
			}
			print json_encode ( $list );
		}
		return TRUE;
	}
	
	private static function POST() {
		$expense_model = new ExpenseModel(self::$db);
		$input = file_get_contents ( 'php://input' );
		$record = json_decode ( $input, TRUE );
		$id = $expense_model->put($record);
		if ( $id === FALSE ) {
			print 'Error: ' . $expense_model->getError();
			return FALSE;
		}
		print json_encode(Array('id' => $id, 'status' => 'OK save'));
		return TRUE;
	}
	
	private static function DELETE() {
		if ( isset ( $_GET['recordId']) ) {		
			$expense_model = new ExpenseModel(self::$db);
			$file_model = new FileModel(self::$db);
			if ( $expense_model->delete($_GET['recordId']) === FALSE ) {
				print 'Error: ' . $expense_model->getError();
				return FALSE;
			}
			if ( $file_model->delete($_GET['recordId']) === FALSE ) {
				print 'Error: ' . $file_model->getError();
				return FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}
}

ExpenseService::dispatch($_SERVER ['REQUEST_METHOD']);
