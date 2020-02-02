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

   $my_settings_page = new MySettingsPage();
    if(!empty($_GET['qwe']))
    {

        //echo 'qwe';
        //echo "<script> consolo.log('qwe') </script>";
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url = substr($url, 0, strpos($url, "?"));
        global $wpdb,$woocommerce;
        $results = $wpdb->get_results( "SELECT order_id FROM {$wpdb->prefix}creditCards ");
        print_r( $results);
       //echo gettype($wpdb);
        $order_id = $results[0]->order_id;
        $myWC = WC();
        woo_api();
        // Now you have access to (see above)...
 
        //echo $order->get_id();
        //WC()->get();
        //$order = wc_get_order( $order_id );
        //print_r($order);
        //echo gettype($myWC);
        
        //$cart_content = WC()->cart->get_cart();
       //echo $cart_content;
        //echo $url;
        // $woocommerce = new Client(
        //     $url, // Your store URL
        //     $my_settings_page->get_api_user(), // Your consumer key
        //     $my_settings_page->get_api_pass(), // Your consumer secret
        //     [
        //         'wp_api' => true, // Enable the WP REST API integration
        //         'version' => 'wc/v3' // WooCommerce WP REST API version
        //     ]
        // );
        //$results = $woocommerce->get('orders');
        //print_r($result);
        // $myObj= create_Remote_order($results, "1234");
        // $myObj->shareToken = $my_settings_page->get_share_token();
        // $myJSON = json_encode($myObj);
        // echo $myJSON;
        //$myfile = fopen("newfilew.txt", "w") or die("Unable to open file!");
        //fwrite($myfile, $myJSON);
        //echo 'token' . 
        //echo 'user' . 
        //echo 'password' . 
    // $my_settings_page->test();



    }
    function woo_api(){
        $username = 'ck_d2bc995ba20f60ebaf241bac002e2699bb90bff7';
        $password = 'cs_636f8e9f01a63eb072b768c73f92256d3f8ba56d';
        $host = 'https://freshit-order.ussl.blog/wp-json/wc/v3/orders/';
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        print_r($return);
        curl_close($ch);
    }
    
?>