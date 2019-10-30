<?php 
/*------------------------------------------------------------------------------
  $Id$
  
  Blog Manager Extension for
  AbanteCart, Ideal OpenSource Ecommerce Solution
  
  Copyright © 2016 - 2017 Corner Stores Online
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerBlocksBlogAuthor extends AController {
	public $data = array();

	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadModel('blog/blog');
		$this->view->assign('heading_title', $this->language->get('blog_author_title') );
		
		$status = $this->model_blog_blog->getblog_config('author_list_block_status');
		$min = $this->model_blog_blog->getblog_config('author_list_block_min');
		$this->data['limit'] = $this->model_blog_blog->getblog_config('author_list_block_limit');
		$this->data['max'] = $this->model_blog_blog->getblog_config('author_list_block_max');
		$this->view->assign('more', $this->language->get('text_more'));
		$this->view->assign('less', $this->language->get('text_less'));
		
		if($status) {
			$author = $this->model_blog_blog->getAuthors($this->data['max']);
			$this->data['author_count'] = count($author);
			if($this->data['author_count'] >= $min) {
				$author_links = array();
				foreach ($author as $row) {
					$author_links[] = array(
						'author_name' => $row['author_name'],
						'entry_count' => $row['entry_count'],
						'href' => $this->blog_html->getBLOGSEOURL('blog/author','&blog_author_id=' . $row['blog_author_id'], '&encode'),
						'entry_count' => $row['entry_count'],
					);
				}
				$this->data['author_links'] = $author_links;
				// framed needs to show frames for generic block.
				//If tpl used by listing block framed was set by listing block settings
				$this->view->assign('block_framed',true);
				
				$this->view->batchAssign($this->data);
				$this->processTemplate();
			}
		}
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}	

}
