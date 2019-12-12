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
			<?php echo trim( edd_currency_filter( $order['amount'], $order['currency'] ) ) . ( ( isset( $order['payment_method'] ) && '' !== $order['payment_method'] ) ?  ' - ' . $order['payment_method'] : '' ); ?>
		</p>

		<?php if ( ! empty( $order['downloads'] ) ) : ?>
			<?php do_action( 'edd_helpscout_before_order_downloads', $order, $order['downloads'] ); ?>

			<ul class="unstyled">
				<?php foreach( $order['downloads'] as $download ) : ?>
					<li>
						<?php if (isset($download['id'])): // this is the actual download ?>
							<strong><?php echo get_the_title( $download['id'] ); ?></strong><br />
							<?php if( isset( $download['options']['price_id'] ) ) {
									echo edd_get_price_option_name( $download['id'], $download['options']['price_id'] ); 
							} ?>
						<?php endif ?>

						<?php do_action( 'edd_helpscout_before_order_download_details', $order, $download ); ?>

						<?php if( ! empty( $download['license'] ) ) : ?>
							<?php
							$licenses = array( $download['license'] );
							if ( !empty( $download['child_licenses'] ) ) {
								$licenses = array_merge( $licenses, $download['child_licenses'] );
							}
							?>
							<?php foreach ($licenses as $license): ?>
								<?php do_action( 'edd_helpscout_before_order_download_license', $order, $download, $license ); ?>

								<a href="<?php echo esc_url( $license['view_url'] ); ?>" style="display: block;">
									<?php echo $license['key']; ?>
								</a>

								<?php
								if( ! empty( $license['expires_at'] ) ) {
									$suffix = $license['is_expired'] ? 'd' : 's';
									$color = $license['is_expired'] ? 'orange' : '-';
									echo sprintf( '<span class="muted" style="color: %s;">Expire%s at %s</span><br />', $color, $suffix, date( 'Y-m-d', $license['expires_at'] ) );
								}

								if( $license['is_revoked'] ) {
									echo '<span style="color:red; font-weight:bold;"> revoked</span>';
								}

								if( ! empty( $license['sites'] ) ) { ?>
									<div class="toggleGroup nested">
										<a href="" class="toggleBtn"><i class="icon-arrow"></i> Active sites <?php printf( '(%d/%d)', count( $license['sites'] ), $license['limit'] ); ?></a>
										<div class="toggle indent">
											<ul class="unstyled">
												<?php foreach( $license['sites'] as $site ) : ?>
													<li>
														<a href="<?php echo esc_url( $site['url'] ); ?>" target="_blank"><?php echo esc_html( preg_replace( '/^https?:\/\//', '', $site['url'] ) ); ?></a>
														<a href="<?php echo esc_url( $site['deactivate_link'] ); ?>" target="_blank"> <small style="color: red;">(deactivate)</small></a>
													</li>
												<?php endforeach; // end foreach sites ?>
											</ul>
										</div>
									</div>
								<?php
								} // end if sites not empty ?>

								<?php do_action( 'edd_helpscout_after_order_download_license', $order, $download, $license ); ?>
								
							<?php endforeach ?>

						<?php endif; //end if has license ?>

						<?php do_action( 'edd_helpscout_after_order_download_details', $order, $download ); ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php do_action( 'edd_helpscout_after_order_downloads', $order, $order['downloads'] ); ?>

		<?php endif; // endif downloads ?>

		<?php do_action( 'edd_helpscout_after_order_details', $order ); ?>

	</div>

	<?php do_action( 'edd_helpscout_after_order', $order ); ?>

</div>

<div class="divider"></div>
