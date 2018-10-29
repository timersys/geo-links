<?php
// nonce in last metabox
wp_nonce_field( 'geol_options', 'geol_options_nonce' );
?>

<?php if ( $opts['dest'] ) : ?>

<table class="wp-list-table widefat striped">
	<thead>
		<tr><th><?php _e('Destinations','geol'); ?></th><th><?php _e('Clicks','geol'); ?></th></tr>
	</thead>
	<tbody>
	<?php foreach ( $opts['dest'] as $key => $data ) : ?>
		<tr>
			<td class="geol_stats_url"><?php echo $data['url']; ?></td>
			<td class="geol_stats_count"><?php echo $data['count_dest']; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<br />
<div style="text-align: right;">
	<span class="geol_msg_reset"></span>
	<button class="button-secondary geol_reset"><?php _e('Reset Stats','geol'); ?></button>
</div>

<?php else : ?>

	<h3><?php _e('Please, first save destinations to see the stats','geol'); ?></h3>

<?php endif; ?>