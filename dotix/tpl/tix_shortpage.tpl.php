<?php defined( 'WPINC' ) || exit ; ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" />

<div class="container-fluid">
	<div class="row text-center">


		<div class="col-12 card border-<?php echo $color ; ?> mx-auto my-3">
			<div class="card-header h1 text-<?php echo $color ; ?>" style="font-size: 10vmin;">Tickets Left</div>
			<div class="card-body text-<?php echo $color ; ?>">
				<h5 class="card-title" style="font-size: 40vmin;"><?php echo $bal ; ?></h5>
				<h6 class="text-primary">Order ID: <b><?php echo $order_id ?></b></h6>
				<?php if ( $status != 'completed' ) : ?>
				<p class="card-text text-danger">Tickets are not available due to order status: <b><?php echo $status ; ?></b>.</p>
				<?php endif ; ?>
			</div>
		</div>

		<div class="col-12">
			<?php $this->qrcode( $_GET[ 'qrtix' ], 15 ) ; ?>
		</div>
	</div>
</div>
