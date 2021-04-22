<?php
/**
** A base module for [textarea] and [textarea*]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_textarea1', 10, 0 );

function wpcf7_add_form_tag_textarea1() {
	wpcf7_add_form_tag( array( 'textarea', 'textarea*' ),
		'wpcf7_textarea_form_tag_handler1', array( 'name-attr' => true )
	);
}

function wpcf7_textarea_form_tag_handler1( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['cols'] = $tag->get_cols_option( '40' );
	$atts['rows'] = $tag->get_rows_option( '10' );
	$atts['maxlength'] = $tag->get_maxlength_option();
	$atts['minlength'] = $tag->get_minlength_option();

	if ( $atts['maxlength'] and $atts['minlength']
	and $atts['maxlength'] < $atts['minlength'] ) {
		unset( $atts['maxlength'], $atts['minlength'] );
	}

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	$atts['autocomplete'] = $tag->get_option( 'autocomplete',
		'[-0-9a-zA-Z]+', true );

	if ( $tag->has_option( 'readonly' ) ) {
		$atts['readonly'] = 'readonly';
	}

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	if ( $validation_error ) {
		$atts['aria-invalid'] = 'true';
		$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
			$tag->name
		);
	} else {
		$atts['aria-invalid'] = 'false';
	}

	$value = empty( $tag->content )
		? (string) reset( $tag->values )
		: $tag->content;

	if ( $tag->has_option( 'placeholder' )
	or $tag->has_option( 'watermark' ) ) {
		$atts['placeholder'] = $value;
		$value = '';
	}

	$value = $tag->get_default_option( $value );

	$value = wpcf7_get_hangover( $tag->name, $value );

	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><textarea %2$s>%3$s</textarea>%4$s</span>',
		sanitize_html_class( $tag->name ), $atts,
		esc_textarea( $value ), $validation_error
	);

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_textarea',
	'wpcf7_textarea_validation_filter1', 10, 2 );
add_filter( 'wpcf7_validate_textarea*',
	'wpcf7_textarea_validation_filter1', 10, 2 );

function wpcf7_textarea_validation_filter1( $result, $tag ) {
	$type = $tag->type;
	$name = $tag->name;

	$value = isset( $_POST[$name] ) ? (string) $_POST[$name] : '';

	if ( $tag->is_required() and '' === $value ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}

	if ( '' !== $value ) {
		$maxlength = $tag->get_maxlength_option();
		$minlength = $tag->get_minlength_option();

		if ( $maxlength and $minlength
		and $maxlength < $minlength ) {
			$maxlength = $minlength = null;
		}

		$code_units = wpcf7_count_code_units( stripslashes( $value ) );

		if ( false !== $code_units ) {
			if ( $maxlength and $maxlength < $code_units ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_too_long' ) );
			} elseif ( $minlength and $code_units < $minlength ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_too_short' ) );
			}
		}
	}

	return $result;
}

/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_textarea1', 20, 0 );

function wpcf7_add_tag_generator_textarea1() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'textarea', __( 'text area', 'contact-form-7' ),
		'wpcf7_tag_generator_textarea1' );
}

function wpcf7_tag_generator_textarea1( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'textarea';

	$description = __( "Generate a form-tag for a multi-line text input field. For more details, see %s.", 'contact-form-7' );

	$desc_link = wpcf7_link( __( 'https://contactform7.com/text-fields/', 'contact-form-7' ), __( 'Text fields', 'contact-form-7' ) );

?>
<div class="feedback-form">
    <form>
        <div class="form-group">
            <label for="exampleFormControlInput1">Email address</label>
            <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
        </div>
        <div class="form-group">
            <label for="exampleFormControlSelect1">Example select</label>
            <select class="form-control" id="exampleFormControlSelect1">
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleFormControlSelect2">Example multiple select</label>
            <select multiple class="form-control" id="exampleFormControlSelect2">
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleFormControlTextarea1">Example textarea</label>
            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
        </div>
    </form>
</div>

<?php
}
