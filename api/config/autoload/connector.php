<?php
require_once 'Zend/Config.php';
require_once 'Zend/Db.php';

$db = new (array (
    'host' => '127.0.0.1',
    'username' => 'webuser',
    'password' => 'xxxxxxxx',
    'dbname' => 'teste'
));

return $db;


class Conector{

    public function query($sql){

        $params ['host']      = '127.0.0.1';
        $params ['dbname']    = 'mydb';
        $params ['username']  = 'root';
        $params ['password']  = '';
        $database ['adapter'] = 'Mysqli';
        $database ['params']  = $params;
        $data ['database']    = $database;

        $config = new Zend_Config($data);

        $db = Zend_Db::factory($config->database);



    }

}