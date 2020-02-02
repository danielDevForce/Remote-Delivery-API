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
    //include(dirname(__FILE__)."/Includes/Remote-Delivery-API.php");
    //include(dirname(__FILE__).'/Include/vendor/autoload.php');
    //require __DIR__ . '/Include/vendor/autoload.php';
    use Automattic\WooCommerce\Client;

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
        //WC()->get();
        //$order = wc_get_order( $order_id );
        //print_r($order);
        //echo gettype($myWC);
        
        //$cart_content = WC()->cart->get_cart();
       //echo $cart_content;
        //echo $url;
        $woocommerce = new Client(
            $url, // Your store URL
            $my_settings_page->get_api_user(), // Your consumer key
            $my_settings_page->get_api_pass(), // Your consumer secret
            [
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3' // WooCommerce WP REST API version
            ]
        );
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
    
?>