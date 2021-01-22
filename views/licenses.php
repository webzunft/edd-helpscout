<div class="c-sb-section c-sb-section--toggle nested open">
	<div class="c-sb-section__title js-sb-toggle">
		<i class="icon-folder icon-sb"></i> Licenses
		<i class="caret sb-caret"></i>
	</div>
	<div class="c-sb-section__body">
		<?php foreach ($licenses as $license_id => $license): ?>
			<ul class="c-sb-list c-sb-list--compact">
				<?php if ( $license['show_activations'] ): ?>
					<li class="c-sb-list-item" style="font-size: 80% !important;">
						<span class="badge <?= $license['status_color'] ?>"><?= $license['status'] ?></span>&nbsp;
						<?= $license['is_expired'] ? __('Expired') : __('Expires'); ?>: <?= $license['expires'] ?>
					</li>
				<?php endif ?>

				<li class="c-sb-list-item"><strong><?= $license['title'] ?></strong></li>
				<li class="c-sb-list-item" style="font-size: 80% !important;">
					<?php if (!empty($license['price_option'])): ?><?= $license['price_option'] ?> - <?php endif ?>
					<?= $license['key'] ?>
					<a href="<?= $license['url']?>" target="_blank"><span class="icon-gear"></span></a>
				</li>
			</ul>
			<div style="margin-left: 18px">
			<?php foreach ($license['children'] as $child_license_id => $child_license): ?>
				<ul class="c-sb-list c-sb-list--compact">
					<?php if ( $child_license['show_activations'] ): ?>
						<li class="c-sb-list-item" style="font-size: 80% !important;">
							<span class="badge <?= $child_license['status_color'] ?>"><?= $child_license['status'] ?></span>&nbsp;
							<?= $child_license['is_expired'] ? __('Expired') : __('Expires'); ?>: <?= $child_license['expires'] ?>
						</li>
					<?php endif ?>
					<li class="c-sb-list-item"><strong><?= $child_license['title'] ?></strong></li>
					<li class="c-sb-list-item" style="font-size: 80% !important;">
						<?php if (!empty($child_license['price_option'])): ?><?= $child_license['price_option'] ?> - <?php endif ?>
						<?= $child_license['key'] ?>
						<a href="<?= $child_license['url']?>" target="_blank"><span class="icon-gear"></span></a>
					</li>
				</ul>
				<?php if ( $child_license['show_activations'] ): ?>
					<div class="c-sb-section c-sb-section--toggle nested">
						<div class="c-sb-section__title js-sb-toggle">
							Active sites (<?= $child_license['activation_count'] ?>/<?= $child_license['limit'] ?>) <i class="caret sb-caret"></i>
						</div>
						<div class="c-sb-section__body">
							<ul class="c-sb-list c-sb-list--compact">
								<?php foreach ($child_license['sites'] as $site): ?>
									<li class="c-sb-list-item--bullet">
										<a class="c-sb-list-item__link t-tx-blue-500" href="https://<?= $site ?>"><?= $site ?></a>
									</li>
								<?php endforeach ?>
							</ul>
						</div>
					</div>
				<?php endif ?>
			<?php endforeach ?>
			</div>
			<?php if (!empty($license['upgrades'])): ?>
				<div class="c-sb-section c-sb-section--toggle nested">
					<div class="c-sb-section__title js-sb-toggle">
						Upgrades <i class="caret sb-caret"></i>
					</div>
					<div class="c-sb-section__body">
						<ul class="c-sb-timeline c-sb-timeline--list u-mrg-0">
							<?php foreach ($license['upgrades'] as $upgrade_id => $upgrade): ?>
								<li class="c-sb-timeline-item">
									<span class="c-sb-timeline-item__text">
										<a class="c-sb-timeline-item__link t-tx-blue-500" href="<?= $upgrade['url'] ?>">
											<?= $upgrade['title'] ?> - <?= $upgrade['price_option'] ?>
										</a>
										<span class="c-sb-list-item__text__secondary t-tx-charcoal-400">
											<?= $upgrade['price'] ?>
										</span>
									</span>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			<?php endif ?>
			<?php if ( $license['show_activations'] ): ?>
				<div class="c-sb-section c-sb-section--toggle nested">
					<div class="c-sb-section__title js-sb-toggle">
						Active sites (<?= $license['activation_count'] ?>/<?= $license['limit'] ?>) <i class="caret sb-caret"></i>
					</div>
					<div class="c-sb-section__body">
						<ul class="c-sb-list c-sb-list--compact">
							<?php foreach ($license['sites'] as $site): ?>
								<li class="c-sb-list-item--bullet">
									<a class="c-sb-list-item__link t-tx-blue-500" href="https://<?= $site ?>"><?= $site ?></a>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			<?php endif ?>
			<?php if ( $license['is_expired'] ): ?>
				<div class="c-sb-section">
					<a href="<?= $license['renewal_url'] ?>"><?= __('Renew') ?></a>
				</div>
			<?php endif ?>
			<div class="divider"></div>
		<?php endforeach ?>
	</div>
</div>

