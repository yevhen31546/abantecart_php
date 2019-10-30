<div class="thumbnails list-inline">
	<?php
	if ($products) {
		$tax_exempt = $this->customer->isTaxExempt();
		$config_tax = $this->config->get('config_tax');
		$icount = 0;
		foreach ($products as $product) {
			$tax_message = '';

			if ($config_tax && !$tax_exempt && $product['tax_class_id']) {
					$tax_message = '&nbsp;&nbsp;' . $price_with_tax;
			}

			$item = array();
			$item['image'] = $product['thumb']['thumb_html'];
			$item['title'] = $product['name'];
			//John Chen 2018-09-13 Add model to the title
			if (stristr($item['title'],$product['model'])<>false) { $item['title'] = str_ireplace($product['model'],'', $item['title']); }
			$item['title'] = str_ireplace('iSpring', 'iSpring '. $product['model'], $item['title']);
			//John Chen 2018-09-13 Add model to the title
			if (stristr($item['title'],$item['model'])==false) $item['title'] = preg_replace('/(^\w+) /', '\1 '.$item['model'], $item['title']);
			$item['description'] = $product['model'];
			$item['rating'] = ($product['rating']) ? "<img class=\"rating\" src='" . $this->templateResource('/image/stars_' . $product['rating'] . '.png') . "' alt='" . $product['stars'] . "' width='64' height='12' />" : '';
			$product['blurb'] = html_entity_decode($product['blurb']);
			$item['info_url'] = $product['href'];
			$item['buy_url'] = $product['add'];

			if (!$display_price) {
				$item['price'] = '';
			}

			$review = $button_write;
			if ($item['rating']) {
				$review = $item['rating'];
			}

			if($icount == 4) {
				$icount = 0;
			?>
				<div class="clearfix"></div>
			<?php
			}
			$icount++;
			?>
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="fixed_wrapper">
					<div class="fixed">
						<a class="prdocutname" href="<?php echo $item['info_url'] ?>"
						   title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></a>
					</div>
				</div>
				<div class="thumbnail">
					<?php if ($product['special']) { ?>
						<span class="sale"></span>
					<?php } ?>
					<?php if ($product['new_product']) { ?>
						<span class="new"></span>
					<?php } ?>
					<a href="<?php echo $item['info_url'] ?>"><?php echo $item['image'] ?></a>

					<div class="shortlinks" style="text-align: left;">
						<a class="details" href="<?php echo $item['info_url'] ?>"><?php echo $button_view ?></a>
						<?php if ($review_status) { ?>
							<a class="compare" href="<?php echo $item['info_url'] ?>#review"><?php echo $review ?></a>
						<?php } ?>
						<?php echo $product['buttons'];
						echo '<br>'. substr(str_ireplace('<OL>','<UL>',$product['blurb']),0,512); //John Chen 2018-09-13 Add blurb to the pop-up
						?>
					</div>
					<div class="blurb"><?php //John Chen 2018-09-13 blurb makes the height uneven. echo html_entity_decode($product['blurb']) ?></div>
					<?php if ($display_price) { ?>
						<div class="pricetag jumbotron">
							<?php if($product['call_to_order']){ ?>
							<a data-id="<?php echo $product['product_id'] ?>" href="#"
								class="btn call_to_order"
							   title="<?php echo $text_call_to_order ?>"
							>&nbsp;
								<i class="fa fa-phone fa-fw"></i>
							</a>
							<?php } else if ($product['track_stock'] && !$product['in_stock']) { ?>
								<span class="nostock"><?php echo $text_out_of_stock .' | ETA '. date('M/d/Y',strtotime($product['date_available'])); ?></span>
							<?php } else { ?>
							<a data-id="<?php echo $product['product_id'] ?>"
								href="<?php echo $item['buy_url'] ?>"
							   class="productcart"
							   title="<?php echo $button_add_to_cart ?>"
							>
								<i class="fa fa-cart-plus fa-fw"></i>
							</a>
							<?php } ?>
							<div class="price">
								<?php if ($product['special']) { ?>
									<div class="pricenew"><?php echo $product['special'] . $tax_message; ?></div>
									<div class="priceold"><?php echo $product['price']; ?></div>
								<?php } else { ?>
									<div class="oneprice"><?php echo $product['price'] . $tax_message; ?></div>
								<?php } ?>
							</div>
						</div>
					<?php
					}
					echo $this->getHookVar('product_price_hook_var_' . $product['product_id']);
					?>
				</div>
			</div>
		<?php
		}
	}
	?>
</div>