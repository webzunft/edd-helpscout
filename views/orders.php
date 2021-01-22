<div class="c-sb-section c-sb-section--toggle">
	<div class="c-sb-section__title js-sb-toggle">
		<i class="icon-cart icon-sb"></i> Orders
		<i class="caret sb-caret"></i>
	</div>
	<div class="c-sb-section__body">
		<ul class="c-sb-list c-sb-list--compact">
			<?php foreach ($orders as $order_id => $order): ?>
				<?php do_action( 'edd_helpscout_before_order_list_item', $order, $helpscout_data ); ?>
				<li class="c-sb-list-item">
					<span class="c-sb-list-item__text t-tx-charcoal-500">
					<a href="<?= $order['url'] ?>">#<?= $order['id'] ?></a> - <?= $order['total'] ?> <span class="badge <?= $order['status_color'] ?>"><?= $order['status_label'] ?></span>
					</span>
				</li>
				<li class="c-sb-list-item">
					<span class="c-sb-list-item__label t-tx-charcoal-500">
						<?php do_action( 'edd_helpscout_order_list_item_data_start', $order, $helpscout_data ); ?>
						<span class="c-sb-list-item__text t-tx-charcoal-500">
							<?= $order['payment_method'] ?> - <?= $order['date'] ?>
						</span>
						<?php do_action( 'edd_helpscout_order_list_item_data_end', $order, $helpscout_data ); ?>
					</span>
				</li>
				<?php foreach ($order['items'] as $item): ?>
					<li class="c-sb-list-item--bullet">
						<strong><?= $item['title'] ?></strong>
						<?php if (!empty($item['price_option'])): ?> - <?= $item['price_option'] ?><?php endif ?>
					</li>
				<?php endforeach ?>
				<?php do_action( 'edd_helpscout_after_order_list_item', $order, $helpscout_data ); ?>
				<li role="separator" class="c-sb-list-divider"></li>
			<?php endforeach ?>
		</ul>
	</div>
</div>
