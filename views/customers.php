<div class="c-sb-section">
	<div class="c-sb-section__body">
		<ul class="c-sb-list c-sb-list">
			<?php foreach ($customers as $customer_id => $customer): ?>
				<li class="c-sb-list-item">
					<i class="c-sb-list-item__icon icon icon-avatar" aria-hidden="true"></i>
					<span class="c-sb-list-item__label">
						<?php do_action( 'edd_helpscout_customer_list_item_start', $customer, $helpscout_data ); ?>
						<strong><a href="<?= $customer['url'] ?>"><?= $customer['name'] ?> (#<?= $customer_id ?>)</a></strong>
						<?php if ( intval( $customer['user_id'] ) > 0 ): ?>
							<span class="c-sb-list-item__text t-tx-charcoal-200">WP User: <a href="<?= $customer['user_url'] ?>">#<?= $customer['user_id'] ?></a></span>
						<?php endif ?>
						<?php do_action( 'edd_helpscout_customer_list_item_end', $customer, $helpscout_data ); ?>
					</span>
				</li>
				<?php do_action( 'edd_helpscout_after_customer_list_item', $customer, $helpscout_data ); ?>
  			<?php endforeach ?>
		</ul>
	</div>
</div>