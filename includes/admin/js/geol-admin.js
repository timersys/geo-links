jQuery(document).on('click', 'table.geol_repeater a.geol_plus', function(e) {
	e.preventDefault();

	//vars
	var $tr = jQuery(this).closest('tr'),
		$add = $tr.clone(),
		old_id = $add.attr('id'),
		new_id = 'dest_' + ( parseInt( old_id.replace('dest_', ''), 10 ) + 1);


	$add.find('.selectize-control').remove();

	$add.find('.geol_countries').selectize({
				options : geol_var.countries,
				plugins: ['remove_button'],
			});

	$add.find('.geol_regions').selectize({
				options : geol_var.regions,
				plugins: ['remove_button'],
			});

	// update names
	$add.find('[name]').each(function(){
		jQuery(this).attr('name', jQuery(this).attr('name').replace( old_id, new_id ));
	});

	// update data-i
	$add.attr( 'id', new_id );

	// add tr
	$tr.after( $add );
});


jQuery(document).on('click', 'table.geol_repeater a.geol_less', function(e) {
	e.preventDefault();

	//vars
	jQuery(this).closest('tr').remove();
});


jQuery(document).on('keyup','td#source input', function(e) {
	var slug = jQuery(this).val();
	jQuery('td#source p.help span').html(slug);
});


jQuery(document).on('keypress','td#source input', function(e) {

	if (!/[a-z0-9_-]/i.test(e.key))
		return false;
});


jQuery(document).on('focusout','input#source_slug', function() {

	var source_slug = jQuery(this).val();

	jQuery.post(geol_var.ajax_url, 	{
				action: 'geol_source',
				slug : source_slug,
				wpnonce: geol_var.nonce,
				exclude: geol_var.post_id
			},
			function(response) {
				var style;

				if( response.type == 'success' )
					style = 'color:green;';
				else
					style = 'color:red;';

				msg_total = '<span style="'+ style +'">\
								<span class="dashicons '+ response.icon +'"></span>'+ response.msg +'\
							<span>';

				jQuery('span#source_msg').html(msg_total);
		});
});