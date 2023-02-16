<?php

namespace RomethemeFormPlugin;

use RForm;
use RomeThemeForm;
use RomethemeForm\Autoloader;
use RomethemeForm\Form\Form;

class Plugin
{
    public static function register_autoloader()
    {
        require_once \RomeThemeForm::plugin_dir() . '/autoloader.php';
        Autoloader::run();
    }

    public static function load_romethemeform_form()
    {
        require_once \RomethemeForm::module_dir() . 'form/form.php';
        new Form();
    }

    public static function register_widget($widgets_manager)
    {
        require_once(RomeThemeForm::widget_dir() . 'rtform-text.php');
        require_once(RomeThemeForm::widget_dir() . 'rtform.php');
        require_once(RomeThemeForm::widget_dir() . 'rform-button-submit.php');
        require_once(RomeThemeForm::widget_dir() . 'rtform-email.php');
        require_once(RomeThemeForm::widget_dir() . 'rtform-text-area.php');
        require_once(RomeThemeForm::widget_dir() . 'rtform-date.php');
        require_once(RomeThemeForm::widget_dir() . 'rtform-time.php');
        $widgets_manager->register(new RForm());
        $widgets_manager->register(new \RTForm_Text());
        $widgets_manager->register(new \Rform_Button_Submit());
        $widgets_manager->register(new \RTForm_Email());
        $widgets_manager->register(new \RTForm_TextArea());
        $widgets_manager->register(new \RTForm_Date());
        $widgets_manager->register(new \RTForm_Time());
    }

    public static function register_widget_styles()
    {
        wp_enqueue_style('rtform-text-style', \RomeThemeForm::widget_url() . 'assets/css/rtform_text.css');
        wp_enqueue_style('rform-style', \RomeThemeForm::widget_url() . 'assets/css/rform.css');
        wp_enqueue_style('spinner-style' , \RomeThemeForm::widget_url() . 'assets/css/spinner-loading.css');
        wp_enqueue_style('rform-btn-style' , \RomeThemeForm::widget_url() . 'assets/css/rform-button.css');;
    }

    public static function register_widget_scripts()
    {
        wp_enqueue_script('rtform-text-js', \RomeThemeForm::widget_url() . 'assets/js/rtform_text.js');
        wp_enqueue_script('rform-script', \RomeThemeForm::widget_url() . 'assets/js/rform.js');
        wp_localize_script('rform-script', 'romethemeform_ajax_url', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }

    public static function add_elementor_widget_categories($elements_manager)
    {
        $elements_manager->add_category('romethemeform_form_fields', [
            'title' => esc_html__('Rometheme Form')
        ]);
    }

    public static function add_controls($controls_manager)
    {
        require_once(RomeThemeForm::controls_dir() . 'form_controls.php');
        $controls_manager->register(new \RFormControls());
    }
}
