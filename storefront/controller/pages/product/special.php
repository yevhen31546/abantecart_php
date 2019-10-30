<?php 
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2017 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesProductSpecial extends AController {

	public $data = array();

	/**
	 * Check if HTML Cache is enabled for the method
	 * @return array - array of data keys to be used for cache key building  
	 */	
	public static function main_cache_keys(){
		return array('page','limit','sort','order');
	}

	public function main() {
		$request = $this->request->get;

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('product/special');
		$this->document->setTitle( $this->language->get('heading_title') );
		$this->document->resetBreadcrumbs();
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getHomeURL(),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		 ));

		$url = '';
		if (isset($request['page'])) {
			$url .= '&page=' . $request['page'];
		}

		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getNonSecureURL('product/special',  $url),
			'text'      => $this->language->get('heading_title'),
			'separator' => $this->language->get('text_separator')
		 ));

		if (isset($request['page'])) {
			$page = $request['page'];
		} else {
			$page = 1;
		}

		if (isset($request['limit'])) {
			$limit = (int)$request['limit'];
			$limit = $limit > 50 ? 50 : $limit;
		} else {
			$limit = $this->config->get('config_catalog_limit');
		}

		if (isset($request['sort'])) {
			$sorting_href = $request['sort'];
		} else {
			$sorting_href = $this->config->get('config_product_default_sort_order');
		}
		list($sort, $order) = explode("-", $sorting_href);
		if($sort=='name'){
			$sort = 'pd.'.$sort;
		}elseif(in_array($sort,array('sort_order','price'))){
			$sort = 'p.'.$sort;
		}

		$this->loadModel('catalog/product');
		$promotion = new APromotion();

		$product_total = $promotion->getTotalProductSpecials();

		if ($product_total) {
			$this->loadModel('catalog/review');
			$this->loadModel('tool/seo_url');
			$this->loadModel('tool/image');

			$this->data['button_add_to_cart'] = $this->language->get('button_add_to_cart');

			$results = $promotion->getProductSpecials($sort,
													 $order,
													 ($page - 1) * $limit,
													 $limit);

			$product_ids = array();
			foreach($results as $result){
				$product_ids[] = (int)$result['product_id'];
			}

			//Format product data specific for confirmation page
			$resource = new AResource('image');
			$thumbnails = $resource->getMainThumbList(
							'products',
							$product_ids,
							$this->config->get('config_image_product_width'),
							$this->config->get('config_image_product_height')
			);
			$stock_info = $this->model_catalog_product->getProductsStockInfo($product_ids);
			foreach ($results as $result) {
				$thumbnail = $thumbnails[$result['product_id']];
				if ($this->config->get('enable_reviews')) {
					$rating = $this->model_catalog_review->getAverageRating($result['product_id']);
				} else {
					$rating = false;
				}

				$special = FALSE;
				$discount = $promotion->getProductDiscount($result['product_id']);
				if ($discount) {
					$price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
					$special = $promotion->getProductSpecial($result['product_id']);
					if ($special) {
						$special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')));
					}
				}

				$options = $this->model_catalog_product->getProductOptions($result['product_id']);
				if ($options) {
					$add = $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'], '&encode');
				} else {
					if($this->config->get('config_cart_ajax')){
						$add = '#';
					}else{
						$add = $this->html->getSecureURL('checkout/cart', '&product_id=' . $result['product_id'], '&encode');
					}
				}

				//check for stock status, availability and config
				$track_stock = false;
				$in_stock = false;
				$no_stock_text = $this->language->get('text_out_of_stock');
				$total_quantity = 0;
				$stock_checkout = $result['stock_checkout'] === '' ?  $this->config->get('config_stock_checkout') : $result['stock_checkout'];
				if ( $stock_info[$result['product_id']]['subtract'] ) {
					$track_stock = true;
					$total_quantity = $stock_info[$result['product_id']]['quantity'];
					//we have stock or out of stock checkout is allowed
					if ($total_quantity > 0 || $stock_checkout) {
						$in_stock = true;
					}
				}

				$this->data['products'][] = array(
					'product_id'    => $result['product_id'],
					'name'    		=> $result['name'],
					'model'   		=> $result['model'],
					'rating'  		=> $rating,
					'stars'   		=> sprintf($this->language->get('text_stars'), $rating),
					'price'   		=> $price,
					'raw_price'     => $result['price'],
					'call_to_order' => $result['call_to_order'],
					'options'   	=> $options,
					'special' 		=> $special,
					'thumb'   		=> $thumbnail,
					'href'    		=> $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'], '&encode'),
					'add'    		=> $add,
					'description'	=> html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
					'blurb'	        => $result['blurb'],
					'track_stock'   => $track_stock,
					'in_stock'		=> $in_stock,
					'no_stock_text' => $no_stock_text,
					'total_quantity'=> $total_quantity,
					'tax_class_id'  => $result['tax_class_id']
				);
			}

			if ($this->config->get('config_customer_price')) {
				$display_price = TRUE;
			} elseif ($this->customer->isLogged()) {
				$display_price = TRUE;
			} else {
				$display_price = FALSE;
			}
			$this->data['display_price'] = $display_price;

			$sorts = array();
			$sorts[] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->html->getURL('product/special', $url . '&sort=p.sort_order&order=ASC', '&encode')
			);

			$sorts[] = array(
				'text'  => $this->language->get('text_sorting_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->html->getURL('product/special', $url . '&sort=pd.name&order=ASC', '&encode')
			); 

			$sorts[] = array(
				'text'  => $this->language->get('text_sorting_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->html->getURL('product/special', $url . '&sort=pd.name&order=DESC', '&encode')
			);  

			$sorts[] = array(
				'text'  => $this->language->get('text_sorting_price_asc'),
				'value' => 'ps.price-ASC',
				'href'  => $this->html->getURL('product/special', $url . '&sort=special&order=ASC', '&encode')
			); 

			$sorts[] = array(
				'text'  => $this->language->get('text_sorting_price_desc'),
				'value' => 'ps.price-DESC',
				'href'  => $this->html->getURL('product/special', $url . '&sort=special&order=DESC', '&encode')
			); 
			$sorts[] = array(
				'text'  => $this->language->get('text_sorting_rating_desc'),
				'value' => 'rating-DESC',
				'href'  => $this->html->getURL('product/special', $url . '&sort=rating&order=DESC', '&encode')
			); 
			$sorts[] = array(
				'text'  => $this->language->get('text_sorting_rating_asc'),
				'value' => 'rating-ASC',
				'href'  => $this->html->getURL('product/special', $url . '&sort=rating&order=ASC', '&encode')
			);
			$sorts[] = array(
				'text'  => $this->language->get('text_sorting_date_desc'),
				'value' => 'date_modified-DESC',
				'href'  => $this->html->getSEOURL('product/special', $url . '&sort=date_modified&order=DESC', '&encode')
			);
			$sorts[] = array(
				'text'  => $this->language->get('text_sorting_date_asc'),
				'value' => 'date_modified-ASC',
				'href'  => $this->html->getSEOURL('product/special', $url . '&sort=date_modified&order=ASC', '&encode')
			);
			$sort_options = array();
			foreach($sorts as $item){
				$sort_options[$item['value']] = $item['text'];
			}
			$sorting = $this->html->buildElement(
				array (
					'type' => 'selectbox',
					'name' => 'sort',
					'options'=> $sort_options,
					'value'=> $sort.'-'.$order
				)
			);

			$this->view->assign('sorting', $sorting );
			$this->view->assign('url', $this->html->getURL('product/special') );

			$this->data['sorts'] = $sorts;
			$pagination_url = $this->html->getURL('product/special', '&sort=' . $sorting_href . '&page={page}' . '&limit=' . $limit, '&encode');
			$this->data['pagination_bootstrap'] = $this->html->buildElement(
									array (
											'type' => 'Pagination',
											'name' => 'pagination',
											'text'=> $this->language->get('text_pagination'),
											'text_limit' => $this->language->get('text_per_page'),
											'total'	=> $product_total,
											'page'	=> $page,
											'limit'	=> $limit,
											'url' => $pagination_url,
											'style' => 'pagination'));

			$this->data['sort'] = $sort;
			$this->data['order'] = $order;
			$this->data['review_status'] = $this->config->get('enable_reviews');
			$this->view->batchAssign($this->data);
			$this->view->setTemplate( 'pages/product/special.tpl' );
		} else {
			$this->view->assign('text_error', $this->language->get('text_empty') );
			$continue = $this->html->buildElement(
											array(
												'type' => 'button',
												'name' => 'continue_button',
												'text'=> $this->language->get('button_continue'),
												'style' => 'button'));
			$this->view->assign('button_continue', $continue);
			$this->view->assign('continue',  $this->html->getHomeURL() );
			$this->view->setTemplate( 'pages/error/not_found.tpl' );
		}
		$this->processTemplate();

		//init controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
