<div class="c-sb-section c-sb-section--toggle <?= $toggle ?>">
	<div class="c-sb-section__title js-sb-toggle" style="font-size: 15px; padding: 6px 0 10px 0;">
		<i class="icon-cart icon-sb" style="font-size: 19px; margin-right: 3px; top: 4px;"></i>Orders<i class="caret sb-caret" style="margin-top: 4px;"></i>
	</div>
	<div class="c-sb-section__body">
		<ul class="c-sb-list c-sb-list--compact" style="padding-bottom: 0;">
			<?php foreach ($orders as $order_id => $order): ?>
				<?php do_action( 'edd_helpscout_before_order_list_item', $order, $helpscout_data ); ?>
				<li class="c-sb-list-item" style="padding-top: 16px;">
					<span class="c-sb-list-item__text t-tx-charcoal-500" style="font-size: 85%;">
					<span class="badge <?= $order['status_color'] ?>" style="font-size: 85%; padding: 3px 4px; margin: -1px 4px 0 0;"><?= $order['status_label'] ?></span> <a href="<?= $order['url'] ?>">#<?= $order['id'] ?></a> - <?= $order['total'] ?>
					</span>
				</li>
				<?php foreach ($order['items'] as $item): ?>
					<li class="c-sb-list-item c-sb-list-item--bullet" style="list-style-type: circle; list-style-position: outside; margin-left: 1.2em;padding: 4px 0 6px 0;">
						<span class="c-sb-list-item__label t-tx-charcoal-500" style="padding-bottom: 4px;">
							<strong style="font-size: 14px;"><?= $item['title'] ?></strong>
							<?php if (!empty($item['price_option'])): ?>
								<span class="c-sb-list-item__text t-tx-charcoal-500" style="font-size: 11px;"><?= $item['price_option'] ?></span>
							<?php endif ?>
						</span>
					</li>
				<?php endforeach ?>
				<li class="c-sb-list-item">
					<?php do_action( 'edd_helpscout_order_list_item_data_start', $order, $helpscout_data ); ?>
					<span class="c-sb-list-item__text t-tx-charcoal-500" style="font-size: 11px;">
					<?= $order['date'] ?> - <?= $order['payment_method'] ?>
					</span>
					<?php do_action( 'edd_helpscout_order_list_item_data_end', $order, $helpscout_data ); ?>
				</li>
				<?php do_action( 'edd_helpscout_after_order_list_item', $order, $helpscout_data ); ?>
				<li role="separator" class="c-sb-list-divider" style="margin-bottom: -1px; padding-top: 16px;"></li>
			<?php endforeach ?>
		</ul>
	</div>
</div>
