<div class="c-sb-section c-sb-section--toggle">
	<div class="c-sb-section__title js-sb-toggle" style="font-size: 15px; padding: 6px 0 10px 0;">
		<i class="icon-star icon-sb" style="font-size: 19px; margin-right: 3px;"></i>Subscriptions<i class="caret sb-caret" style="margin-top: 4px;"></i>
	</div>
	<div class="c-sb-section__body">
		<ul class="c-sb-list c-sb-list--compact">
			<?php foreach ($subscriptions as $subscription_id => $subscription): ?>
				<li class="c-sb-list-item--bullet">
					<span class="badge <?= $subscription['status_color'] ?>">
						<a href="<?= $subscription['url'] ?>" class="t-tx-white" title="<?= $subscription['status_label'] ?>">#<?= $subscription_id ?></a>
					</span> - <?= $subscription['title'] ?>
				</li>
  			<?php endforeach ?>
		</ul>
	</div>
</div>
