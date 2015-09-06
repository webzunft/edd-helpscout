<?php defined( 'ABSPATH' ) or exit; ?>
<div class="toggleGroup <?php if( $order['is_completed'] ) echo 'open'; ?>">

	<?php do_action( 'edd_helpscout_before_order', $order ); ?>

	<strong>
		<i class="icon-cart"></i>
		<a target="_blank" href="<?php echo esc_attr( admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id='. $order['payment_id'] ) ); ?>">#<?php echo $order['payment_id']; ?></a>
	</strong>
	<a class="toggleBtn"><i class="icon-arrow"></i></a>

	<?php do_action( 'edd_helpscout_before_order_status', $order ); ?>

	<?php if( $order['is_completed'] ) { ?>
		<a style="float:right" href="<?php echo  esc_url( $order['resend_receipt_link'] ); ?>" target="_blank">
			<i title="<?php echo esc_attr( __( 'Resend Purchase Receipt', 'edd' ) ); ?>" class="icon-doc"></i>
		</a>
	<?php } else { ?>
		<span style="color:orange;font-weight:bold;"> <?php echo esc_html( $order['status'] ); ?></span>
	<?php } ?>

	<?php if( $order['is_renewal'] ) : ?>
		<span style="color:#008000;font-weight:bold;"> (renewal)</span>
	<?php endif; ?>

	<?php do_action( 'edd_helpscout_after_order_status', $order ); ?>

	<div class="toggle indent">

		<?php do_action( 'edd_helpscout_before_order_details', $order ); ?>

		<p>
			<span class="muted"><?php echo $order['date']; ?></span><br/>
			<?php echo trim( edd_currency_filter( $order['amount'] ) ) . ( ( isset( $order['payment_method'] ) && '' !== $order['payment_method'] ) ?  ' - ' . $order['payment_method'] : '' ); ?>
		</p>

		<?php if ( ! empty( $order['downloads'] ) ) : ?>
			<?php do_action( 'edd_helpscout_before_order_downloads', $order, $downloads ); ?>

			<ul class="unstyled">
				<?php foreach( $order['downloads'] as $download ) : ?>
					<li>
						<strong><?php echo get_the_title( $download['id'] ); ?></strong><br />
						<?php echo edd_get_price_option_name( $download['id'], $download['options']['price_id'] ); ?>

						<?php do_action( 'edd_helpscout_before_order_download_details', $order, $download ); ?>

						<?php if( ! empty( $download['license'] ) ) : ?>

							<?php do_action( 'edd_helpscout_before_order_download_license', $order, $download, $download['license'] ); ?>

							<a href="<?php echo admin_url( 'edit.php?post_type=download&page=edd-licenses&s=' . $download['license']['key'] ); ?>">
								<?php echo $download['license']['key']; ?>
							</a>
							<?php if( $download['license']['is_expired'] ) : ?>
								<span style="color:orange; font-weight:bold;"> expired</span>
							<?php endif; ?>

							<?php if( ! empty( $download['license']['sites'] ) ) : ?>
								<div class="toggleGroup">
									<a href="" class="toggleBtn"><i class="icon-arrow"></i> Active sites</a>
									<div class="toggle indent">
										<ul class="unstyled">
											<?php foreach( $download['license']['sites'] as $site ) : ?>
												<li>
													<a href="<?php echo esc_url( $site['url'] ); ?>" target="_blank"><?php echo esc_html( ltrim( $site['url'], 'http://' ) ); ?></a>
													<a href="<?php echo esc_url( $site['deactivate_link'] ); ?>" target="_blank"> <small style="color: red;">(deactivate)</small></a>
												</li>
											<?php endforeach; // end foreach sites ?>
										</ul>
									</div>
								</div>
							<?php endif; // end if sites not empty ?>

							<?php do_action( 'edd_helpscout_after_order_download_license', $order, $download, $download['license'] ); ?>

						<?php endif; //end if has license ?>

						<?php do_action( 'edd_helpscout_after_order_download_details', $order, $download ); ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php do_action( 'edd_helpscout_after_order_downloads', $order, $downloads ); ?>

		<?php endif; // endif downloads ?>

		<?php do_action( 'edd_helpscout_after_order_details', $order ); ?>

	</div>

	<?php do_action( 'edd_helpscout_after_order', $order ); ?>

</div>

<div class="divider"></div>
