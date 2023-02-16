<?php

/**
 * Plugin Name:       RomethemeForm
 * Description:       The Advanced Form Builder for Elementor 
 * Version:           1.0.0
 * Author:            Rometheme
 * License : 		  GPLv3
 * 
 */

define('ROMETHEMEFORM_PLUGIN_DIR', plugin_dir_path(__FILE__));

class RomeThemeForm
{
    function __construct()
    {
        require_once self::plugin_dir() . 'libs/notice/notice.php';
        add_action('admin_menu', [$this, 'romethemeform_add_menu']);
        add_action('plugins_loaded', [$this, 'init'], 100);
    }
    public function isCompatible()
    {
        if (!did_action('elementor/loaded')) {
            add_action('admin_head', array($this, 'missing_elementor'));
            return false;
        }

        return true;
    }
    public function init()
    {
        if ($this->isCompatible()) {
            require_once self::plugin_dir() . '/plugin.php';
            \RomethemeFormPlugin\Plugin::register_autoloader();
            \RomethemeFormPlugin\Plugin::load_romethemeform_form();
            add_action('admin_enqueue_scripts', [$this, 'register_style']);
            add_action('elementor/widgets/register', [\RomethemeFormPlugin\Plugin::class, 'register_widget']);
            add_action('elementor/elements/categories_registered', [\RomethemeFormPlugin\Plugin::class, 'add_elementor_widget_categories']);
            add_action('wp_enqueue_scripts', [\RomethemeFormPlugin\Plugin::class, 'register_widget_styles']);
            add_action('elementor/frontend/after_register_scripts', [\RomethemeFormPlugin\Plugin::class, 'register_widget_scripts']);
            add_action('elementor/editor/before_enqueue_styles', [\RomethemeFormPlugin\Plugin::class, 'register_widget_styles']);
            add_action('elementor/editor/before_register_scripts', [\RomethemeFormPlugin\Plugin::class, 'register_widget_scripts']);
            add_action('elementor/editor/before_enqueue_scripts', [\RomethemeFormPlugin\Plugin::class, 'register_widget_scripts']);
            add_action('elementor/controls/register', [\RomethemeFormPlugin\Plugin::class, 'add_controls']);
        }
    }

    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     * @var string Minimum Elementor version required to run the plugin.
     */
    static function min_el_version()
    {
        return '3.0.0';
    }


    /**
     * Plugin file
     *
     * @since 1.0.0
     * @var string plugins's root file.
     */
    static function plugin_file()
    {
        return __FILE__;
    }

    /**
     * Plugin url
     *
     * @since 1.0.0
     * @var string plugins's root url.
     */
    static function plugin_url()
    {
        return trailingslashit(plugin_dir_url(__FILE__));
    }
    /**
     * Plugin dir
     *
     * @since 1.0.0
     * @var string plugins's root directory.
     */
    static function plugin_dir()
    {
        return trailingslashit(plugin_dir_path(__FILE__));
    }

    /**
     * Plugin's module directory.
     *
     * @since 1.0.0
     * @var string module's root directory.
     */
    static function module_dir()
    {
        return self::plugin_dir() . 'modules/';
    }

    /**
     * Plugin's module url.
     *
     * @since 1.0.0
     * @var string module's root url.
     */
    static function module_url()
    {
        return self::plugin_url() . 'modules/';
    }

    /**
     * Plugin's Widget directory.
     *
     * @since 1.0.0
     * @var string widget's root directory.
     */
    static function widget_dir()
    {
        return self::plugin_dir() . 'widgets/';
    }

    /**
     * Plugin's widget url.
     *
     * @since 1.0.0
     * @var string widget's root url.
     */
    static function widget_url()
    {
        return self::plugin_url() . 'widgets/';
    }

    /**
     * Plugin's controls dir.
     *
     * @since 1.0.0
     * @var string control's root dir.
     */
    static function controls_dir()
    {
        return self::plugin_dir() . 'controls/';
    }

    /**
     * Plugin's controls url.
     *
     * @since 1.0.0
     * @var string control's root url.
     */
    static function controls_url()
    {
        return self::plugin_url() . 'controls/';
    }

    public function missing_elementor()
    {
        $btn = array(
            'default_class' => 'button',
            'class'         => 'button-primary ', // button-primary button-secondary button-small button-large button-link
        );

        if (file_exists(WP_PLUGIN_DIR . '/elementor/elementor.php')) {
            $btn['text'] = esc_html__('Activate Elementor', 'romethemeform-plugin');
            $btn['url']  = wp_nonce_url('plugins.php?action=activate&plugin=elementor/elementor.php&plugin_status=all&paged=1', 'activate-plugin_elementor/elementor.php');
        } else {
            $btn['text'] = esc_html__('Install Elementor', 'romethemeform-plugin');
            $btn['url']  = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__('%1$s requires %2$s to work properly. Please install and activate it first.', 'romethemeform-plugin'),
            '<strong>' . esc_html__('RomeThemeForm', 'romethemeform-plugin') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'romethemeform-plugin') . '</strong>'
        );

        \Oxaim\Libs\Notice::instance('romethemeform-plugin', 'unsupported-elementor-version')
            ->set_type('error')
            ->set_message($message)
            ->set_button($btn)
            ->call();
    }

    function romethemeform_add_menu()
    {
        add_menu_page(
            'romethemeform-plugin', //page-title
            'RomethemeForm', //title
            'manage_options', //capability
            'romethemeform', //slug
            [$this, 'romethemeform_call'], //callback,
            $this->plugin_url() . 'assets/images/rform.svg', //icon,
            20
        );
    }
    function romethemeform_call()
    {
        require self::plugin_dir() . 'views/welcome.php';
    }

    function register_style()
    {
        $screen = get_current_screen();
        if ($screen->id == 'toplevel_page_romethemeform') {
            wp_enqueue_style('style.css', self::plugin_url() . 'bootstrap/css/bootstrap.css');
        }
    }
}

new RomethemeForm();