<?php
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
            'Products', 
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
            <h1>Product Settings</h1>
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
            'API Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );  

        // add_settings_field(
        //     'id_number', // ID
        //     'ID Number', // Title 
        //     array( $this, 'id_number_callback' ), // Callback
        //     'my-setting-admin', // Page
        //     'setting_section_id' // Section           
        // );      

        // add_settings_field(
        //     'title', 
        //     'Title', 
        //     array( $this, 'title_callback' ), 
        //     'my-setting-admin', 
        //     'setting_section_id'
        // );  

        add_settings_field(
            'app_id', 
            'APP ID', 
            array( $this, 'app_id_callback' ), 
            'my-setting-admin', 
            'setting_section_id'
        );              

        add_settings_field(
            'secret_key', 
            'Secret Key', 
            array( $this, 'secret_key_callback' ), 
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
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        if( isset( $input['app_id'] ) )
            $new_input['app_id'] = sanitize_text_field( $input['app_id'] );

		if( isset( $input['secret_key'] ) )
            $new_input['secret_key'] = sanitize_text_field( $input['secret_key'] );


        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
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

    /** 
     * Get the settings option array and print one of its values
     */
    public function app_id_callback()
    {
        printf(
            '<input type="text" id="app_id" name="my_option_name[app_id]" value="%s" />',
            isset( $this->options['app_id'] ) ? esc_attr( $this->options['app_id']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function secret_key_callback()
    {
        printf(
            '<input type="text" id="secret_key" name="my_option_name[secret_key]" value="%s" />',
            isset( $this->options['secret_key'] ) ? esc_attr( $this->options['secret_key']) : ''
        );
    }        
}
