<?php
/**
* @package Custom_Registration_Fields
* @version 1.0
*/
/*
Plugin Name: ScriptHere's Custom Registration Fields
Plugin URI: https://github.com/blogscripthere/custom_registration_fields
Description: Add custom fields to WordPress frontend registration page, manually add a WordPress user with administrator and WordPress user profile page.
Author: Narendra Padala
Author URI: https://in.linkedin.com/in/narendrapadala
Text Domain: shcf
Version: 1.0
Last Updated: 02/12/2017
*/

/**
* Storing every field within an array
*/
$user_extra_fields =  array(
    array( 'phone', __( 'Phone Number', 'shcf' ), true )
);


/**
* display additional fields at frontend user register page callback
*/
function sh_display_frontend_custom_fields_callback(){
    //get extra fields globally defined
    global $user_extra_fields;
    //init
    $html = '';
    //sh_pr($user_extra_fields);exit;
    //loop each extra fields
    foreach ($user_extra_fields as $extra_field){
        //check
        if(is_array($extra_field) && !empty($extra_field)){
            //set field details
            list($field_id,$field_label,$display_staus) = $extra_field;
            //check
            if($display_staus){
                //append each field
                $html .='<p>';
                $html .='<label for="'.$field_id.'">'.$field_label.'<br>';
                //get and set any values already sent
                $value = ( isset( $_POST[$field_id] ) ) ? $_POST[$field_id] : '';
                //set
                $html .='<input name="'.$field_id.'" id="'.$field_id.'" class="input" value="'.esc_attr( stripslashes( $value ) ).'" type="text"></label>';
                $html .='</p>';
            }
        }
    }
    //display
    echo $html;
}

/**
 * display additional fields at frontend user register page hook
 */
add_action( 'register_form', 'sh_display_frontend_custom_fields_callback' );


/**
 * validate additional fields callback
 */
function sh_check_custom_fields_callback( $errors, $sanitized_user_login, $user_email ) {
    //get extra fields globally defined
    global $user_extra_fields;
    //loop each extra fields
    foreach ($user_extra_fields as $extra_field) {
        //check
        if (is_array($extra_field) && !empty($extra_field)) {
            //set field details
            list($field_id, $field_label, $display_staus) = $extra_field;
            //check
            if ($display_staus) {
                //check
                switch ($field_id){
                    case 'phone':{
                        //check
                        if(isset($_POST[$field_id]) && empty($_POST[$field_id])){
                            $errors->add( $field_id.'_error', __( '<strong>ERROR</strong>: Invalid '.$field_label,'shcf'));
                        }elseif (isset($_POST[$field_id]) && !empty($_POST[$field_id])){
                            //check for 10 digit mobile number
                            if(!preg_match('/^[0-9]{10}+$/', $_POST[$field_id])){
                                $errors->add( $field_id.'_error', __( '<strong>ERROR</strong>: Invalid '.$field_label,'shcf'));
                            }
                        }
                        break;
                    }
                    default:{
                        //check
                        if(isset($_POST[$field_id]) && empty($_POST[$field_id])){
                            $errors->add( $field_id.'_error', __( '<strong>ERROR</strong>: Invalid '.$field_label,'shcf'));
                        }
                        break;
                    }

                }
            }
        }
    }
    //return
    return apply_filters( 'sh_custom_fields_errors', $errors);
}

/**
* validate additional fields hook
*/
add_filter( 'registration_errors', 'sh_check_custom_fields_callback', 10, 3 );

/**
 * save additional fields at user register page callback
 */
function sh_save_custom_fields_callback( $user_id ) {
    //get extra fields globally defined
    global $user_extra_fields;
    //loop each extra fields
    foreach ($user_extra_fields as $extra_field) {
        //check
        if (is_array($extra_field) && !empty($extra_field)) {
            //set field details
            list($field_id, $field_label, $display_staus) = $extra_field;
            //check
            if ($display_staus && isset( $_POST[$field_id] ) ) {
                //save custom field data
                update_user_meta($user_id, $field_id, $_POST[$field_id]);
            }
        }
    }
}

/**
 * save additional fields at frontend user register page hook
 */
add_action( 'user_register', 'sh_save_custom_fields_callback', 10, 1 );



/**
* display additional fields at admin user register page callback
*/
function sh_display_admin_custom_fields_callback( $operation )
{
    //get extra fields globally defined
    global $user_extra_fields;
    //check
    if ( 'add-new-user' !== $operation ) {
        // $operation may also be 'add-existing-user'
        return;
    }
    //init
    $html = '';
    //loop each extra fields
    foreach ($user_extra_fields as $extra_field) {
        //check
        if (is_array($extra_field) && !empty($extra_field)) {
            //set field details
            list($field_id, $field_label, $display_staus) = $extra_field;
            //check
            if($display_staus) {
                //get and set any values already sent
                $value = ( isset( $_POST[$field_id] ) ) ? $_POST[$field_id] : '';
                //append each field
                $html .= '<table class="form-table">
                <tbody>
                    <tr class="form-field">
                        <th scope="row"><label for="'.esc_html($field_id).'">'.$field_label.'<span class="description"> '.__(' (required)', 'shcf' ).'</span></label></th>
                        <td><input name="'.esc_html($field_id).'" id="'.esc_html($field_id).'" value="'.esc_attr( stripslashes( $value ) ).'" type="text"></td>
                    </tr>
                <tbody>
	            </table>';
            }
        }
    }
    //display
	echo $html;
}

