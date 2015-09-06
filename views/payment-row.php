<?php defined( 'ABSPATH' ) or exit; ?>
<div class="toggleGroup <?php if( $order['is_completed'] ) echo 'open'; ?>">
	<strong><i class="icon-cart"></i> <?php echo $order['link']; ?></strong><a class="toggleBtn"><i class="icon-arrow"></i></a>

	<?php if( $order['is_completed'] ) { ?>
		<a style="float:right" href="<?php echo  esc_url( $order['resend_receipt_link'] ); ?>" target="_blank">
			<i title="<?php echo esc_attr( __( 'Resend Purchase Receipt', 'edd' ) ); ?>" class="icon-doc"></i>
		</a>
	<?php } else { ?>
		<span style="color:orange;font-weight:bold;"> - <?php echo esc_html( $order['status'] ); ?></span>
	<?php } ?>

	<?php if( $order['is_renewal'] ) : ?>
		<span style="color:#008000;font-weight:bold;"> (renewal)</span>
	<?php endif; ?>

	<div class="toggle indent">
		<p>
			<span class="muted"><?php echo $order['date']; ?></span><br/>
			<?php echo trim( edd_currency_filter( $order['amount'] ) ) . ( ( isset( $order['payment_method'] ) && '' !== $order['payment_method'] ) ?  ' - ' . $order['payment_method'] : '' ); ?>
		</p>

		<?php if ( ! empty( $order['downloads'] ) ) : ?>
			<ul class="unstyled">
				<?php foreach( $order['downloads'] as $download ) : ?>
					<li>
						<strong><?php echo get_the_title( $download['id'] ); ?></strong><br />
						<?php echo edd_get_price_option_name( $download['id'], $download['options']['price_id'] ); ?>

						<?php if( ! empty( $download['license'] ) ) : ?>
							<br />
							<a href="<?php echo admin_url( 'edit.php?post_type=download&page=edd-licenses&s=' . $download['license']['key'] ); ?>">
								<?php echo $download['license']['key']; ?>
							</a>
							<?php if( $download['license']['expired'] ) : ?>
								<span style="color:orange; font-weight:bold;">expired</span>
							<?php endif; ?>

							<?php if( ! empty( $download['license']['sites'] ) ) : ?>
								<div class="toggleGroup">
									<a href="" class="toggleBtn"><i class="icon-arrow"></i> Active sites</a>
									<div class="toggle indent">
										<ul class="unstyled">
											<?php foreach( $download['license']['sites'] as $site ) : ?>
												<li>
													<a href="<?php echo esc_url( $site['url'] ); ?>" target="_blank"><?php echo esc_html( $site['url'] ); ?></a>
													<a href="<?php echo esc_url( $site['deactivate_link'] ); ?>" target="_blank"><small>(deactivate)</small></a>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>

</div>

<div class="divider"></div>
