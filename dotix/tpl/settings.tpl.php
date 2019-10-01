<?php
namespace dotix;

defined( 'WPINC' ) || exit;

?>
<style>
	.dotix-settings .field-col {
		display: inline-block;
		margin-right: 20px;
	}

	.dotix-settings .field-col-desc{
		min-width: 540px;
		max-width: calc(100% - 640px);
		vertical-align: top;
	}

	.dotix-h2 {
		display: inline-block;
	}

	.dotix-desc {
		font-size: 12px;
		font-weight: normal;
		color: #7a919e;
		margin: 10px 0;
		line-height: 1.7;
		max-width: 840px;
	}
</style>
<div class="wrap dotix-settings">
	<h2 class="dotix-h2"><?php echo __( 'Dotix Settings', 'dotix' ); ?></h2>
	<span class="dotix-desc">
		v<?php echo Core::VER ; ?>
	</span>

	<hr class="wp-header-end">

	<form method="post" action="<?php menu_page_url( 'dotix' ); ?>" class="dotix-relative">
	<?php wp_nonce_field( 'dotix' ); ?>

	<table class="form-table">
		<tr>
			<th scope="row" valign="top"><?php echo __( 'QR Code', 'dotix' ); ?></th>
			<td>
				<p><label><input type="checkbox" name="qrcode" value="1" <?php echo Conf::val( 'qrcode' ) ? 'checked' : '' ; ?> /> <?php echo __( 'Enable', 'dotix' ); ?></label></p>
				<p><?php echo __( 'QRCode size', 'dotix' ); ?>: <input type="text" size="3" maxlength="4" name="qrcode_size" value="<?php echo Conf::val( 'qrcode_size' ); ?>" /></p>
				<p class="description">
					<?php echo __( 'This will change the QR Code size shown in the order detail page.', 'dotix' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'Credit title', 'dotix' ); ?></th>
			<td>
				<p><input type="text" size="40" name="credit_title" value="<?php echo Conf::val( 'credit_title' ); ?>" /></p>
				<p class="description">
					<?php echo __( 'Rename the credit title.', 'dotix' ); ?>
				</p>
			</td>
		</tr>

		<tr>
			<th scope="row" valign="top"><?php echo __( 'Auto Upgrade', 'dotix' ); ?></th>
			<td>
				<p><label><input type="checkbox" name="auto_upgrade" value="1" <?php echo Conf::val( 'auto_upgrade' ) ? 'checked' : '' ; ?> /> <?php echo __( 'Enable', 'dotix' ); ?></label></p>
				<p class="description">
					<?php echo __( 'Enable this option to get the latest features at the first moment.', 'dotix' ); ?>
				</p>
			</td>
		</tr>

	</table>

	<p class="submit">
		<?php submit_button(); ?>
	</p>
	</form>
</div>

<div class="wrap dotix-settings">
	<h3><?php echo __( 'Consume Log', 'dotix' ); ?></h3>

	<table class="wp-list-table widefat striped">
		<thead>
		<tr>
			<th>#</th>
			<th><?php echo __( 'Date', 'dotix' ); ?></th>
			<th><?php echo __( 'App ID', 'dotix' ); ?></th>
			<th><?php echo __( 'order ID', 'dotix' ); ?></th>
			<th><?php echo __( 'Number Consumed', 'dotix' ); ?></th>
			<th><?php echo __( 'Number Left', 'dotix' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $this->log() as $v ) : ?>
			<tr>
				<td><?php echo $v->id ; ?></td>
				<td><?php echo Util::readable_time( $v->dateline ); ?></td>
				<td><?php echo $v->app_id ; ?></td>
				<td><?php echo $v->order_id ; ?></td>
				<td><?php echo $v->num_consumed ; ?></td>
				<td><?php echo $v->num_left ; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>