/**
* display additional fields at admin user register page hook
*/
add_action( 'user_new_form', 'sh_display_admin_custom_fields_callback' );



/**
 * validate admin additional fields callback
 */
function sh_check_admin_custom_fields_callback( $errors, $update, $user ) {
    //get extra fields globally defined
    global $user_extra_fields;
    //loop each extra fields
    foreach ($user_extra_fields as $extra_field) {
        //check
        if (is_array($extra_field) && !empty($extra_field)) {
            //set field details
            list($field_id, $field_label, $display_staus) = $extra_field;
            //check
            if ($display_staus) {
                //check
                switch ($field_id){
                    case 'phone':{
                        //check
                        if(isset($_POST[$field_id]) && empty($_POST[$field_id])){
                            $errors->add( $field_id.'_error', __( '<strong>ERROR</strong>: Invalid '.$field_label,'shcf'));
                        }elseif (isset($_POST[$field_id]) && !empty($_POST[$field_id])){
                            //check for 10 digit mobile number
                            if(!preg_match('/^[0-9]{10}+$/', $_POST[$field_id])){
                                $errors->add( $field_id.'_error', __( '<strong>ERROR</strong>: Invalid '.$field_label,'shcf'));
                            }
                        }
                        break;
                    }
                    default:{
                        //check
                        if(isset($_POST[$field_id]) && empty($_POST[$field_id])){
                            $errors->add( $field_id.'_error', __( '<strong>ERROR</strong>: Invalid '.$field_label,'shcf'));
                        }
                        break;
                    }

                }
            }
        }
    }
    //return
    return apply_filters( 'sh_admin_custom_fields_errors', $errors);
}

/**
 * validate admin additional fields hook
 */
add_action( 'user_profile_update_errors', 'sh_check_admin_custom_fields_callback', 10, 3 );

/**
 * save additional fields at admin user register page hook
 */
add_action( 'edit_user_created_user', 'sh_save_custom_fields_callback', 10, 1 );




/**
* display additional fields at admin user profile page callback
*/
function sh_display_profile_custom_fields_callback( $user )
{
    //get extra fields globally defined
    global $user_extra_fields;
    //init
    $html = '';
    //loop each extra fields
    foreach ($user_extra_fields as $extra_field) {
        //check
        if (is_array($extra_field) && !empty($extra_field)) {
            //set field details
            list($field_id, $field_label, $display_staus) = $extra_field;
            //check
            if($display_staus) {
                //get exter filed data from user meta table
                $value = get_user_meta($user->ID, $field_id);
                //append each field
                $html .= '<table class="form-table">
                <tbody>
                    <tr class="form-field">
                        <th scope="row"><label for="'.esc_html($field_id).'">'.$field_label.'<span class="description"> '.__(' (required)', 'shcf' ).'</span></label></th>
                        <td><input name="'.esc_html($field_id).'" id="'.esc_html($field_id).'" value="'.esc_attr( stripslashes( $value[0] ) ).'" type="text"></td>
                    </tr>
                <tbody>
	            </table>';
            }
        }
    }
    //display
    echo $html;
}

/**
* display additional fields at admin user profile page hooks
*/
add_action( 'show_user_profile', 'sh_display_profile_custom_fields_callback' );
add_action( 'edit_user_profile', 'sh_display_profile_custom_fields_callback' );


/**
* update additional fields at user profile page callback
*/
function sh_update_custom_fields_callback( $user_id ) {
    //get extra fields globally defined
    global $user_extra_fields;

    //check edit permission for user
    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;
    
    //loop each extra fields
    foreach ($user_extra_fields as $extra_field) {
        //check
        if (is_array($extra_field) && !empty($extra_field)) {
            //set field details
            list($field_id, $field_label, $display_staus) = $extra_field;
            //check
            if ($display_staus && isset( $_POST[$field_id] ) ) {
                //save custom field data
                update_user_meta($user_id, $field_id, $_POST[$field_id]);
            }
        }
    }
}

/**
* update additional fields at user profile page callback
*/
add_action( 'personal_options_update', 'sh_update_custom_fields_callback' );
add_action( 'edit_user_profile_update', 'sh_update_custom_fields_callback' );







/**
* print details for debug method
*/
function sh_pr($data){
   //check
   if(is_array($data) || is_object($data)) {
       echo "<pre>";
       print_r($data);
       echo "</pre>";
   }else{
       print $data;
   }
}



?>
