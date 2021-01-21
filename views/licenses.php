<div class="c-sb-section c-sb-section--toggle">
	<div class="c-sb-section__title js-sb-toggle">
		<i class="icon-folder icon-sb"></i> Licenses
		<i class="caret sb-caret"></i>
	</div>
	<div class="c-sb-section__body">
		<?php foreach ($licenses as $license_id => $license): ?>
			<ul class="c-sb-list c-sb-list--compact">
				<li class="c-sb-list-item"><strong><?= $license['title'] ?></strong></li>
				<li class="c-sb-list-item">
					<?php if (!empty($license['price_option'])): ?><?= $license['price_option'] ?> - <?php endif ?>
					<?= $license['key'] ?>
					<a href="<?= $license['link']?>" target="_blank"><span class="icon-gear"></span></a>
				</li>
			</ul>
			<?php foreach ($license['children'] as $child_license_id => $child_license): ?>
				<ul class="c-sb-list c-sb-list--compact">
					<li class="c-sb-list-item"><strong><?= $child_license['title'] ?></strong></li>
					<li class="c-sb-list-item">
						<?php if (!empty($child_license['price_option'])): ?><?= $child_license['price_option'] ?> - <?php endif ?>
						<?= $child_license['key'] ?>
						<a href="<?= $child_license['link']?>" target="_blank"><span class="icon-gear"></span></a>
					</li>
				</ul>
				<div class="c-sb-section c-sb-section--toggle">
					<div class="c-sb-section__title js-sb-toggle">
						Active sites (<?= $child_license['activation_count'] ?>/<?= $child_license['limit'] ?>) <i class="caret sb-caret"></i>
					</div>
					<div class="c-sb-section__body">
						<ul class="c-sb-list c-sb-list--compact">
							<?php foreach ($child_license['sites'] as $site): ?>
								<li class="c-sb-list-item">
									<a class="c-sb-list-item__link t-tx-blue-500" href="https://<?= $site ?>"><?= $site ?></a>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			<?php endforeach ?>
			<?php if (!empty($license['upgrades'])): ?>
				<div class="c-sb-section c-sb-section--toggle">
					<div class="c-sb-section__title js-sb-toggle">
						Upgrades <i class="caret sb-caret"></i>
					</div>
					<div class="c-sb-section__body">
						<ul class="c-sb-timeline c-sb-timeline--list u-mrg-0">
							<?php foreach ($license['upgrades'] as $upgrade_id => $upgrade): ?>
								<li class="c-sb-timeline-item">
									<span class="c-sb-timeline-item__text">
										<a class="c-sb-timeline-item__link t-tx-blue-500" href="<?= $upgrade['link'] ?>">
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
			<?php if ( apply_filters( 'edd_helpscout_show_bundle_activations', empty( $license['children'] ), $license ) ): ?>
				<div class="c-sb-section c-sb-section--toggle">
					<div class="c-sb-section__title js-sb-toggle">
						Active sites (<?= $license['activation_count'] ?>/<?= $license['limit'] ?>) <i class="caret sb-caret"></i>
					</div>
					<div class="c-sb-section__body">
						<ul class="c-sb-list c-sb-list--compact">
							<?php foreach ($license['sites'] as $site): ?>
								<li class="c-sb-list-item">
									<a class="c-sb-list-item__link t-tx-blue-500" href="https://<?= $site ?>"><?= $site ?></a>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			<?php endif ?>
			<div class="divider"></div>
		<?php endforeach ?>
	</div>
</div>

