<?php

//unique id
$wrapper_id = randomWord(6);
?>
<div id="<?php echo $wrapper_id ?>" class="text-editor panel panel-default">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active">
			<a href="#text_<?php echo $wrapper_id; ?>" aria-controls="text_<?php echo $wrapper_id; ?>" role="tab" data-toggle="tab">
				&nbsp;&nbsp;&nbsp;<?php echo $tab_text; ?>&nbsp;&nbsp;&nbsp;
			</a>
		</li>
		<li role="presentation">
			<a href="#visual_<?php echo $wrapper_id; ?>" aria-controls="visual_<?php echo $wrapper_id; ?>" role="tab" data-toggle="tab">
				&nbsp;&nbsp;&nbsp;<?php echo $tab_visual; ?>&nbsp;&nbsp;&nbsp;
			</a>
		</li>
	</ul>

	<div class="common_content_actions pull-right">
		<div class="btn-group">

			<?php if ($required == 'Y' || $multilingual){ ?>
			<span class="btn afield-nav">
	        <?php if ($required == 'Y'){ ?>
			    <span class="required">*</span>
		    <?php } ?>
	
			<?php if ($multilingual){ ?>
			    <span class="multilingual"><i class="fa fa-flag"></i></span>
			<?php } ?>
	
	        </span>
			<?php } ?>
			<?php if(!$attr == 'no_media') { ?>
			<a title="<?php echo $button_add_media; ?>"
				data-original-title="<?php echo $button_add_media; ?>"
                href="#"
                class="btn btn-primary tooltips add_media">
				<i class="fa fa-file-picture-o fa-fw"></i> <?php echo $button_add_media; ?>
			</a>
            <?php } ?>
		</div>

	</div>
	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="text_<?php echo $wrapper_id; ?>">
			<div class="zennable">
			
			<div class="toolbar text-editor-toolbar">
				<div class="primary_content_actions">
					<div class="btn-group">
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_strong" data-original-title="Paragraph">p</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_strong" data-original-title="Bold">b</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_em" data-original-title="Italic">i</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt_link" data-original-title="Insert link">a</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_block" data-original-title="Blockquote">blockquote</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_del" data-original-title="Deleted text (strikethrough)">del</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_ins" data-original-title="Inserted text">ins</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt_img" data-original-title="Insert image">img</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_ul" data-original-title="Bulleted list">ul</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_ol" data-original-title="Numbered list">ol</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_li" data-original-title="List item">li</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt qt_cnt_code" data-original-title="Code">code</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt_comment" data-original-title="Comment">comment</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt_expand" data-original-title="Expand">
							<i class="fa fa-expand fa-fw"></i>
						</a>
						<a href="#" class="btn btn-default btn-xs tooltips qt_cnt_shrink" data-original-title="Shrink" style="display:none">
							<i class="fa fa-compress fa-fw"></i>
						</a>
					</div>
				</div>
			</div>
		    <div class="zen-backdrop">
			        <textarea
					    class="form-control atext <?php echo $style ?>"
					    name="<?php echo $name ?>"
					    placeholder="<?php echo $placeholder; ?>"
					    id="<?php echo $id ?>"
					    data-orgvalue="<?php echo $ovalue ?>"
					    <?php if(!$attr == 'no_media') { echo $attr; } ?>
					    ><?php echo $value ?></textarea>
		
		    </div>
		</div>

		</div>

		<div role="tabpanel" class="tab-pane" id="visual_<?php echo $wrapper_id ?>">
	        <textarea
	        	class="visual_editor"
			    name="<?php echo $name ?>"
			    disabled="disabled"
			    placeholder="<?php echo $placeholder; ?>"
			    id="text_editor_<?php echo $id ?>"
			    ><?php echo $value ?>
	        </textarea>
		</div>

	</div>

</div>


