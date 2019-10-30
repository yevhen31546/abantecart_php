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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ControllerResponsesListingGridProduct extends AController {
	public $data = array();
	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('catalog/product');
		$this->loadModel('catalog/product');
		$this->loadModel('tool/image');

		//Clean up parameters if needed
		if (isset($this->request->get['keyword']) && $this->request->get['keyword'] == $this->language->get('filter_product')) {
			unset($this->request->get['keyword']);
		}
		if (isset($this->request->get['pfrom']) && $this->request->get['pfrom'] == 0) {
			unset($this->request->get['pfrom']);
		}
		if (isset($this->request->get['pto']) && $this->request->get['pto'] == $this->language->get('filter_price_max')) {
			unset($this->request->get['pto']);
		}

		//Prepare filter config
		$filter_params = array( 'category', 'status', 'keyword', 'match', 'pfrom', 'pto' );
		$grid_filter_params = array( 'name', 'sort_order', 'model' );

		$filter_form = new AFilter(array( 'method' => 'get', 'filter_params' => $filter_params ));
		$filter_grid = new AFilter(array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ));
		$data = array_merge($filter_form->getFilterData(), $filter_grid->getFilterData());

		$total = $this->model_catalog_product->getTotalProducts($data);
		$response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages($total);
		$response->records = $total;
		$response->userdata = new stdClass();
		$response->userdata->classes = array();
		$results = $this->model_catalog_product->getProducts($data);

		$product_ids = array();
		foreach($results as $result){
			$product_ids[] = (int)$result['product_id'];
		}

		$resource = new AResource('image');
		$thumbnails = $resource->getMainThumbList(
						'products',
						$product_ids,
						$this->config->get('config_image_grid_width'),
						$this->config->get('config_image_grid_height')
		);
		$i = 0;
		foreach ($results as $result) {
			$thumbnail = $thumbnails[ $result['product_id'] ];

			$response->rows[ $i ]['id'] = $result['product_id'];
			if( dateISO2Int($result['date_available'])> time()){
				$response->userdata->classes[ $result['product_id'] ] = 'warning';
			}

			if($result['call_to_order']>0){
				$price = $this->language->get('text_call_to_order');
			}else{
				$price = $this->html->buildInput(
								array(
									'name' => 'price[' . $result['product_id'] . ']',
									'value' => moneyDisplayFormat( $result['price'] )
								));
			}

			$response->rows[ $i ]['cell'] = array(
				$thumbnail['thumb_html'],
				$this->html->buildInput(array(
					'name' => 'product_description[' . $result['product_id'] . '][name]',
					'value' => $result['name'],
				)),
				$this->html->buildInput(array(
					'name' => 'model[' . $result['product_id'] . ']',
					'value' => $result['model'],
				)),
				$price,
				(int)$result['quantity'],
				$this->html->buildCheckbox(array(
					'name' => 'status[' . $result['product_id'] . ']',
					'value' => $result['status'],
					'style' => 'btn_switch',
				)),
			);
			$i++;
		}
		$this->data['response'] = $response;
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($this->data['response']));
	}

	public function update() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('listing_grid/product')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/product'),
					'reset_value' => true
				));
		}

		$this->loadModel('catalog/product');
		$this->loadLanguage('catalog/product');

		switch ($this->request->post['oper']) {
			case 'del':
				$ids = explode(',', $this->request->post['id']);
				if (!empty($ids))
					foreach ($ids as $id) {
						$err = $this->_validateDelete($id);
						if (!empty($err)) {
							$error = new AError('');
							return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
						}
						$this->model_catalog_product->deleteProduct($id);
					}
				break;
			case 'save':
				$allowedFields = array_merge(array( 'product_description', 'model', 'call_to_order', 'price', 'quantity', 'status' ), (array)$this->data['allowed_fields']);
				$ids = explode(',', $this->request->post['id']);
				if (!empty($ids))
					foreach ($ids as $id) {
						foreach ($allowedFields as $f) {
							if ($f == 'status' && !isset($this->request->post['status'][ $id ]))
								$this->request->post['status'][ $id ] = 0;

							if (isset($this->request->post[ $f ][ $id ])) {
								$err = $this->_validateField($f, $this->request->post[ $f ][ $id ]);
								if (!empty($err)) {
									$error = new AError('');
									return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
								}
								$this->model_catalog_product->updateProduct($id, array( $f => $this->request->post[ $f ][ $id ] ));
							}
						}
					}
				break;
			case 'relate':
				$ids = explode(',', $this->request->post['id']);
				if (!empty($ids)){
					$this->model_catalog_product->relateProducts($ids);
				}
				break;
			default:
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	/**
	 * update only one field
	 *
	 * @return void
	 */
	public function update_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('listing_grid/product')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/product'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('catalog/product');
		$this->loadModel('catalog/product');

		$product_id = (int)$this->request->get['id'];
		if ($product_id) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				$err = $this->_validateField($key, $value);
				if (!empty($err)) {
					$error = new AError('');
					return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
				}
				if($key=='date_available'){
					$value = dateDisplay2ISO($value);
				}
				$data = array( $key => $value );
				$this->model_catalog_product->updateProduct($product_id, $data);
				$this->model_catalog_product->updateProductLinks($product_id, $data);
			}
			return null;
		}

		//request sent from jGrid. ID is key of array
		$allowedFields = array_merge(array( 'product_description', 'model', 'price', 'call_to_order', 'quantity', 'status' ), (array)$this->data['allowed_fields']);
		foreach ($allowedFields as $f) {
			if (isset($this->request->post[ $f ]))
				foreach ($this->request->post[ $f ] as $k => $v) {
					$err = $this->_validateField($f, $v);
					if (!empty($err)) {
						$error = new AError('');
						return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
					}
					$this->model_catalog_product->updateProduct($k, array( $f => $v ));
				}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function update_discount_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('listing_grid/product')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/product'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('catalog/product');
		$this->loadModel('catalog/product');
		if (isset($this->request->get['id'])) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				$data = array( $key => $value );
				$this->model_catalog_product->updateProductDiscount($this->request->get['id'], $data);
			}
			return null;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function update_special_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('listing_grid/product')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/product'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('catalog/product');
		$this->loadModel('catalog/product');
		if (isset($this->request->get['id'])) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				$data = array( $key => $value );
				$this->model_catalog_product->updateProductSpecial($this->request->get['id'], $data);
			}
			return null;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function update_relations_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('listing_grid/product')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/product'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('catalog/product');
		$this->loadModel('catalog/product');
		if (isset($this->request->get['id'])) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				$data = array( $key => $value );
				$this->model_catalog_product->updateProductLinks($this->request->get['id'], $data);
			}
			return null;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validateField($field, $value) {
		$this->data['error'] = '';
		switch ($field) {
			case 'product_description' :
				if (isset($value['name']) && ((mb_strlen($value['name']) < 1) || (mb_strlen($value['name']) > 255))) {
					$this->data['error'] = $this->language->get('error_name');
				}
				break;
			case 'model' :
				if (mb_strlen($value) > 64) {
					$this->data['error'] = $this->language->get('error_model');
				}
				break;
			case 'keyword' :
				$this->data['error'] = $this->html->isSEOkeywordExists('product_id='.$this->request->get['id'], $value);
				break;
			case 'length' :
			case 'width'  :
			case 'height' :
			case 'weight' :
				$v =  abs(preformatFloat($value, $this->language->get('decimal_point')));
				if($v>=1000){
					$this->data['error'] = $this->language->get('error_measure_value');
				}
				break;
		}
		$this->extensions->hk_ValidateData($this, array(__FUNCTION__, $field, $value));
		return $this->data['error'];
	}

	private function _validateDelete($id) {
		$this->data['error'] = '';
		$this->extensions->hk_ValidateData($this, array(__FUNCTION__, $id));
		return $this->data['error'];
	}

}