<div class="c-sb-section">
	<div class="c-sb-section__body">
		<ul class="c-sb-list c-sb-list">
			<?php foreach ($customers as $customer_id => $customer): ?>
				<li class="c-sb-list-item">
					<i class="c-sb-list-item__icon icon icon-avatar" aria-hidden="true"></i>
					<span class="c-sb-list-item__label">
						<strong><a href="<?= $customer['link'] ?>"><?= $customer['name'] ?> (#<?= $customer_id ?>)</a></strong>
						<span class="c-sb-list-item__text t-tx-charcoal-200">WP User: <a href="<?= $customer['user_link'] ?>">#<?= $customer['user_id'] ?></a></span>
					</span>
				</li>
  			<?php endforeach ?>
		</ul>
	</div>
</div>