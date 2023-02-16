<?php

class RFormControls extends \Elementor\Base_Data_Control
{

    public function get_type()
    {
        return 'rform_control';
    }

    public static function get_forms()
    {
        $form_list = [];

        $forms = get_posts(['post_type' => 'romethemeform_form']);

        foreach ($forms as $form) {
            $form_list[$form->ID] = $form->post_title;
        }

        return $form_list;
    }

    protected function get_default_settings()
    {
        return ['form_id' => self::get_forms()];
    }

    public function enqueue()
    {
        // Styles
        wp_register_style('control-style', \RomethemeForm::controls_url() . 'assets/css/form_modal.css');
        wp_enqueue_style('control-style');

        // Scripts
        wp_register_script('control-script', \RomeThemeForm::controls_url() . 'assets/js/form_modal.js');
        wp_enqueue_script('control-script');
    }

    public function content_template()
    {
        $control_uid = $this->get_control_uid();
?>
        <div class="flex-direction-col">
            <div class="elementor-control-field">
                <# if ( data.label ) {#>
			    <label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			    <# } #>

                <select id="<?php echo esc_attr( $control_uid ); ?>" data-setting="{{ data.name }}">
					<# _.each( data.form_id, function( form_label, form_value ) { #>
					<option value="{{ form_value }}">{{{ form_label }}}</option>
					<# } ); #>
				</select>
            </div>
            <div class="elementor-control-input-wrapper">
                <div class="edit-form-wrapper">
                <button onclick="openmodal( '<?php echo esc_js( $control_uid )?>' , '<?php echo esc_js( admin_url() ) ?>')" type="button" class="elementor-button elementor-button-success elementor-modal-iframe-btn-control">
                    <?php echo esc_html__('EDIT FORM', 'romethemeform') ?>
                </button>
                </div>
                <div id="myModal<?php echo $control_uid ?>" class="modal">
                    <div class="modal-content">
                        <div class="elementor-editor-header-iframe">
                        <div class="rform-editor-header">
                    <svg id="e7Eau9k46x81" xmlns="http://www.w3.org/2000/svg" fill="#f0f0f1" width="30" height="30" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 250 250" shape-rendering="geometricPrecision" text-rendering="geometricPrecision"><g transform="matrix(.060606 0 0-.053983-25.454395 259.154569)"><path d="M2350,4777c-36-13-277-146-540-298-261-151-639-369-840-484-229-132-383-226-413-254-58-54-93-110-118-192-18-58-19-109-19-1064s1-1006,19-1064c44-143,105-204,341-338c96-55,396-228,665-383c978-565,910-530,1035-530c110,0,162,20,391,152c118,68,502,290,854,492c709,408,719,414,786,552l34,69v1055v1055l-32,65c-60,122-68,128-898,605-418,241-802,463-854,493-163,95-279,115-411,69Zm799-1508c353-204,641-375,641-380c0-16-41-45-250-180-231-148-251-159-279-159-12,0-188,97-393,216-205,118-375,216-378,217s-174-92-380-208c-246-137-387-211-411-213-32-3-61,13-276,156-218,145-266,183-245,196c4,2,156,87,337,189c182,101,440,247,575,324c305,175,377,213,400,213c10,0,307-167,659-371Zm-264-613c243-139,283-166,271-185-4-5-29-22-57-37-27-15-46-31-42-35c5-4,166-98,358-209c193-111,356-208,364-216c12-10,12-16,2-28-17-20-446-295-488-312-17-8-40-11-50-7-17,7-437,246-518,296-22,13-88,52-146,86l-106,61-364-211c-452-262-409-240-450-228-56,16-494,302-497,325-2,16,114,87,705,432c632,368,712,412,743,408c21-2,131-59,275-140Z"/></g></svg>
                    <strong>ROMETHEMEFORM</strong>
                    </div>
                            <button onclick="closemodal('<?php echo esc_js( $control_uid )?>')" class="elementor-button elementor-button-success elementor-modal-iframe-btn-control"><?php echo esc_html__('SAVE & CLOSE', 'romethemeform') ?></button>
                        </div>
                        <div class="elementor-editor-container">
                            <iframe class="ifr-editor" id="ifr-<?php echo esc_attr( $control_uid )?>" src="" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <# if ( data.description ) { #>
            <div class="elementor-control-field-description">{{{ data.description }}}</div>
            <# } #>
        <?php
    }
}
