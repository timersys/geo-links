<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table class="form-table">

	<?php do_action( 'geol/metaboxes/before_source', $opts ); ?>

	<tr valign="top">
		<th><label for="source_slug"><?php _e( 'Link Slug', 'geol' ); ?></label></th>
		<td id="source">
			<input type="text" id="source_slug" class="widefat" name="geol[source_slug]"
			       value="<?php echo isset($opts['source_slug']) ? esc_attr($opts['source_slug']) : ''; ?>"/>
			<span id="source_msg"></span>
			<p class="help"><?php echo site_url( $settings['goto_page'] ); ?>/<span><?php echo $opts['source_slug']; ?></span></p>
		</td>
	</tr>

	<tr valign="top">
		<th><label for="status_code"><?php _e( 'Redirection code', 'geol' ); ?></label></th>
		<td id="code">
			<input type="number" id="status_code" class="widefat" name="geol[status_code]"
			       value="<?php echo isset($opts['status_code']) ? esc_attr($opts['status_code']) : ''; ?>"/>
			<p class="help"><?php _e('Add redirection code. Default to 302','geol'); ?></p>
		</td>
	</tr>

	<?php do_action( 'geol/metaboxes/after_source', $opts ); ?>

</table>

<h3><?php _e( 'Destinations', 'geol' ); ?></h3>

<?php if ( $opts['dest'] ) : ?>

	<?php foreach ( $opts['dest'] as $key => $data ) : //$key = 'dest_'.$i; ?>

	<table class="form-table geol_repeater">
	<tr id="<?php echo $key; ?>">
		<td>
			<div class="geol_border">
				<table class="form-table">

					<?php do_action( 'geol/metaboxes/before_repeater', $opts ); ?>

					<tr valign="top">
						<th><label for="geol_dest"><?php _e( 'Destination URL', 'geol' ); ?></label></th>
						<td>
							<input type="text" class="widefat" name="geol[dest][<?php echo $key; ?>][url]"
							       value="<?php echo isset($data['url']) ? esc_attr( $data['url'] ) : ''; ?>"
							       placeholder="<?php _e( 'Enter you destination url', 'geol' ); ?>"/>
							<p class="help-text"><?php _e( 'Where the user is going to be redirected if rules below match', 'geol' ); ?></p>
						</td>
					</tr>

					<tr valign="top">
						<th colspan="2"><?php _e( 'Geo Options', 'geol' ); ?></label></th>
					</tr>

					<tr valign="top">
						<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Countries', 'geol' ); ?></label></th>
						<td>
							<select name="geol[dest][<?php echo $key; ?>][countries][]" class="geot-chosen-select-multiple geol_countries" placeholder="<?php _e( 'Choose one or more countries', 'geol' ); ?>" multiple="multiple">
							<?php foreach ($countries as $c) : ?>
								<option value="<?php echo $c->iso_code; ?>" <?php isset( $data['countries'] ) && is_array( $data['countries'] ) ? selected(true, in_array( $c->iso_code, $data['countries']) ) :''; ?>> <?php echo $c->country; ?></option>
							<?php endforeach; ?>
							</select>
							<p class="help-text" style="margin-top: -20px;"><?php _e( 'Choose one or more countries', 'geol' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Regions', 'geol' ); ?></label></th>
						<td>
							<select name="geol[dest][<?php echo $key; ?>][regions][]" class="geot-chosen-select-multiple geol_regions" placeholder="<?php _e( 'Choose one or more regions', 'geol' ); ?>" multiple="multiple">
								<?php if( !empty( $geowp['region'] ) ) : ?>
									<?php foreach ( $geowp['region'] as $region ) : ?>
								
									<option value="<?php echo $region['name']; ?>" <?php isset( $data['regions'] ) && is_array( $data['regions'] ) ? selected(true, in_array( $region['name'], $data['regions']) ) :''; ?>> <?php echo $region['name']; ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<p class="help-text" style="margin-top: -20px;"><?php _e( 'Choose one or more regions', 'geol' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Cities', 'geol' ); ?></label></th>
						<td>
							<input type="text" class="widefat"
							       name="geol[dest][<?php echo $key; ?>][cities]"
							       value="<?php echo isset($data['cities']) ? esc_attr( $data['cities'] ) : ''; ?>"
							       placeholder="<?php _e( 'Cities / Regions', 'geol' ); ?>"/>
							<p class="help-text"><?php _e( 'Type city names or city regions, comma separated', 'geol' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'States', 'geol' ); ?></label></th>
						<td>
							<input type="text" class="widefat"
							       name="geol[dest][<?php echo $key; ?>][states]"
							       value="<?php echo isset($data['states']) ? esc_attr( $data['states'] ) : ''; ?>"
							       placeholder="<?php _e( 'States', 'geol' ); ?>"/>
							<p class="help-text"><?php _e( 'Type state iso codes, comma separated', 'geol' ); ?></p>
						</td>

					</tr>

					<tr valign="top">
						<th><label for="geol_trigger"><?php _e( 'Device', 'geol' ); ?></label></th>
						<td>
							<select class="widefat selectized geol_device"
							        name="geol[dest][<?php echo $key; ?>][device]"
							        value="<?php echo esc_attr( $data['device'] ); ?>"
							        placeholder="<?php _e( 'Enter a device', 'geol' ); ?>">
								<option value="all"><?php _e( 'All Devices', 'geol' ); ?></option>
								<?php foreach ( $devices as $key_dev => $name_dev ) : ?>
									<option value="<?php echo $key_dev; ?>" <?php selected( $data['device'], $key_dev ) ?>><?php echo $name_dev; ?></option>
								<?php endforeach; ?>
							</select>
							<p class="help-text"><?php __( 'Only redirect if using this device', 'geolinks' ); ?></p>
						</td>
					</tr>

					<tr valign="top">
						<th><label for="geol_trigger"><?php _e( 'Referrer URL', 'geol' ); ?></label></th>
						<td>
							<input type="text" class="widefat" name="geol[dest][<?php echo $key; ?>][ref]"
							       value="<?php echo esc_attr( $data['ref'] ); ?>"/>
							<p class="help-text"><?php __( 'Only redirect if user coming from this url', 'geolinks' ); ?></p>
						</td>
					</tr>

					<?php do_action( 'geol/metaboxes/after_repeater', $opts ); ?>
				</table>
			</div>
		</td>
		<td style="padding:1px;">
			<a href="" class="button geol_plus" title="Add">+ ADD</a>
		</td>
		<td style="padding:1px;">
			<a href="" class="geol_symbol geol_less" title="Remove">-</a>
		</td>
	</tr>
	</table>

	<?php endforeach; ?>
<?php endif; ?>

<?php wp_nonce_field( 'geol_options', 'geol_options_nonce' ); ?>