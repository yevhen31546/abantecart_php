

<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<?php echo $blog_manager_tabs ?>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<?php echo $this->getHookVar('common_content_buttons'); ?>
					<?php if ($help) { ?>
                        <a class="btn btn-white tooltips"
                           href="<?php echo $help['link']; ?>"
                           data-toggle="modal" data-target="#help_modal"
                           title="<?php echo $text_more_help ?>"><i
                                    class="fa fa-flask fa-lg"></i> <?php echo $help['text'] ?></a>
                    <?php } ?>
				<?php echo $this->getHookVar('extension_toolbar_buttons'); ?>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>
	<?php if ($proc == 'new') { ?>
    	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
    		<?php echo $setup_message . ' <a href="' . $details_link . '">Click Here!</a>'; ?> 
    	</div>
    <?php } else { ?>
        <?php echo $form['form_open']; ?>
        <div class="panel-body panel-body-nopadding tab-content col-xs-12">
            <?php foreach ($form['fields'] as $section => $fields) { ?>
                <div id="<?php echo $section; ?>" style="margin-top: 16px;">
                    <?php foreach ($fields as $name => $field) { ?>
                        <?php 
                            //Logic to calculate fields width
                            $widthcasses = "col-sm-7";
                            if ( is_int(stripos($field->style, 'large-field')) ) {
                                $widthcasses = "col-sm-7";
                            } else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
                                $widthcasses = "col-sm-5";
                            } else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
                                $widthcasses = "col-sm-3";
                            } else if ( is_int(stripos($field->style, 'tiny-field')) ) {
                                $widthcasses = "col-sm-2";
                            }
                            $widthcasses .= " col-xs-12";
                        ?>
                        
                        <div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
                            <label class="control-label col-sm-4 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
                            <div id="field_<?php echo $name; ?>" class="input-group afield <?php echo $widthcasses; ?>">
                                <?php echo $field; ?>                                                                
                            </div>
                            <?php if (!empty($error[$name])) { ?>
                            <span class="help-block field_err"><?php echo $error[$name]; ?></span>
                            <?php } ?>
                            
                        </div>
                    <?php }  ?>
                   
                 </div>       
            <?php }  ?>  
        </div>
    
        <div class="panel-footer col-xs-12">
            <div class="text-center">
                <button id="page-submit" class="btn btn-primary lock-on-click">
                <i class="fa fa-cog fa-fw"></i> <?php echo $form['submit']->text; ?>
                </button>
                <a class="btn btn-default" href="<?php echo $cancel; ?>">
                <i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
                </a>
            </div>
        </div>
        </form>
	<?php } ?>
</div><!-- <div class="tab-content"> -->

<?php echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'help_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'
		));
?>
<script type="text/javascript"><!--
	var option = $('input:radio[name=blog_access]:checked', '#blogManagerFrm').val();
	
	var data_option = $('input:radio[name=login_data]:checked', '#blogManagerFrm').val();
	toggleOptions(option);
	
	$(document).ready(function(){
		$('input:radio[name=blog_access]').on('click', function(){
			var option = $('input:radio[name=blog_access]:checked', '#blogManagerFrm').val();
			toggleOptions(option);
			
		});
		$('input:radio[name=login_data]').on('click', function(){
			var data_option = $('input:radio[name=login_data]:checked', '#blogManagerFrm').val();
			toggleDataOptions(data_option);
			
		});
	});
	
	function toggleOptions(option) {
		if (option == 'all') {
			$('#restrict').hide();
			$('#new_user').hide();
			$('#customer').hide();
			$('#misc').hide();
		}else{
			$('#misc').show();
			$('#restrict').show();
			var data_option = $('input:radio[name=login_data]:checked', '#blogManagerFrm').val();
			toggleDataOptions(data_option);
		}
	}
	function toggleDataOptions(data_option) {
		if (data_option == 'self') {
			$('#customer').hide();
			$('#new_user').show();
		}else{
			$('#new_user').hide();
			$('#customer').show();
		}
	}
	
//--></script>
