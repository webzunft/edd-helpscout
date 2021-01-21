<div class="c-sb-section c-sb-section--toggle">
	<div class="c-sb-section__title js-sb-toggle">
		<i class="icon-cart icon-sb"></i> Orders
		<i class="caret sb-caret"></i>
	</div>
	<div class="c-sb-section__body">
		<ul class="c-sb-list c-sb-list--compact">
			<?php foreach ($orders as $order_id => $order): ?>
				<li class="c-sb-list-item">
					<span class="c-sb-list-item__text t-tx-charcoal-500">
					<a href="<?= $order['link'] ?>">#<?= $order['id'] ?></a> - <?= $order['total'] ?> - <?= $order['payment_method'] ?> - <?= $order['date'] ?>
					</span>
				</li>
				<?php foreach ($order['items'] as $item): ?>
					<li class="c-sb-list-item--bullet">
						<strong><?= $item['title'] ?></strong>
						<?php if (!empty($item['price_option'])): ?> - <?= $item['price_option'] ?><?php endif ?>
					</li>
				<?php endforeach ?>
  				<li role="separator" class="c-sb-list-divider"></li>
  			<?php endforeach ?>
		</ul>
	</div>
</div>
