<?php

namespace RomethemeForm\Form;

use WP_Query;

class Form
{
    public $dir;
    public $url;

    function __construct()
    {
        $this->dir = \RomethemeForm::module_dir() . 'form/';
        $this->url = \RomeThemeForm::module_url() . 'form/';
        add_action('init', [$this, 'romethemeform_template_post_type']);
        add_action('admin_menu', [$this, 'add_form_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_romethemeform_scripts']);
        add_action('wp_ajax_rtformnewform', [$this, 'rtformnewform']);
        add_action('wp_ajax_nopriv_rtformnewform', [$this, 'rtformnewform']);
        add_action('wp_ajax_rtformupdate', [$this, 'rtformupdate']);
        add_action('wp_ajax_nopriv_rtformupdate', [$this, 'rtformupdate']);
        add_action('wp_ajax_rformsendform', [$this, 'rformsendform']);
        add_action('wp_ajax_nopriv_rformsendform', [$this, 'rformsendform']);
        add_action('wp_ajax_export_entries', [$this, 'export_entries']);
        add_action('wp_ajax_nopriv_export_entries', [$this, 'export_entries']);
        add_filter('single_template', array($this, 'load_canvas_template'));
        add_shortcode('rform', [$this, 'rform_shortcode']);
    }

    function add_form_menu()
    {
        add_submenu_page('romethemeform', 'Forms', 'Forms', 'manage_options', 'romethemeform-form', [$this, 'romethemeform_form_call']);
        add_submenu_page('romethemeform', 'Entries', 'Entries', 'manage_options', 'romethemeform-entries', [$this, 'romethemeform_entries_call']);
    }
    function romethemeform_form_call()
    {
        require_once $this->dir . 'views/form-view.php';
    }

    function romethemeform_entries_call()
    {
        if (!isset($_GET['entry_id']) || $_GET['entry_id'] == "" || $_GET['rform_id'] != '') {
            require_once $this->dir . 'views/entries-table.php';
        } else {
            require_once $this->dir . 'views/entries-view.php';
        }
    }

    function enqueue_romethemeform_scripts()
    {
        $screen = get_current_screen();
        if ('romethemeform_page_romethemeform-form' === $screen->id || 'romethemeform_page_romethemeform-entries' === $screen->id) {
            wp_enqueue_style('style.css', \RomeThemeForm::plugin_url() . 'bootstrap/css/bootstrap.css');
            wp_enqueue_script('romethemeform-js', \RomeThemeForm::plugin_url() . 'bootstrap/js/bootstrap.min.js');
            wp_enqueue_script('rform-js', $this->url . 'assets/js/form.js');
            wp_localize_script('rform-js', 'romethemeform_ajax_url', array(
                'ajax_url' => admin_url('admin-ajax.php')
            ));
            wp_localize_script('rform-js', 'romethemeform_url', ['form_url' =>  admin_url() . 'admin.php?page=romethemeform-form']);
        }
    }


