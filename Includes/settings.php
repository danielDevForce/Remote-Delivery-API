<?php
/*
Plugin Name: test
Plugin URI: devforce.co.il
Description: setting page test
Author: Devforce
Version: 1.0.0
*/

class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Remote Delivery API Settings', 
            'manage_options', 
            'my-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'my_option_name' );
        ?>
        <div class="wrap">
            <h1>API settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'My Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );  

        add_settings_field(
            'id_number', // ID
            'Share_Token', // Title 
            array( $this, 'id_number_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'title', 
            'api_user', 
            array( $this, 'title_callback' ), 
            'my-setting-admin', 
            'setting_section_id'
        ); 
        add_settings_field(
            'api_pass',
            'api_password',
            array( $this, 'api_pass_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );    
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        //return isset( $input ) ? true : false;

        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );
        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );
        if(isset( $input['api_pass']))
            $new_input['api_pass'] = sanitize_text_field( $input['api_pass'] );


        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    public function api_pass_callback()
    {
        printf(
            '<input type="password" id="api_pass" name="my_option_name[api_pass]" value="%s" />',
            isset( $this->options['api_pass'] ) ? esc_attr( $this->options['api_pass']) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function id_number_callback()
    {
        printf(
            '<input type="text" id="id_number" name="my_option_name[id_number]" value="%s" />',
            isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function title_callback()
    {
        printf(
            '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }

    
    function get_share_token() {
        $options = get_option('my_option_name');
        if (!isset($options['id_number'])) {
          return false;
        }
        return $options['id_number'];
    }

    function get_api_user() {
        $options = get_option('my_option_name');
        if (!isset($options['title'])) {
          return false;
        }
        return $options['title'];
    }

    function get_api_pass() {
        $options = get_option('my_option_name');
        if (!isset($options['api_pass'])) {
          return false;
        }
        return $options['api_pass'];
    }


}

// if( is_admin() )
// {

//     $my_settings_page = new MySettingsPage();
//     //echo 'token' . $my_settings_page->get_share_token();
//     //echo 'user' . $my_settings_page->get_api_user();
//     //echo 'password' . $my_settings_page->get_api_pass();
// }

?>
