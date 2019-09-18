<?php
// See if there's a media id already saved as post meta
$all_options = get_option(OPTION_NAME);

// var_export($all_options);

define('ALL_POST_TYPES', get_post_types([
   'public'   => true,
]));

function dfi_print_row($selected_type = null, $selected_img_id = null)
{
    ?>
	<div class="js-dfi-row js-dfi-group">
		<select name="type" class="type-selector">
			<?php foreach (ALL_POST_TYPES as $type) : ?>
		     	<option 
				 	<?php echo $selected_type == $type ?  "selected" : null; ?>  
					value="<?php echo $type; ?>"><?php echo $type; ?>
					</option>
			 <?php endforeach; ?>
		</select>

		<?php
            if ($selected_img_id) {
                $img_url = wp_get_attachment_url($selected_img_id);
                $btn_txt = "<img src='$img_url' style='max-width:40px;' />";
            } else {
                $btn_txt = 'Upload image';
            } ?>
		<button class="upload_image_button button" value="<?php echo $selected_img_id ?>"><?php echo $btn_txt ?></button>
		<button class="remove_image_button button">remove</button>
	</div>
<?php
}

if (is_array($all_options)) {
    foreach ($all_options as $selected_type => $selected_img_id) {
        dfi_print_row($selected_type, $selected_img_id);
    }
}

dfi_print_row();

?>

<button id="dfi-add-row" class="button">add row</button>
<button id="dfi-save" class="button-primary">save</button>