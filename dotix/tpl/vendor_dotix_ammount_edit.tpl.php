<?php defined( 'WPINC' ) || exit ; ?>

<tr class="form-field term-group-wrap">
	<th scope="row"><label for="dotix"><?php echo __( 'Dotix Credit Unit', 'dotix' ); ?></label></th>
	<td>
		<input type="text" name="dotix" value="<?php echo $curr ; ?>" />
		<p class="description">
			<?php echo __( 'Specify how many credits this allowance indicates.', 'dotix' ) ; ?>
			<?php echo sprintf( __( 'Use %s to allow consume all the credits one time.', 'dotix' ), '<code>max</code>' ) ; ?>
		</p>

	</td>
</tr>