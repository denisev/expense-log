<?php
require_once '../config.php';
require_once 'file-model.php';

session_start();
class FileService {
    private static $db;
    public static function dispatch($method) {
        global $config;
        
        self::$db = new mysqli( $config ['db_host'], $config ['db_user'], $config ['db_pass'], $config ['db_name'] );
        
        if (self::$db->errno) {
            print 'Connection error: ' . self::$db->error;
            exit();
        }
        
        self::$method();
        
        self::$db->close();
    }
    private static function GET() {
        if (isset( $_GET ['fileId'] )) {
            $file_model = new FileModel( self::$db );
            
            $record = $file_model->get( $_GET ['fileId'] );
            
            if ($record === FALSE) {
                print 'Error: ' . $file_model->getError();
                return FALSE;
            }
            
            header( 'Content-Type: ' . $record ['type'] );
            header( 'Content-Length: ' . $record ['size'] );
            
            print $record ['bits'];
            return TRUE;
        }
        return FALSE;
    }
    private static function POST() {
        if (isset( $_POST ['fileId'] )) {
            // handle image file upload
            $file_model = new FileModel( self::$db );
            
            $put_result = $file_model->put( Array (
                    'id' => $_POST ['fileId'],
                    'type' => $_FILES ['file'] ['type'],
                    'size' => $_FILES ['file'] ['size'] 
            ), file_get_contents( $_FILES ['file'] ['tmp_name'] ) );
            
            if ($put_result === FALSE) {
                print 'Error: ' . $file_model->getError();
                return FALSE;
            }
            return TRUE;
        }
    }
}

FileService::dispatch( $_SERVER ['REQUEST_METHOD'] );
