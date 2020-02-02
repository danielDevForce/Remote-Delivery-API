<?php
/*
Plugin Name: paymenttest
Plugin URI: devforce.co.il
Description: payment page test
Author: Devforce
Version: 1.0.0
*/

/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter( 'woocommerce_payment_gateways', 'payment_add_gateway_class' );
function payment_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_Custom_Payment_Gateway'; // your class name is here
	return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'init_custom_gateway_class' );
function init_custom_gateway_class() {
    class WC_Custom_Payment_Gateway extends WC_Payment_Gateway {
 
        /**
         * Class constructor, more about it in Step 3
         */
        public function __construct() {
            $this->id = "custom"; // payment gateway plugin ID
            $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Custom Gateway';
            $this->method_description = 'Pay with credit card'; // will be displayed on the options page
         
            // gateways can support subscriptions, refunds, saved payment methods,
            // but in this tutorial we begin with simple payments
            $this->supports = array(
                'products'
            );
         
            // Method with all the options fields
            $this->init_form_fields();
         
            // Load the settings.
            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            $this->testmode = 'yes' === $this->get_option( 'testmode' );
            $this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
            $this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );
         
            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
         
            // We need custom JavaScript to obtain a token
            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
         
            // You can also register a webhook here
            // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
         }

       /**
         * Plugin options, we deal with it in Step 3 too
         */
        public function init_form_fields(){

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Custom Gateway',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'Credit Card',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay with your credit card via our super-cool payment gateway.',
                ),
                'testmode' => array(
                    'title'       => 'Test mode',
                    'label'       => 'Enable Test Mode',
                    'type'        => 'checkbox',
                    'description' => 'Place the payment gateway in test mode using test API keys.',
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
                'test_publishable_key' => array(
                    'title'       => 'Test Publishable Key',
                    'type'        => 'text'
                ),
                'test_private_key' => array(
                    'title'       => 'Test Private Key',
                    'type'        => 'password',
                ),
                'publishable_key' => array(
                    'title'       => 'Live Publishable Key',
                    'type'        => 'text'
                ),
                'private_key' => array(
                    'title'       => 'Live Private Key',
                    'type'        => 'password'
                )
            );

        }

       /**
        * You will need it if you want your custom credit card form, Step 4 is about it
        */
       public function payment_fields() {

            // ok, let's display some description before the payment form
            if ( $this->description ) {
                // you can instructions for test mode, I mean test card numbers etc.
                if ( $this->testmode ) {
                    $this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#" target="_blank" rel="noopener noreferrer">documentation</a>.';
                    $this->description  = trim( $this->description );
                }
                // display the description with <p> tags etc.
                echo wpautop( wp_kses_post( $this->description ) );
            }
        
            // I will echo() the form, but you can close PHP tags and print it directly in HTML
            echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" method="post" style="background:transparent;">';
        
            // Add this action hook if you want your custom payment gateway to support it
            do_action( 'woocommerce_credit_card_form_start', $this->id );
        
            // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
            echo '
            <div class="form-row form-row-wide" style="direction: ltr;"><label>מספר כרטיס <span class="required">*</span></label>
                <input id="custom_ccNo"  name="ccNumber" type="text" autocomplete="off" placeholder="**** **** **** ****" method="post">
                </div>
                <div class="form-row form-row-first">
                    <label>תאריך תפוגה<span class="required">*</span></label>
                    <input id="custom_expdateM" name="ccM" type="text" style="display: inline; width: 50px;" autocomplete="off" placeholder="MM">
                    <input id="custom_expdateY" name="ccY" type="text" style="display: inline; width: 50px;" autocomplete="off" placeholder="YY">
                </div>
                <div class="form-row form-row-last">
                    <label>CVC <span class="required">*</span></label>
                    <input id="custom_cvv" name="Cvv" style="display: inline; width: 50px;"  type="text" autocomplete="off" placeholder="CVC">
                </div>
                <div class="clear"></div>
                ';
            do_action( 'woocommerce_credit_card_form_end', $this->id );
        
            echo '<div class="clear"></div></fieldset>';

       }

       /*
        * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
        */
        public function payment_scripts() {
                 // we need JavaScript to process a token only on cart/checkout pages, right?
                if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
                    return;
                }
            
                // if our payment gateway is disabled, we do not have to enqueue JS too
                if ( 'no' === $this->enabled ) {
                    return;
                }
            
                // no reason to enqueue JavaScript if API keys are not set
                // if ( empty( $this->private_key ) || empty( $this->publishable_key ) ) {
                //     return;
                // }
            
                // do not work with card detailes without SSL unless your website is in a test mode
                if ( ! $this->testmode && ! is_ssl() ) {
                    return;
                }
                
        }

       /*
         * Fields validation
        */
       public function validate_fields() {
        
        print_r($_POST);
        $cc_num = $_POST[ 'ccNumber' ];
        $cc_num = str_replace(' ', '', $cc_num);
        $month = $_POST['ccM'];
        $year = $_POST['ccY'];
        $cvv = $_POST['Cvv'];
        $Visapattern = "(4\d{12}(?:\d{3})?)";//Visa
        $MCpattern = "/^([51|52|53|54|55]{2})([0-9]{14})$/";//Mastercard
        if(!preg_match($Visapattern, $cc_num) && !preg_match($MCpattern, $cc_num)){
            wc_add_notice(  'CreditCard number is not valid', 'error' );
            return false;
        }
        if(!preg_match("/^[0-9]{3}$/", $cvv)){
            wc_add_notice( 'CVV number is not valid', 'error' );
            return false;
        }
        if($month < 1 || $month>12){
            wc_add_notice( 'Month is not valid', 'error' );
            return false;
        }
        if($year < 20 || $year > 29){
            wc_add_notice( 'year is not valid', 'error' );
            return false;
        }

        
        return true;
       }

       /*
        * We're processing the payments here, everything about it is in Step 5
        */
       public function process_payment( $order_id ) {

            global $woocommerce;
            global $wpdb;
            $cc_num = $_POST[ 'ccNumber' ];
            $cc_num = str_replace(' ', '', $cc_num);
            $month = $_POST['ccM'];
            $year = $_POST['ccY'];
            $cvv = $_POST['Cvv'];

            $wpdb->show_errors();
            $charset_collate = $wpdb->get_charset_collate();

            $table_name = $wpdb->base_prefix.'creditCards';
            $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

            if (  !$wpdb->get_var( $query ) == $table_name ) {
                $sql = "CREATE TABLE `{$wpdb->base_prefix}creditCards` (
                    order_id int NOT NULL,
                    order_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    creditcard_number varchar(255) NOT NULL,
                    creditcard_month varchar(5) NOT NULL,
                    creditcard_year varchar(5) NULL,
                    creditcard_CVV varchar(55) NOT NULL,
                    PRIMARY KEY  (order_id)
                    ) $charset_collate;";
        
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }

            strlen($month) < 2 ? $month = '0' . $month : "";
            echo $month;
            $wpdb->insert( 
                $table_name, 
                array( 
                    'order_id' => $order_id, 
                    'order_date' => current_time( 'mysql' ), 
                    'creditcard_number' => $cc_num, 
                    'creditcard_month' => $month,
                    'creditcard_year' => $year,
                    'creditcard_CVV' => $cvv,
                ) 
            );
           
            
            //write_to_db();
            //$email = $wpdb->query("SELECT user_email FROM nxq_users");
            
           
            /*
             
            // we need it to get any order detailes
            $order = wc_get_order( $order_id );
            //Remove cart
            WC()->cart->empty_cart();
                    
            //Return thankyou redirect
            return array(
                'result'    => 'success',
                'redirect'  => $this->get_return_url( $order )
            );
                */

        }

       /*
        * In case you need a webhook, like PayPal IPN etc
        */
       public function webhook() {

        return;

        }
    }

}