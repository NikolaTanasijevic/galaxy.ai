<?php

add_action('domain_cat_add_form_fields', 'gm_cat_intro_add_field');
add_action('domain_cat_edit_form_fields', 'gm_cat_intro_edit_field');

function gm_cat_intro_add_field() {
	?>
	<div class="form-field">
		<label for="gm_cat_intro">Category Intro Text</label>
		<textarea name="gm_cat_intro" id="gm_cat_intro" rows="4"></textarea>
		<p>Short intro paragraph shown on the category archive page.</p>
	</div>
	<?php
}

function gm_cat_intro_edit_field($term) {
	$val = get_term_meta($term->term_id, 'gm_cat_intro', true);
	?>
	<tr class="form-field">
		<th><label for="gm_cat_intro">Category Intro Text</label></th>
		<td>
			<textarea name="gm_cat_intro" id="gm_cat_intro" rows="4" style="width:100%"><?php echo esc_textarea($val); ?></textarea>
			<p class="description">Short intro paragraph shown on the category archive page.</p>
		</td>
	</tr>
	<?php
}

add_action('created_domain_cat', 'gm_save_cat_intro');
add_action('edited_domain_cat', 'gm_save_cat_intro');
function gm_save_cat_intro($term_id) {
	if (isset($_POST['gm_cat_intro'])) {
		update_term_meta($term_id, 'gm_cat_intro', sanitize_textarea_field($_POST['gm_cat_intro']));
	}
}
