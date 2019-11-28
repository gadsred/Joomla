<?php
/**
 * @version     1.0.0
 * @package     com_faq
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */
// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_faq', JPATH_ADMINISTRATOR);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/components/com_faq/assets/js/form.js');
$user       = JFactory::getUser();


//gads enable switch linked listing
				$user_id = JFactory::getUser()->id;
				
				$db = JFactory::getDBO();
				$query = "Select link_id From #__users where id='$user_id'";
				$db->setQuery($query);
				$db->loadObject();
				$id = $db->loadObject();
?>
</style>
<script type="text/javascript">
    if (jQuery === 'undefined') {
        document.addEventListener("DOMContentLoaded", function(event) { 
            jQuery('#form-faq').submit(function(event) {
                
            });

            
        });
    } else {
        jQuery(document).ready(function() {
            jQuery('#form-faq').submit(function(event) {
                
            });

            
        });
    }
</script>
<style>
	.buttons_mainl {
	padding: 10px 15px;
    height: 46px;
    text-align: center;
    font-size: 15px;
    color: #fff;
    font-weight: bold;
    border: 3px solid #cccccc;
    text-transform: ;
    margin-right: 10px;
	background-color:#004e65;
	width:100% !important;
}
</style>
<div class="wbc_h2container">
<span>
<?php if (!empty($this->item->id)): ?>
Edit Question & Answer
<?php $link_id=$this->item->link_id; ?>
<?php else: ?>
Add New Question & Answer <?php $link_id=$user->link_id;?>
<?php endif; ?>
</span>
</div>
<br/>
<form id="form-faq" action="<?php echo JRoute::_('index.php?option=com_faq&task=faq.save'); ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[link_id]" value="<?php echo $id->link_id; ?>" />
	
	<div class="profile_left_lable"><?php echo $this->form->getLabel('question'); ?></div>
	<div class="profile_left_txtbox"><?php echo $this->form->getInput('question'); ?></div>
	<div class="clear"></div>
	
	<div class="profile_left_lable"><?php echo $this->form->getLabel('answer'); ?></div>
	<div class="profile_left_txtbox"><?php echo $this->form->getInput('answer'); ?></div>
	<div class="clear"></div>
	
	<input type="hidden" name="option" value="com_faq" />
	<input type="hidden" name="task" value="faqform.save" />
	<?php echo JHtml::_('form.token'); ?>
		
	<div class="buttons_containers_pro">
		<button id="send" type="submit" class="buttons_main blue_bg"><?php echo JText::_('JSUBMIT'); ?></button>
		<button type="button" onclick="history.back();" class="buttons_main black_bg"><?php echo JText::_('JCANCEL'); ?></button>
	</div>
</form>

<!--gads signup modal-->

	<div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="width: 25%;position:fixed;left: 75%;">
	  
		<div id="signModal" class="modal-content" style="">
			<div class="modal-header">
				<button type="button" class="close" onClick=" $('.modal-backdrop').remove();" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 style="font-size: 18px;color: #41565b;font-weight: bold;">
				
				</h4>
			</div>
			<div class="modal-body" style="display: inline-block;left: 5px;">
				<h4 style="line-height:25px;" class="s-h4">Before you start updating your listing you will need to sign up to activate your listing.</h4>
			</div>
			<div class="modal-footer" style="text-align:left !important;">

				<div id="foot" class="col-md sp2">
					<input id="signup" type="button" onClick="$('#form-faq').submit();" value="Sign Up Now" class="buttons_mainl">		
				</div>

			</div>
		</div>
	  
	</div>

<!--end gads signup modal-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>

jQuery(document).ready(function() {
	// $('#jform_question').attr("style","width:100%;");
	// $('#jform_answer').attr("style","width:100%;");
	
	//gads redirect signup
		   <?php
				//---gads redirect list with us---//
				$db =  JFactory::getDbo();
				$query= "Select link_published,sub_id,invite,invite_code,invite_open From #__mt_links Where link_id='{$id->link_id}'";
				$db->setQuery($query);
				$link_status = $db->loadObject();
				
				if($link_status->link_published !='1' || !$link_status->sub_id)
				{?>
				
				jQuery('#send').attr('data-toggle','modal');
				jQuery('#send').attr('data-target','#signupModal');
				jQuery('#send').attr('type','button');
		<?php	}?>	
});
</script>
