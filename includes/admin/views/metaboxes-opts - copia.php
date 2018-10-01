<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;?>

<table class="form-table">

	<?php do_action( 'geol/metaboxes/before_url', $opts ); ?>

	<tr valign="top">
		<th><label for="geol_trigger"><?php _e( 'Link Slug', $domain ); ?></label></th>
		<td>
			<input type="text" class="widefat" name="geol[source_url]" min="0" value="<?php echo esc_attr($opts['source_url']); ?>"  />
			<p class="help"><?php echo site_url('goto/'); ?></p>
		</td>
	</tr>

	<?php do_action( 'geol/metaboxes/before_url', $opts ); ?>

</table>

<h3><?php _e( 'Destinations', $domain ); ?></h3>

<?php if( $opts['dest'] ) : ?>
	<?php foreach($opts['dest'] as $i => $data) : ?>

		<table class="form-table">
			<tr>
				<td>

		<div class="geol_repeater">
			<table class="form-table">

				<?php do_action( 'geol/metaboxes/before_repeater', $opts ); ?>

				<tr valign="top">
					<th><label for="geol_trigger"><?php _e( 'Destination URL', $domain ); ?></label></th>
					<td>
						<input type="text" class="widefat" name="geol[dest][<?php echo $i; ?>][url]" value="<?php echo esc_attr($data['url']); ?>"  />
					</td>
				</tr>

				<tr valign="top">
					<th><label for="geol_trigger"><?php _e( 'Geo Options', $domain ); ?></label></th>
					<td>
						<input type="text" class="widefat" name="geol[dest][<?php echo $i; ?>][country]" value="<?php echo esc_attr($data['country']); ?>"  />

						<input type="text" class="widefat" name="geol[dest][<?php echo $i; ?>][state]" value="<?php echo esc_attr($data['state']); ?>"  />

						<input type="text" class="widefat" name="geol[dest][<?php echo $i; ?>][city]" value="<?php echo esc_attr($data['city']); ?>"  />
					</td>
				</tr>

				<tr valign="top">
					<th><label for="geol_trigger"><?php _e( 'Referrer URL', $domain ); ?></label></th>
					<td>
						<input type="text" class="widefat" name="geol[dest][<?php echo $i; ?>][ref]" value="<?php echo esc_attr($data['ref']); ?>"  />
					</td>
				</tr>

				<?php do_action( 'geol/metaboxes/after_repeater', $opts ); ?>
			</table>
		</div>

		</td>
		<td>
			<a href="" class="geol_symbol geol_plus">+</a>
			<?php if( $i != 0 ) : ?>
				<a href="" class="geol_symbol geol_less">-</a>
			<?php endif; ?>
		</td>
		</tr>
	</table>
	
	<?php endforeach; ?>
<?php endif; ?>

<?php wp_nonce_field( 'geol_options', 'geol_options_nonce' ); ?>