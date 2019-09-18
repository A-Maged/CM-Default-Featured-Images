<?php

/**
 * Plugin Name: CM Default Featured Images
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

define('OPTION_NAME', 'Default_Image');

if (!class_exists('cm_default_thumb')) {
    class cm_default_thumb
    {
        public $plugin_name;
        public $plugin_slug;
        public $plugin_version = '1.0.0';

        public function __construct()
        {
            $this->plugin_name = explode('/', plugin_basename(__FILE__))[0];
            $this->plugin_slug = sanitize_title($this->plugin_name);

            add_action('admin_menu', array(&$this, 'register_admin_page'));
            add_action('admin_enqueue_scripts', array(&$this, 'load_admin_libs'));
            add_action('wp_ajax_update_options', array(&$this, 'update_options_ajax'));
            add_filter("get_post_metadata", array(&$this, 'set_default_thumbnail'), 10, 4);
        }

        // ACTIVATE
        // register plugin options page and show in admin bar
        // flush rewrite

        public function load_admin_libs()
        {
            wp_enqueue_media();
            
            wp_enqueue_style('wp_media_uploader_css', plugin_dir_url(__FILE__) . 'admin-page.css', false, null);
            wp_enqueue_script('wp_media_uploader_js', plugin_dir_url(__FILE__) . 'admin-page.js', array( 'jquery' ), null);

            //Here we create a javascript object variable called "youruniquejs_vars". We can access any variable in the array using youruniquejs_vars.name_of_sub_variable
            wp_localize_script(
                'wp_media_uploader_js',
                'wp_localized_vars',
                array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                )
            );
        }


        public function register_admin_page()
        {
            add_menu_page(
                'Default Featured Image',
                'DFI Options',
                'manage_options',
                'cm_default_featured_image',
                [ $this,'admin_index' ],
                '',
                110
            );
        }

        // admin page template
        public function admin_index()
        {
            require_once plugin_dir_path(__FILE__) . 'admin-page.php';
        }


        public function update_options_ajax()
        {
            $all = $_POST['options'];

            update_option(OPTION_NAME, $all);

            wp_send_json([
                '$_post' => $_POST,
                '$all' => $all
            ]);
        }
        
        /* DEACTIVATE */

        /* UNINSTALL */
        // delete plugin data from db


        /*
        * return the id of the default image if the post don't have a thumbnail
        */
        public function set_default_thumbnail($null = null, $post_id, $meta_key, $single)
        {
            /* skip if : post has a thumbnail OR on backend screen*/
            if ('_thumbnail_id' !== $meta_key || is_admin()) {
                return $null;
            }

            global $wpdb;
            $thumb = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta where meta_key = '_thumbnail_id' and post_id = '$post_id'");
            if ($thumb) {
                return $thumb;
            }

            // GET POST TYPE
            $current_p_type = get_post_type($post_id);

            // GET OPTIONS
            $options = get_option(OPTION_NAME);

            // return default img id
            if (array_key_exists($current_p_type, $options)) {
                return $options[$current_p_type] ;
            }
        }
    }

    new cm_default_thumb();
}