    function romethemeform_template_post_type()
    {
        $labels = array(
            'name'               => esc_html__('Rometheme Form Templates', 'romethemeform'),
            'singular_name'      => esc_html__('Templates', 'romethemeform'),
            'menu_name'          => esc_html__('Form', 'romethemeform'),
            'name_admin_bar'     => esc_html__('Form', 'romethemeform'),
            'add_new'            => esc_html__('Add New', 'romethemeform'),
            'add_new_item'       => esc_html__('Add New Template', 'romethemeform'),
            'new_item'           => esc_html__('New Template', 'romethemeform'),
            'edit_item'          => esc_html__('Edit Template', 'romethemeform'),
            'view_item'          => esc_html__('View Template', 'romethemeform'),
            'all_items'          => esc_html__('All Templates', 'romethemeform'),
            'search_items'       => esc_html__('Search Templates', 'romethemeform'),
            'parent_item_colon'  => esc_html__('Parent Templates:', 'romethemeform'),
            'not_found'          => esc_html__('No Templates found.', 'romethemeform'),
            'not_found_in_trash' => esc_html__('No Templates found in Trash.', 'romethemeform'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'rewrite'             => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'exclude_from_search' => true,
            'capability_type'     => 'page',
            'hierarchical'        => false,
            'supports'            => array('title', 'thumbnail', 'elementor'),
        );
        register_post_type('romethemeform_form', $args);


        $label_entries = array(
            'name'               => esc_html__('Rometheme Form Entries', 'romethemeform'),
            'singular_name'      => esc_html__('Entry', 'romethemeform'),
            'menu_name'          => esc_html__('Entries', 'romethemeform'),
        );

        $args_entries = array(
            'labels'              => $label_entries,
            'public'              => true,
            'rewrite'             => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'exclude_from_search' => true,
            'capability_type'     => 'page',
            'hierarchical'        => false,
            'supports'            => array('title', 'thumbnail', 'elementor'),
        );
        register_post_type('romethemeform_entry', $args_entries);
    }

    function load_canvas_template($single_template)
    {

        global $post;

        if ('romethemeform_form' == $post->post_type) {

            $elementor_2_0_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

            if (file_exists($elementor_2_0_canvas)) {
                return $elementor_2_0_canvas;
            } else {
                return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
            }
        }

        return $single_template;
    }

    public function rtformnewform()
    {
        $data = [
            'post_author' => get_current_user_id(),
            'post_title' => sanitize_text_field($_POST['form-name']),
            'post_type' => 'romethemeform_form',
            'post_status' => 'publish',
        ];

        $form_id = wp_insert_post($data);
        add_post_meta($form_id, 'rtform_form_entry_title', sanitize_text_field($_POST['entry-name']));
        add_post_meta($form_id, 'rtform_form_restricted', sanitize_text_field($_POST['require-login']));
        add_post_meta($form_id, 'rtform_form_success_message', sanitize_text_field($_POST['success-message']));
        add_post_meta($form_id, 'rtform_shortcode', '[rform form_id=' . $form_id . ']');
    }

    public function rtformupdate()
    {
        $data = [
            'ID' => sanitize_text_field($_POST['id']),
            'post_title' => sanitize_text_field($_POST['form-name']),
            'meta_input' => [
                'rtform_form_entry_title' => sanitize_text_field($_POST['entry-name']),
                'rtform_form_restricted' => sanitize_text_field($_POST['require-login']),
                'rtform_form_success_message' => sanitize_text_field($_POST['success-message'])
            ]
        ];

        wp_update_post($data, false, true);
        exit;
    }

    public static function count_entries($id_post)
    {
        global $wpdb;
        $count = $wpdb->get_row("SELECT COUNT(*) AS THE_COUNT FROM $wpdb->postmeta WHERE (meta_key = 'rform-entri-form-id' AND meta_value = '$id_post')");

        return $count->THE_COUNT;
    }

    public function rform_shortcode($atts)
    {
        $form_id = shortcode_atts(array(
            'form_id' => ''
        ), $atts);

        if ('' == $form_id['form_id']) {
            return '<h6>Please Select Form.</h6>';
        } else {
            return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($form_id['form_id'], true);
        }
    }

    public static function rformsendform()
    {
        $form_id = sanitize_text_field($_POST['id']);
        $data = sanitize_text_field($_POST['data']);
        $entri_title = get_post_meta($form_id, 'rtform_form_entry_title', true);
        $args = [
            'post_type' => 'romethemeform_entry',
            'post_status' => 'publish',
            'post_title' => $entri_title
        ];
        $entri_id = wp_insert_post($args);
        add_post_meta($entri_id, 'rform-entri-data', $data);
        add_post_meta($entri_id, 'rform-entri-form-id', $form_id);
        $arg = [
            'ID' => $entri_id,
            'post_title' => $entri_title . ' ' . $entri_id,
        ];
        wp_update_post($arg);
    }

    public static function export_entries()
    {
        $form_id = sanitize_text_field($_GET['form_id']);
        $form_name = sanitize_text_field($_GET['form_name']);
        $file = fopen($form_name . '-' . $form_id .  '.csv', 'w');

        $args = [
            'post_type' => 'romethemeform_entry',
            'meta_query' => [
                'meta_value' => [
                    'key' => 'rform-entri-form-id',
                    'value' => $form_id,
                    'compare' => '='
                ]
            ],
        ];

        $entries = new WP_Query($args);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $form_name . '-' . $form_id . '.csv"');
        while ($entries->have_posts()) {
            $entries->the_post();
            $entri_id = get_the_ID();
            $datas = json_decode(get_post_meta($entri_id, 'rform-entri-data', true), true);
            $entri_title = [get_the_title()];
            fputcsv($file, $entri_title);
            $header = array_keys($datas);
            fputcsv($file, $header);
            $row = array_values($datas);
            fputcsv($file, $row);
            fputcsv($file, ['']);
        }
        fclose($file);
        readfile($form_name . '-' . $form_id .  '.csv');
        exit();
    }
}
