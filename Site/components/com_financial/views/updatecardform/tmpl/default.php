<?php
/**
 * @version     1.0.0
 * @package     com_financial
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
$lang->load('com_financial', JPATH_SITE);
//$doc = JFactory::getDocument();
//$doc->addScript(JUri::base() . '/components/com_financial/assets/js/form.js');
$user=JFactory::getUser();

?>
</style>
<script type="text/javascript">
    if (jQuery === 'undefined') {
        document.addEventListener("DOMContentLoaded", function(event) { 
            jQuery('#form-updatecard').submit(function(event) {
                
            });

            
        });
    } else {
        jQuery(document).ready(function() {
            jQuery('#form-updatecard').submit(function(event) {
                
            });

            
        });
    }
</script>
<script type="text/javascript">
    jQuery(document).ready(function(){
		var i = 0;
        jQuery(".custom-select").each(function(){
			i++;
			if(i%2 == 1){
				jQuery(this).wrap("<span class='select-wrapper left'></span>");
			} else {
				jQuery(this).wrap("<span class='select-wrapper'></span>");
			}
            // jQuery(this).wrap("<span class='select-wrapper'></span>");
            jQuery(this).after("<span class='holder'></span>");
			jQuery(this).removeClass( "chzn-done" );
			jQuery(this).removeAttr( "style" );
        });
        jQuery(".custom-select").change(function(){
            var selectedOption = jQuery(this).find(":selected").text();
            jQuery(this).next(".holder").text(selectedOption);
        }).trigger('change');
		jQuery(".chzn-container").remove();
    })
</script>
<form id="form-updatecard" action="<?php echo JRoute::_('index.php?option=com_financial&task=updatecard.update'); ?>" method="post" enctype="multipart/form-data">
	<div id="upgrade_subscription"></div>
	
	<?php //---gads sneak peek banner---//
		$user=JFactory::getUser();
		$db=JFactory::getDBO();
		$query="Select link_id from #__users Where id=".$user->id;
		$db->setQuery($query);
		$link_id=$db->loadObject()->link_id;
		if(!$user->name)
		{
		echo '<h3 class="contentheading" align="center" style="color:#ff4d00;font-size: 21px;word-spacing: 2px;line-height: inherit;">
				TO EDIT AND LIST YOUR BUSINESS CLICK BELOW<br/>
				<a align="center" id="active" href="'.JRoute::_('index.php?option=com_chargify&view=registers&link_id='.$this->link->link_id).'" style="background-color: #ff4d00;border-color: #ff4d00;padding: 15px;font-size: 15px;" class="btn btn-primary">
					ACTIVATE YOUR LISTING FROM ONLY $11 PER MONTH
				</a>
			  </h3> 
				';
		}
	?>
	
	<div class="wbc_h2container"><span>update credit card</span></div>
	<div class="wbc_txtboxes_container marg_top">
		<input type="text" id="fn" name="jform[firstname]" required="" placeholder="First Name on Card*" value="">
		<input type="text" id="ln" name="jform[lastname]" required="" placeholder="Last Name on Card*" value="">
		<div class="two_txt_boxes_cont">
			<input type="text" name="jform[cardno]" required="" placeholder="Card Number*" class="cardnumber_txtbox">
			<img style="padding-right: 5px;" src="/images/visa.png"><img style="padding-right: 5px;" src="/images/master-card.png">
			<input type="text" name="jform[cvv]" required="" placeholder="CVV*" class="cvvnumber_txtbox">
			<div class="clear"></div>
			<select name="jform[expiremonth]" class="custom-select">
				<option>Expiry Month*</option>
				<?php for($i=1;$i<=12;$i++):?>
				<option value="<?php echo $i;?>"><?php echo $i;?></option>
				<?php endfor;?>
			 </select>
			<select name="jform[expireyear]" class="custom-select">
				<option>Expiry Year*</option>
				<?php for($i=2015;$i<=date('Y')+10;$i++):?>
				<option value="<?php echo $i;?>"><?php echo $i;?></option>
				<?php endfor;?>
			</select>
			<div class="securiity_pic"><img src="<?php echo JURI::base(); ?>/templates/property/img/security_img.jpg" width="367" height="104" alt=""></div>
		</div>
	</div> 
	<div class="buttons_containers_pro">
	  <input id="send" name="" type="submit" value="update credit card" class="buttons_main blue_bg long_button_adjust">
	</div>
	<input type="hidden" name="jform[link_id]" value="<?php echo $link_id;?>"/> 
	<input type="hidden" name="option" value="com_financial" />
	<input type="hidden" name="task" value="updatecardform.update" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<!--</div>-->

<!--gads signup modal-->

	<div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="width: 25%;position:fixed;left: 75%;">
	  
		<div id="signModal" class="modal-content" style="">
			<div class="modal-header">
				<button type="button" class="close" onClick=" jQuery('.modal-backdrop').remove();" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 style="font-size: 18px;color: #41565b;font-weight: bold;">
				
				</h4>
			</div>
			<div class="modal-body" style="display: inline-block;left: 5px;">
				<h4 style="line-height:25px;" class="s-h4">Before you start updating your listing you will need to sign up to activate your listing.</h4>
			</div>
			<div class="modal-footer" style="text-align:left !important;">

				<div id="foot" class="col-md sp2"> 
					<input id="signup" type="button" onClick="jQuery('#form-updatecard').submit();" value="Sign Up Now" class="buttons_mainl">		
				</div>

			</div>
		</div>
	  
	</div>

<!--end gads signup modal-->

<script>
var c = jQuery.noConflict();
	c(document).ready(function() {
	c('#signupModal').modal('hide');
	//gads redirect signup
		   <?php
				//---gads redirect list with us---//
				$link_id = $this->link->link_id;
				if(empty($link_id))
				{
					$link_id=JFactory::getUser()->link_id;
				}
				
				$db =  JFactory::getDbo();
				$query= "Select link_published,sub_id From #__mt_links Where link_id='{$link_id}'";
				$db->setQuery($query);
				$link_status = $db->loadObject();
				
				if(!$user->name || $link_status->link_published !='1' || !$link_status->sub_id)
				{?>
				
				c('#send').attr('data-toggle','modal');
				c('#send').attr('data-target','#signupModal');
				c('#send').attr('type','button');
				
		<?php	}?>	
});
</script>