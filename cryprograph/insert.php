<?php
$path = preg_replace('/wp-content.*$/','',__DIR__);
require_once($path."wp-load.php");

global $wpdb;

$amount=$_POST['amount'];
$curr1=$_POST['curr1'];
$curr2=$_POST['curr2'];
$resultFull=$_POST['resultFull'];

$table_name = $wpdb->prefix . 'history_rate11';
        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time( 'mysql' ),
                'amount' => $amount,
                'curr1' => $curr1,
                'curr2' => $curr2,
                'resultFull' => $resultFull,
            )
        );