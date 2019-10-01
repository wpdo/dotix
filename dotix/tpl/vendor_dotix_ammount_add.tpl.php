<?php defined( 'WPINC' ) || exit ; ?>

<div class="form-field term-group">
	<label for="dotix"><?php echo __( 'Dotix Credit Unit', 'dotix' ) ; ?></label>
	<input type="text" name="dotix" />
	<p>
		<?php echo __( 'Specify how many credits this allowance indicates.', 'dotix' ) ; ?>
		<?php echo sprintf( __( 'Use %s to allow consume all the credits one time.', 'dotix' ), '<code>max</code>' ) ; ?>
	</p>
</div>