<script type="application/javascript">
	$(document).ready(function () {
		tinymce.remove();
		//initiate editor
		//mcei.selector = 'textarea#text_editor_<?php echo $id ?>';

		tinymce.baseURL = "<?php echo $template_dir; ?>javascript/tinymce";

		//tinymce.init(mcei);
		tinymce.init({
			selector: 'textarea#text_editor_<?php echo $id ?>',
			height: '262px',
			theme: "modern",
			skin: "lightgray",
			language: "<?php echo $language_code; ?>",
			formats: {
				alignleft: [{
					selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
					styles: {textAlign: "left"}
				}, {selector: "img,table,dl.wp-caption", classes: "alignleft"}],
				aligncenter: [{
					selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
					styles: {textAlign: "center"}
				}, {selector: "img,table,dl.wp-caption", classes: "aligncenter"}],
				alignright: [{
					selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
					styles: {textAlign: "right"}
				}, {selector: "img,table,dl.wp-caption", classes: "alignright"}],
				strikethrough: {inline: "del"}
			},
			forced_root_block : false,
			cleanup : false,
			verify_html : false,
			trim_span_elements: false,
			fix_list_elements: false,
			relative_urls: false,
			remove_script_host: false,
			convert_urls: false,
			browser_spellcheck: true,
			entities: "38,amp,60,lt,62,gt",
			entity_encoding: "raw",
			keep_styles: false,
			cache_suffix: "abc-mce-433-20160114",
			preview_styles: "font-family font-size font-weight font-style text-decoration text-transform",
			end_container_on_empty_block: true,
			editimage_disable_captions: false,
			editimage_html5_captions: true,
			plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,link,table",
			resize: true,
			menubar: false,
			autop: true,
			indent: false,
			toolbar_items_size : 'small',
			toolbar1: "undo,redo,bold,italic,strikethrough,bullist,forecolor backcolor,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,spellchecker,dfw,fullscreen,table",
			valid_elements : '*[*]',
			invalid_elements : "...",
			tabfocus_elements: "content-html,save-post",
			body_class: "content post-type-post post-status-auto-draft post-format-standard locale-en-gb",
			autoresize_on: true,
			add_unload_trigger: false,
		});
		

		//for modal mode
		if($('#<?php echo $wrapper_id; ?>').parents('.modal-content').length>0){
			$('#<?php echo $wrapper_id; ?> a.qt_cnt_expand').hide();
		}
	
		//event for textarea buttons
		$('#<?php echo $wrapper_id; ?> a.qt_cnt_expand').on('click',function(){
			$('#<?php echo $wrapper_id; ?> .zennable').addClass('expanded');
			$('#<?php echo $wrapper_id; ?> .qt_cnt_shrink').show();
			$('#<?php echo $wrapper_id; ?> .qt_cnt_expand').hide();
			return false;
		});
	
		$('#<?php echo $wrapper_id; ?> a.qt_cnt_shrink').on('click',function(){
			$('#<?php echo $wrapper_id; ?> .zennable').removeClass('expanded');
			$('#<?php echo $wrapper_id; ?> .qt_cnt_shrink').hide();
			$('#<?php echo $wrapper_id; ?> .qt_cnt_expand').show();
			return false;
		});

		$('#<?php echo $wrapper_id; ?> a.qt_cnt').on('click',function(){
			var elem = $(this);
			var tag = elem.text();
			var editor, cursorPosition;
			editor = $('#<?php echo $id ?>');
			if(elem.hasClass('closing')){
				textareaInsert(editor, '<'+tag+'>');
				elem.text(tag.replace('/', ''));
				elem.removeClass('closing');				
			} else {
				textareaInsert(editor, '<'+tag+'>');
				elem.prepend('/');
				elem.addClass('closing');	
			}
			return false;
		});
		
		$('#<?php echo $wrapper_id; ?> a.qt_cnt_link').on('click',function(){
			var elem = $(this);
			var tag = elem.text();
			var editor, cursorPosition;
			editor = $('#<?php echo $id ?>');
			if(elem.hasClass('closing')){
				textareaInsert(editor, '<'+tag+'>');
				elem.text(tag.replace('/', ''));
				elem.removeClass('closing');				
			} else {
				textareaInsert(editor, '<a href="" target="">');
				elem.prepend('/');
				elem.addClass('closing');	
			}
			return false;
		});

		$('#<?php echo $wrapper_id; ?> a.qt_cnt_img').on('click',function(){
			var editor, cursorPosition;
			editor = $('#<?php echo $id ?>');
			textareaInsert(editor, '<img src="" alt="" />');
			return false;
		});

		$('#<?php echo $wrapper_id; ?> a.qt_cnt_comment').on('click',function(){
			var editor, cursorPosition;
			editor = $('#<?php echo $id ?>');
			textareaInsert(editor, '<!-- comment -->');
			return false;
		});

		$('#<?php echo $wrapper_id; ?> a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			var newtab_id = $(e.target).attr('aria-controls'), // newly activated tab
		        prevtab_id = $(e.relatedTarget).attr('aria-controls'); // previous active tab

			var textarea, value;
			textarea = $('#'+prevtab_id+ ' textarea');
			
			if(prevtab_id == 'visual_<?php echo $wrapper_id?>'){
				value = tinyMCE.get('text_editor_<?php echo $id ?>').getContent();
				value = visual2html(value);
				$('#'+newtab_id+ ' textarea')
						.val( value )
						.removeAttr('disabled');
			} else {
				$('#'+newtab_id+ ' textarea')
						.val(textarea.val())
						.removeAttr('disabled');
				if(tinyMCE.get('text_editor_<?php echo $id ?>') != null) {
					value = textarea.val();
					value = html2visual(value);
					tinyMCE.get('text_editor_<?php echo $id ?>').setContent( value );
				}
			}
			
			//block previous textarea
			textarea.attr('disabled','disabled');
		});

		<?php if( is_int(strpos($value,'&lt;!--n--&gt;&lt;')) || is_int(strpos($value,'&lt;!--t--&gt;&lt;')) ){?>
		$('#<?php echo $wrapper_id; ?> a[href="#visual_<?php echo $wrapper_id; ?>"]').tab('show');
		<?php } ?>

		//event for addmedia button
		$('#<?php echo $wrapper_id; ?> a.add_media').on('click',function(event){
			
			//get data container
			var data_id = $("#<?php echo $wrapper_id ?> ul.nav-tabs li.active>a").attr('aria-controls');
			var editor, cursorPosition;
			if( data_id == 'text_<?php echo $wrapper_id ?>'){
				editor = $('#<?php echo $id ?>');
				cursorPosition = editor.getCursorPosition();
			}else{
				editor = tinyMCE.get('text_editor_<?php echo $id ?>');
				cursorPosition = null;
			}
			openTextEditRLModal(editor,cursorPosition);
			return false;
		});
	});
</script>