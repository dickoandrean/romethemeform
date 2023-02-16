<?php

class RForm extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'rform';
    }
    public function get_title()
    {
        return 'Rometheme Form';
    }
    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }
    public function get_categories()
    {
        return ['romethemeform_form_fields'];
    }
    public function show_in_panel()
    {
        return 'romethemeform_form' != get_post_type();
    }

    public function get_style_depends()
    {
        return ['rform-style'];
    }

    public function get_keywords()
    {
        return ['rometheme form'];
    }
    protected function register_controls()
    {
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Content', 'romethemeform'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT
        ]);
        $this->add_control('form-control', [
            'label' => esc_html('Select Form'),
            'type' => 'rform_control',
        ]);
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $form_id = $settings['form-control'];
        $shortcode = '[rform form_id=' . $form_id . ']';
        $restricted = get_post_meta($form_id, 'rtform_form_restricted', true);
        $success_msg = get_post_meta($form_id, 'rtform_form_success_message', true);
?>
        <form id="rform-<?php echo esc_attr($this->get_id_int()) ?>" data-form="<?php echo esc_html__($form_id) ?>">
            <div class="require-login msg">
                <div class="require-msg-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="#FF0000" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                    </svg>
                    <div style="width: 100% ;">
                        <h5>Required Login</h5>
                        Please Login for Submit Form.
                    </div>
                    <div>
                        <a type="button" class="close-msg">Close</a>
                    </div>
                </div>
            </div>
            <div class="success-submit msg">
                <div class="success-body">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="#4CAF50" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                    </svg>
                    <div style="width: 100%;">
                        <h5>Success</h5>
                        <?php echo esc_html__($success_msg, 'romethemeform'); ?>
                    </div>
                    <div>
                        <a type="button" class="close-msg">Close</a>
                    </div>
                </div>
            </div>
            <?php echo do_shortcode($shortcode); ?>
        </form>
        <?php
        if ($restricted == true) {
            if (!is_user_logged_in()) {
        ?>
                <script>
                    var parForm = document.getElementById('rform-<?php echo esc_js($this->get_id_int()) ?>');
                    var btn = parForm.querySelector("#rform-button-submit");
                    parForm.classList.add('rform-dsb');
                </script>
<?php
            }
        }
    }
}
