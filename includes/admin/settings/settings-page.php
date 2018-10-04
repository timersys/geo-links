<div class="wrap geol-settings">
	<h2>Geo Links v <?= GEOL_VERSION;?></h2>
	<form name="geol-settings" method="post" enctype="multipart/form-data">
		<table class="form-table">
			<tr valign="top" class="">
				<th colspan="2"><h3><?php _e( 'Geo Links settings:', 'geol' ); ?></h3></th>
				<td colspan="2">
				</td>
			</tr>
			<tr valign="top" class="">
				<th><label for="page_goto"><?php _e( 'Goto Page', 'geol'); ?></label></th>
				<td colspan="3">
					<input type="text" id="goto_page" name="geol_settings[goto_page]" value="<?php echo $opts['goto_page']; ?>" />
					<p class="help"><?php printf(__( 'This page will be used to redirect the links: %s', 'geol'), $opts['goto_url'].'{{ link }}' ); ?></p>
				</td>
			</tr>

			<tr><td><input type="submit" class="button-primary" value="<?php _e( 'Save settings', 'geol' );?>"/></td>
				<?php wp_nonce_field('geol_save_settings','geol_nonce'); ?>
		</table>
	</form>
</div>
