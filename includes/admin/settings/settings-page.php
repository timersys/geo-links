<div class="wrap geol-settings">
	<h2>Geo Links v <?= GEOL_VERSION; ?></h2>
	<form name="geol-settings" method="post" enctype="multipart/form-data">
		<table class="form-table">
			<tr valign="top" class="">
				<th><label for="page_goto"><?php _e( 'Redirection slug', 'geol' ); ?></label></th>
				<td colspan="3">
					<input type="text" id="goto_page" name="geol_settings[goto_page]"
					       value="<?php echo $opts['goto_page']; ?>"/>
					<p class="help"><?php printf( __( 'The slug that will proceed your geolinks: %s', 'geol' ), site_url( $opts['goto_page'] ) . '/{{ geo-link }}' ); ?></p>
				</td>
			</tr>
			<tr valign="top" class="">
				<th><label for="opt_stats"><?php _e( 'Enable Stats', 'geol' ); ?></label></th>
				<td colspan="3">
					<input type="checkbox" name="geol_settings[opt_stats]" value="1" <?php checked($opts['opt_stats'],'1') ?> />
				</td>
			</tr>

			<tr>
				<td><input type="submit" class="button-primary" value="<?php _e( 'Save settings', 'geol' ); ?>"/></td>
				<?php wp_nonce_field( 'geol_save_settings', 'geol_nonce' ); ?>
		</table>
	</form>
</div>
