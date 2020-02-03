<?php
/*
Plugin Name: Remote Delivery APII
Plugin URI: devforce.co.il
Description: Remote Delivery API
Author: Devforce
Version: 1.0.0
*/   
    include(dirname(__FILE__)."/Includes/settings.php");
    include(dirname(__FILE__)."/Includes/payment.php");
    include_once WP_PLUGIN_DIR .'/woocommerce/woocommerce.php';
    include(dirname(__FILE__)."/Includes/classes.php");


   $my_settings_page = new MySettingsPage();
    if(!empty($_GET['qwe']))
    {

        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url = substr($url, 0, strpos($url, "?"));
        global $wpdb,$woocommerce;
        $username =  $my_settings_page->get_api_user();
        $password = $my_settings_page->get_api_pass();
        $order = woo_api($username, $password, $url);

        $arr = json_decode($order);
        //print_r($arr);
        
        $remoteOrder= create_Remote_order($arr, $my_settings_page->get_share_token());
        array_push($remoteOrder->payment, get_creditCard_fromdb($wpdb,$remoteOrder->id)); 
        $myJSON = json_encode($remoteOrder);
        $result = preg_replace('/,\s*"[^"]+":null|"[^"]+":null,?/', '', $myJSON);
        
        echo $result;
    }
    /**
     * gets the order from the woocommerce API
     * 
     * @param $username -> username for the rest api, entered in the setting page.
     * @param $password -> password for the rest api, entered in the setting page.
     * @param $host -> the url of the site
     * @param $order_id -> optinal, gets an order with specific order id from the rest api
     */
    function woo_api($username, $password, $host, $order_id=""){
        $wp_json = 'wp-json/wc/v3/orders/';
        $host= $host . $wp_json . $order_id;
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;', 'charset=utf-8'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        //print_r($return);
        curl_close($ch);
        return $return;
    }
    /**
     * gets credit card info from the database
     * 
     * @param $wpdb -> global wordpress database object
     * @param $id -> the order id to search the credit card info by
     */
    function get_creditCard_fromdb($wpdb,$id){
        $results = $wpdb->get_results( "SELECT * 
                                        FROM {$wpdb->prefix}creditCards  
                                        WHERE order_id=$id");
        $payment = create_payment_obj($results[0]);
        return $payment;
    }
    
?>