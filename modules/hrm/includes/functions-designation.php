<?php

/**
 * Create a new designation
 *
 * @param  array   arguments
 *
 * @return int|false
 */
function erp_hr_create_designation( $args = array() ) {
    global $wpdb;

    $defaults = array(
        'id'          => 0,
        'company_id'  => 0,
        'title'       => '',
        'description' => '',
        'status'      => 1
    );

    $fields = wp_parse_args( $args, $defaults );

    // validation
    if ( ! $fields['company_id'] ) {
        return new WP_Error( 'no-company-id', __( 'No company provided.', 'wp-erp' ) );
    }

    if ( empty( $fields['title'] ) ) {
        return new WP_Error( 'no-name', __( 'No department name provided.', 'wp-erp' ) );
    }

    // unset the department id
    $desig_id = $fields['id'];
    unset( $fields['id'] );

    if ( ! $desig_id ) {

        if ( $wpdb->insert( $wpdb->prefix . 'erp_hr_designations', $fields ) ) {

            do_action( 'erp_hr_desig_new', $wpdb->insert_id, $fields );

            return $wpdb->insert_id;
        }

    } else {

        if ( $wpdb->update( $wpdb->prefix . 'erp_hr_designations', $fields, array( 'id' => $desig_id ) ) ) {

            do_action( 'erp_hr_desig_updated', $desig_id, $fields );

            return $desig_id;
        }

    }

    return false;
}

/**
 * Get all the departments of a company
 *
 * @param  int  the company id
 *
 * @return array  list of departments
 */
function erp_hr_get_designations( $company_id ) {
    global $wpdb;

    return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}erp_hr_designations WHERE company_id = %d", $company_id ) );
}

/**
 * Delete a department
 *
 * @param  int  department id
 *
 * @return bool
 */
function erp_hr_delete_designation( $designation ) {
    global $wpdb;

    do_action( 'erp_hr_desig_delete', $designation );

    return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}erp_hr_designations WHERE id = %d", $designation ) );
}

/**
 * Get the raw designations dropdown
 *
 * @param  int  company id
 *
 * @return array  the key-value paired designations
 */
function erp_hr_get_designation_dropdown_raw( $company_id ) {
    $designations = erp_hr_get_designations( $company_id );
    $dropdown     = array( 0 => __( '- Select Designation -' ) );

    if ( $designations ) {
        foreach ($designations as $key => $designation) {
            $dropdown[$designation->id] = stripslashes( $designation->title );
        }
    }

    return $dropdown;
}

/**
 * Get company designations dropdown
 *
 * @param  int  company id
 * @param  string  selected designation
 *
 * @return string  the dropdown
 */
function erp_hr_get_designation_dropdown( $company_id, $selected = '' ) {
    $designations = erp_hr_get_designation_dropdown_raw( $company_id );

    if ( $designations ) {
        foreach ($designations as $key => $title) {
            $dropdown .= sprintf( "<option value='%s'>%s</option>\n", $key, $title );
        }
    }

    return $dropdown;
}
