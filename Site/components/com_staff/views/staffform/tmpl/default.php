<?php
/**
 * @version     1.0.0
 * @package     com_staff
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
$lang->load('com_staff', JPATH_ADMINISTRATOR);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/components/com_staff/assets/js/form.js');

$session =& JFactory::getSession();
$image_cloud=$session->get( 'image_cloud','');
$name=$session->get( 'name','');
$title=$session->get( 'title','');
$user= JFactory::getUser();

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
            jQuery('#form-staff').submit(function(event) {
                
            });

            
        });
    } else {
        jQuery(document).ready(function() {
            jQuery('#form-staff').submit(function(event) {
                
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
	Edit Staff Profile
<?php else: ?>
	Add New Staff
<?php endif; ?>
</span>
</div>
<br/>
<form id="frmgads" action="<?php echo JRoute::_('index.php?option=com_staff&task=upimage'); ?>" method="post" enctype="multipart/form-data">
	<div class="profile_left_lable"></div>
	<div class="profile_left_txtbox">
		<img id="image" style="width:135px;
								height:135px;
								max-width:100%;
								max-height:100%"   
						src="<?php if(!empty($image_cloud))
									{
										echo $image_cloud;
									}
									else
									{
										if($this->item->image)
										{
											echo $this->item->image;
										}
										else
										{ 
											echo '../images/faq-img.png';
										}
									}?>">
	</div>
	<input style="display:none;"  id="file" name="filename" type="file" />
	<button style="display:none;" id="upload" type="submit" class="upload_button blue_bg">Upload Photo</button>
	
	<input type="hidden" name="name" id="picname" value="" />
	<input type="hidden" name="title" id="title" value="" />
</form>
<form id="form-staff" action="<?php echo JRoute::_('index.php?option=com_staff&task=staff.save'); ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<?php if(empty($this->item->user_id)): ?>
		<input type="hidden" name="jform[user_id]" value="<?php echo JFactory::getUser()->id; ?>" />
		<input type="hidden" name="jform[link_id]" value="<?php echo $id->link_id ?>" />
	<?php else: ?>
		<input type="hidden" name="jform[user_id]" value="<?php echo $this->item->user_id; ?>" />
		<input type="hidden" name="jform[link_id]" value="<?php echo $id->link_id ?>" />
	<?php endif; ?>

	<div class="profile_left_lable"><?php echo $this->form->getLabel('name'); ?></div>
	<div class="profile_left_txtbox"><?php echo $this->form->getInput('name'); ?></div>
	<div class="clear"></div>

	<div class="profile_left_lable"><?php echo $this->form->getLabel('title'); ?></div>
	<div class="profile_left_txtbox"><?php echo $this->form->getInput('title'); ?></div>
	<div class="clear"></div>
	
	<div class="profile_left_lable">Upload Staff Photo</div>
	<div class="profile_left_txtbox">
		<button type="button" style="text-indent: 0px !important;float: left;width: 183px;" onclick="jQuery('#file').click();" class="upload_button blue_bg">Choose File</button>
		<button <?php if(!$user->name){echo 'disabled';}?> style="" onclick="jQuery('#loadstaff').attr('style','');jQuery('#upload').click();" type="button" class="upload_button blue_bg">Upload Photo</button>
		<p style="position:relative;top:0px;color:#000000;" id="filename"></p>
	</div>
	<div class="clear"></div>
	<input id="jform_image" type="hidden" name="jform[image]" value="<?php 
	if(!empty($image_cloud))
	{
		echo $image_cloud;
	}else
	{
		echo $this->item->image;
	}?>" />

	<div class="buttons_containers_pro">
		<button id="send" type="submit" class="buttons_main blue_bg"><?php echo JText::_('JSUBMIT'); ?></button>
		<button type="button" onclick="cancel_staff();" class="buttons_main black_bg"><?php echo JText::_('JCANCEL'); ?></button>
	</div>
	
	<input type="hidden" name="option" value="com_staff" />
	<input type="hidden" name="task" value="staffform.save" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div id="loadstaff" class="loader" style="display:none;" ></div>		
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
					<input id="signup" type="button" onClick="$('#form-staff').submit();" value="Sign Up Now" class="buttons_mainl">		
				</div>

			</div>
		</div>
	  
	</div>

<!--end gads signup modal-->

<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
<script>
var $ = jQuery.noConflict();
<?php if(!empty($name)){?>
$(window).load(function(){
	$('#jform_name').val('<?php if(!empty($name)){echo $name; }?>');
	$('#jform_title').val('<?php if(!empty($title)){echo $title; }?>');
});
<?php }?>
$('#file').click(function() {
	var name=$('#jform_name').val();
	$('#picname').val($('#jform_name').val());
	$('#title').val($('#jform_title').val());
	
	if(!name)
	{	event.preventDefault();
		alert('You need to input name first');
	}
	
	
	
});

$('#uploads').click(function() {
 var path = document.getElementById("file").value;
            alert(path);
});

$('#file').change(function(){
		var filename = $('#file').val();
		filename=filename.split("\\");
		var count_file = filename.length-1
		$('#filename').html('File Name: '+filename[count_file]+' ready, press "Upload Photo"');
	});

	function cancel_staff()
	{
		window.location.href = '<?php echo JURI::base()."index.php?option=com_staff&Itemid=221&lang=en&view=staffs";?>';
	}
	
$(document).ready(function() {
	//gads redirect signup
		   <?php
				//---gads redirect list with us---//
				$db =  JFactory::getDbo();
				$query= "Select link_published,sub_id,invite,invite_code,invite_open From #__mt_links Where link_id='{$id->link_id}'";
				$db->setQuery($query);
				$link_status = $db->loadObject();
				
				if($link_status->link_published !='1' || !$link_status->sub_id)
				{?>
				
				$('#send').attr('data-toggle','modal');
				$('#send').attr('data-target','#signupModal');
				$('#send').attr('type','button');
				$('#upload').prop('disabled',true);
		<?php	}?>	
});

</script>
<!--gads loading-->
<style type="text/css">
.loader,
.loader:before,
.loader:after {
  background: #13495d;
  -webkit-animation: load1 1s infinite ease-in-out;
  animation: load1 1s infinite ease-in-out;
  width: 1em;
  height: 4em;
}
.loader:before,
.loader:after {
  position: absolute;
  top: 0;
  content: '';
}
.loader:before {
  left: -1.5em;
  -webkit-animation-delay: -0.32s;
  animation-delay: -0.32s;
}
.loader {
  text-indent: -9999em;
  margin: auto;
  position: fixed;
   z-index: 999;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  font-size: 11px;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
  -webkit-animation-delay: -0.16s;
  animation-delay: -0.16s;
}
.loader:after {
  left: 1.5em;
}
@-webkit-keyframes load1 {
  0%,
  80%,
  100% {
    box-shadow: 0 0 #13495d;
    height: 4em;
  }
  40% {
    box-shadow: 0 -2em #13495d;
    height: 5em;
  }
}
@keyframes load1 {
  0%,
  80%,
  100% {
    box-shadow: 0 0 #13495d;
    height: 4em;
  }
  40% {
    box-shadow: 0 -2em #13495d;
    height: 5em;
  }
}

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