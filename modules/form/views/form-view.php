<?php
require_once \RomeThemeForm::module_dir() . 'form/form.php';
$index = 0;
$rtform = new WP_Query(['post_type' => 'romethemeform_form']);

?>


<div class="w-100 p-3">
    <div class="d-flex flex-column gap-1 mb-3">
        <h2>Forms</h2>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal">Add New</button>
        </div>
    </div>
    <div class="w-100">
        <table class="table shadow table-sm">
            <thead class="bg-white">
                <tr>
                    <td class="text-center" scope="col">No</td>
                    <td scope="col">Title</td>
                    <td scope="col">Shortcode</td>
                    <td scope="col">Entries</td>
                    <td scope="col">Author</td>
                    <td scope="col">Date</td>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($rtform->have_posts()) {
                    while ($rtform->have_posts()) {
                        $index = $index + 1;
                        $no = (string) $index;
                        $rtform->the_post();
                        $id_post =  intval(get_the_ID());
                        $delete = get_delete_post_link($id_post, '', false);
                        $edit_link = get_edit_post_link($id_post, 'display');
                        $edit_elementor = str_replace('action=edit', 'action=elementor', $edit_link);
                        $status = (get_post_status($id_post) == 'publish') ? 'Published' : 'Draft';
                        $entries = \RomethemeForm\Form\Form::count_entries($id_post);
                        $shortcode = get_post_meta($id_post, 'rtform_shortcode', true);
                        $success_msg = get_post_meta($id_post, 'rtform_form_success_message', true);
                        $f = "export_entries(' " . $id_post . " ',' " . get_the_title() . " ')";
                        echo '<tr>';
                        echo '<td class="text-center">' . esc_html__($no, 'romethemeform') . '</td>';
                        echo '<td><div>' . esc_html(get_the_title());
                        echo '</div>';
                        echo '<smal style="font-size: 13px;">
                        <a type="button" class="link" data-bs-toggle="modal" 
                        data-bs-target="#formUpdate" data-form-id="' . $id_post . '" 
                        data-form-name="' . esc_attr(get_the_title()) . '" 
                        data-form-entry="' . esc_attr(get_post_meta($id_post, "rtform_form_entry_title", true)) . '"
                        data-form-restricted ="' . esc_attr(get_post_meta($id_post, "rtform_form_restricted", true)) . '"
                        data-form-msg-success="' . esc_attr($success_msg) . '"
                        >
                        Edit</a>&nbsp;|&nbsp; <a class="link" href="' . esc_url($edit_elementor) . '">Edit Form</a> &nbsp;|&nbsp;<a class="link-danger" href="' . esc_url($delete) . '">Trash</a></small>';
                        echo '</td>';
                        echo '<td>' . esc_html($shortcode) . '</td>';
                        echo '<td>
                        <a class="btn btn-outline-primary" href="' . esc_url(admin_url("admin.php?page=romethemeform-entries&rform_id=" . $id_post)) . '" type="button" 
                        >' . esc_html($entries) . '</a>
                        <a type="button" class="btn btn-outline-success" onclick="' . esc_attr($f) . '">Export CSV</a>
                        </td>';
                        echo '<td>' . esc_html(get_the_author()) . '</td>';
                        echo '<td><small>' . esc_html($status) . '</small><br><small>' . esc_html(get_the_date('Y/m/d') . ' at ' . get_the_date('H:i a')) . '</small></td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td class="text-center" colspan="6">' . esc_html('No Data') . '</td></tr>';
                }

                ?>
            </tbody>
            <tfoot>
                <tr class="bg-white">
                    <td scope="col"></td>
                    <td scope="col">Title</td>
                    <td scope="col">Shortcode</td>
                    <td scope="col">Entries</td>
                    <td scope="col">Author</td>
                    <td scope="col">Date</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add Form</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rtform-add-form" method="post">
                    <input id="action" name="action" type="text" value="rtformnewform" hidden>
                    <label for="form-name">Form Name</label>
                    <input type="text" name="form-name" id="form-name" class="form-control p-2" placeholder="Enter Form Name">
                    <h5 class="my-3">Settings</h5>
                    <hr>
                    <div class="mb-3">
                        <label for="success-message" class="form-label">Success Message</label>
                        <input type="text" class="form-control p-2" id="success-message" name="success-message" value="Thank you! Form submitted successfully.">
                    </div>
                    <div class="mb-3">
                        <label for="entry-name" class="form-label">Entry Title</label>
                        <input type="text" class="form-control p-2" id="entry-name" name="entry-name" value="Entry #">
                    </div>
                    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
                        <span>
                            <p class="m-0">Require Login</p>
                            <p class="fw-light fst-italic text-black-50">Without login, user can't submit the form.</p>
                        </span>
                        <label class="switch">
                            <input name="require-login" id="switch" type="checkbox" value="true">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="rform-save-button" onclick="add_new_form()" type="button" class="btn btn-primary rform-save-btn">Save & Edit</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formUpdate" tabindex="-1" aria-labelledby="updateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="updateLabel">Update Form</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rtform-update-form" method="post">
                    <input id="action" name="action" type="text" value="rtformupdate" hidden>
                    <input type="text" name="id" id="id" hidden>
                    <label for="form-name">Form Name</label>
                    <input type="text" name="form-name" id="form-name" class="form-control p-2" placeholder="Enter Form Name">
                    <h5 class="my-3">Settings</h5>
                    <hr>
                    <div class="mb-3">
                        <label for="success-message" class="form-label">Success Message</label>
                        <input type="text" class="form-control p-2" id="success-message" name="success-message">
                    </div>
                    <div class="mb-3">
                        <label for="entry-name" class="form-label">Entry Title</label>
                        <input type="text" class="form-control p-2" id="entry-name" name="entry-name">
                    </div>
                    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
                        <span>
                            <p class="m-0">Require Login</p>
                            <p class="fw-light fst-italic text-black-50">Without login, user can't submit the form.</p>
                        </span>
                        <label class="switch">
                            <input name="require-login" id="switch" type="checkbox" value="true">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="rform-update-button" onclick="update_form()" type="button" class="btn btn-primary rform-save-btn">Save changes</button>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f0f0f1;
    }
</style>
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 25px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 17px;
        width: 17px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .rform-save-btn {
        width: 8rem;
    }

    body {
        background-color: #f0f0f1;
    }
</style>