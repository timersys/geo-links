<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;?>

<table class="form-table">

	<?php do_action( 'geol/metaboxes/before_url', $opts ); ?>

	<tr valign="top">
		<th><label for="geol_trigger"><?php _e( 'Link Slug', 'geol' ); ?></label></th>
		<td id="source">
			<input type="text" id="source_slug" class="widefat" name="geol[source_slug]" value="<?php echo $opts['source_slug']; ?>"  />
			<span id="source_msg"></span>
			<p class="help"><?php echo site_url( $settings['goto_page'] ); ?>/<span><?php echo $opts['source_slug']; ?></span></p>
		</td>
	</tr>

	<?php do_action( 'geol/metaboxes/before_url', $opts ); ?>

</table>

<h3><?php _e( 'Destinations', 'geol' ); ?></h3>

<?php if( $opts['dest'] ) : ?>
	<?php foreach($opts['dest'] as $key => $data) : //$key = 'dest_'.$i; ?>

		<table class="form-table geol_repeater">
		<tr id="<?php echo $key; ?>">
			<td>
				<div class="geol_border">
				<table class="form-table">

					<?php do_action( 'geol/metaboxes/before_repeater', $opts ); ?>

					<tr valign="top">
						<th><label for="geol_trigger"><?php _e( 'Destination URL', 'geol' ); ?></label></th>
						<td>
							<input type="text" class="widefat" name="geol[dest][<?php echo $key; ?>][url]" value="<?php echo esc_attr($data['url']); ?>" placeholder="<?php _e('Enter you destination url', 'geol'); ?>"  />
						</td>
					</tr>

					<tr valign="top">
						<th><label for="geol_trigger"><?php _e( 'Geo Options', 'geol' ); ?></label></th>
						<td>
							<input type="text" class="widefat geol_options" name="geol[dest][<?php echo $key; ?>][country]" value="<?php echo esc_attr($data['country']); ?>" placeholder="<?php _e('Enter a country', 'geol'); ?>" />

							<input type="text" class="widefat geol_options" name="geol[dest][<?php echo $key; ?>][state]" value="<?php echo esc_attr($data['state']); ?>" placeholder="<?php _e('Enter a state', 'geol'); ?>" />

							<input type="text" class="widefat geol_options" name="geol[dest][<?php echo $key; ?>][city]" value="<?php echo esc_attr($data['city']); ?>" placeholder="<?php _e('Enter a city', 'geol'); ?>" />
						</td>
					</tr>

					<tr valign="top">
						<th><label for="geol_trigger"><?php _e( 'Device', 'geol' ); ?></label></th>
						<td>
							<select class="widefat selectized geol_device" name="geol[dest][<?php echo $key; ?>][device]" value="<?php echo esc_attr($data['device']); ?>" placeholder="<?php _e('Enter a device', 'geol'); ?>">
								<option value="all"><?php _e('All Devices','geol'); ?></option>
								<?php foreach( $devices as $key_dev => $name_dev ) : ?>
									<option value="<?php echo $key_dev; ?>" <?php selected($data['device'],$key_dev) ?>><?php echo $name_dev; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th><label for="geol_trigger"><?php _e( 'Referrer URL', 'geol' ); ?></label></th>
						<td>
							<input type="text" class="widefat" name="geol[dest][<?php echo $key; ?>][ref]" value="<?php echo esc_attr($data['ref']); ?>"  />
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