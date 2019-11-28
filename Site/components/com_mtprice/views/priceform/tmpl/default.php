<?php
/**
 * @version     1.0.0
 * @package     com_mtprice
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
$lang->load('com_mtprice', JPATH_ADMINISTRATOR);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/components/com_mtprice/assets/js/form.js');
$doc->addCustomTag("<meta name=\"robots\" content=\"noindex,nofollow\">");
		
$jinput = JFactory::getApplication()->input;
		
$price_type=$jinput->get('ptype', '', '');

$key_type=$jinput->get('ktype', '', '');
$state=$jinput->get('state', '', '');

$user=JFactory::getUser();
if($state=='')
{
	$pstate='New State';
}
else{
	
	$pstate=$state;
}

?>
<div class="wbc_h2container">
<span>
<?php
if($price_type=='b')
{
	echo 'Pricing for Buyers';
}
if($price_type=='s')
{
	echo 'Pricing for Sellers';
}
?>
</span>
</div>
</style>
<script type="text/javascript">
    if (jQuery === 'undefined') {
        document.addEventListener("DOMContentLoaded", function(event) { 
            jQuery('#form-price').submit(function(event) {
                
            });

            
        });
    } else {
        jQuery(document).ready(function() {
            jQuery('#form-price').submit(function(event) {
                
            });

            
        });
    }
</script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery(".custom-select select").each(function(){
            jQuery(this).wrap("<span class='select-wrapper'></span>");
            jQuery(this).after("<span class='holder'></span>");
			jQuery(this).removeClass( "chzn-done" );
			jQuery(this).removeAttr( "style" );
        });
        jQuery(".custom-select select").change(function(){
            var selectedOption = jQuery(this).find(":selected").text();
            jQuery(this).next(".holder").text(selectedOption);
        }).trigger('change');
		jQuery(".chzn-container").remove();
    })
</script>

<style type="text/css">
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

</style>
<div class="top_subheading_center">(
<?php if (!empty($this->item->id)): ?>
	<?php if($this->item->keypoints_type=='1')
	{
	echo 'Edit Key Point for Extra Charges';
	}
	if($this->item->keypoints_type=='3')
	{
		echo 'Edit Pricing for '.$this->item->au_state;
	}
	if($this->item->keypoints_type=='2')
	{
		echo 'Edit Key Point for Default Included Charges';
	}?>
<?php else: ?>
	<?php if($key_type=='1')
		{
			echo 'Add Key Point for Extra Charges';
		}
	 if($key_type=='3')
		{
			echo 'Add Price for '.$pstate;
		}
	if($key_type=='2')
		{
			echo 'Add Key Point for Default Included Charges';
		} ?>
	<?php endif; ?>
)
</div>
<br/>


<form id="form-price" action="<?php echo JRoute::_('index.php?option=com_mtprice'); ?>" method="post" enctype="multipart/form-data">
        
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

	<?php if(empty($this->item->user_id)): ?>
		<input type="hidden" name="jform[user_id]" value="<?php echo JFactory::getUser()->id; ?>" />
		<input type="hidden" name="jform[link_id]" value="<?php echo JFactory::getUser()->link_id; ?>" />
	<?php else: ?>
		<input type="hidden" name="jform[user_id]" value="<?php echo $this->item->user_id; ?>" />
		<input type="hidden" name="jform[link_id]" value="<?php echo $this->item->link_id; ?>" />
	<?php endif; ?>
	
	<div class="<?php if(!empty($this->item->id) and $key_type=='3'){echo 'hidden';}?>">
		<div class="profile_left_lable"><?php echo $this->form->getLabel('au_state'); ?></div>
		<div class="profile_left_txtbox custom-select"><?php if(empty($this->item->id) and !empty($state)){echo $this->form->getInput('au_state',null,$state);}else{echo $this->form->getInput('au_state');} ?></div>
	</div>
	<div class="clear"></div>
	
	<div class="<?php if(empty($this->item->id)){echo 'hidden';}?>">
		<div class="profile_left_lable"><label>Show/Hide Price</label></div>
		<div class="profile_left_txtbox custom-select"><?php echo $this->form->getInput('state'); ?></div>
	</div>
	<div class="clear"></div>
	
	<div class="<?php if($key_type=='3'){echo 'hidden';}?>">
		<div class="profile_left_lable"><?php  echo $this->form->getLabel('description'); ?></div>
		<div class="profile_left_txtbox"><?php if(empty($this->item->id) and $key_type=='3'){echo $this->form->getInput('description',null,'Service Price');}else{echo $this->form->getInput('description');} ?></div>
	</div>
	<div class="clear"></div>
	
	<div class="hidden">
		<div class="profile_left_lable"><?php echo $this->form->getLabel('price_type'); ?></div>
		<div class="profile_left_txtbox"><?php if(empty($this->item->id)){echo $this->form->getInput('price_type',null,$price_type);}else{echo $this->form->getInput('price_type');} ?></div>
	</div>
	<div class="clear"></div>
	
	<div class="hidden">
		<div class="profile_left_lable"><?php echo $this->form->getLabel('keypoints_type'); ?></div>
		<div class="profile_left_txtbox"><?php if(empty($this->item->id)){echo $this->form->getInput('keypoints_type',null,$key_type);}else{echo $this->form->getInput('keypoints_type');} ?></div>
	</div>
	<div class="clear"></div>
	
	<div class="<?php if($key_type=='2' or $this->item->keypoints_type=='2'){echo 'hidden';} ?>">
		<div class="profile_left_lable"><?php echo $this->form->getLabel('price'); ?></div>
		<div class="profile_left_txtbox"><?php echo $this->form->getInput('price'); ?></div>
	</div>
	<div class="clear"></div>
<?php if(!empty($this->item->id))
		{?>
			<!--Included keypoints-->
			
				<?php 
					if($this->item->price_type=='s')
					{
						$pricetype="Sellers";
					}
					else{
						$pricetype="Buyers";
					}
				?>
				<br />
				<div class="wbc_down_heding">What services do you include for <?php echo $pricetype;?>?</div>
				<div class="wbc_txtboxes_container">
						<?php 
							$keypoints=explode("|",$this->item->included_charges);
							$count=count($keypoints);
							$counter=1;
							foreach($keypoints as $key => $value)
							{ 
									if($counter==$count)
									{
										$include_keys.="jQuery('#include_key".$key."').val()";
									}
									else{
										$include_keys.="jQuery('#include_key".$key."').val()+'|'+";
									}
								
								?>
								<textarea name="txtkpoins" id="include_key<?php echo $key;?>" placeholder="Key Point Description" /><?php echo $value;?></textarea>
										
					<?php      $counter++;
							}
						?>
				</div>
				<div class="buttons_containers_pro">
					<button id="add_include" type="button" class="buttons_main blue_bg"><?php echo JText::_('Add More'); ?></button>
					<!--<button id="add_include_remove" type="button" class="buttons_main black_bg"><?php echo JText::_('Remove Empty/New Added Field(s)'); ?></button>-->
				</div>
				<hr style="border-top: 1px solid #ddd !important;"/>	
				
				<!--Extra keypoints-->
				<div class="wbc_down_heding">Extra Service Charges:</div>

						<?php 
							$keypoints=explode("|",$this->item->extra_charges);
							$count=count($keypoints);
							$counter=1;
							foreach($keypoints as $key => $value)
							{ 
									if($counter==$count)
									{
										$extra_keys.="jQuery('#extra_key".$key."').val()+'='+jQuery('#extra_price".$key."').val()";
									}
									else{
										$extra_keys.="jQuery('#extra_key".$key."').val()+'='+jQuery('#extra_price".$key."').val()+'|'+";
									}
									
									$price=explode("=",$value);
								?>

								<hr style="border-top: 1px solid #ddd !important;"/>	
								<div class="profile_left_lable">Description</div>
								<div class="profile_left_txtbox"><textarea name="txtkpoins" id="extra_key<?php echo $key;?>" placeholder="Key Point Description"/><?php echo $price[0];?></textarea></div>
								<div class="clear"></div>
								
								<div class="profile_left_lable">Price</div>
								<div class="profile_left_txtbox"><input id="extra_price<?php echo $key;?>" type="text"  placeholder="Numbers Only" name="expice" value="<?php echo $price[1];?>" /></div>
								<div class="clear"></div>
								
					<?php $counter++;}?>

				
				 <div class="buttons_containers_pro">
					<button id="add_extra" type="button" class="validate buttons_main blue_bg"><?php echo JText::_('Add More'); ?></button>
					<!--<button id="add_extra_remove" type="button" class="validate buttons_main black_bg"><?php echo JText::_('Remove Empty/New Added Field(s)'); ?></button>-->
				</div>
				<hr style="border-top: 1px solid #ddd !important;"/>	
<?php   }//if !empty(id)?>
        		
		<div class="buttons_containers_pro">
			<?php if(empty($this->item->id)){?>
				<button id="send" type="submit" class="buttons_main blue_bg"><?php echo JText::_('GOTO_STEP2');?></button>
			<?php }else{?>
				<button id="submit_keypoints" type="button" class="buttons_main blue_bg"><?php echo JText::_('JSUBMIT');?></button>
			<?php }?>
			<button type="button" onclick="history.back();" class="buttons_main black_bg"><?php echo JText::_('JCANCEL'); ?></button>
		</div>
		
		 <div class="control-group hidden">
           <div class="controls"><?php echo $this->form->getInput('included_charges'); ?></div>
		   <div class="controls"><?php echo $this->form->getInput('extra_charges'); ?></div>
        </div>
        <input type="hidden" name="jform[sid]" value="<?php echo $this->item->id;?>" />
		<input type="hidden" name="jform[price_type]" value="<?php echo $price_type	;?>" />
        <input type="hidden" name="option" value="com_mtprice" />
        <input type="hidden" name="task" value="priceform.save" />
	
        <?php echo JHtml::_('form.token'); ?>
    </form>
	<!--- gads commented for now
	<?php if(empty($this->item->id))
		    {
				if($price_type=='b')
				{?>
					<br />
					<h4>Note: You can Add & Edit this section below, after saving the price for this State.</h4>
					<h3>Included Service Charges:</h3>
					 <ul class="li-icon">
						<li><img alt="" src="img/search-list.png">All conveyancing work as defined in the Conveyancers Act 2006.</li>
						<li><img alt="" src="img/search-list.png">All legal advice forming part of the conveyancing work.</li>
						<li><img alt="" src="img/search-list.png">All legal work forming part of the conveyancing work.</li>
						<li><img alt="" src="img/search-list.png">All Searchs including</li>
					</ul>

					<ul class="li-icon w-100">
						<li><img alt="" src="img/search-list.png">All office costs (postage, faxes, phone calls, photocopying) forming part of the conveyancing work.</li>
						<li><img alt="" src="img/search-list.png">Providing of a copy of the Transfer of Land document to client’s bank or lender upon formal written  
							request from the lender.</li>
						<li><img alt="" src="img/search-list.png">Booking of settlement with client’s bank or lender.</li>
						<li><img alt="" src="img/search-list.png">Attend Settlement - the conveyancer will organise and attend the settlement on your behalf.</li>
						<li><img alt="" src="img/search-list.png">Submission of Notices of Acquisition.</li>
					</ul>

					<div class="last-li-icon">
						<h3 class="gray-heading">Extra charges: </h3>
						<ul class="li-icon w-100 singl">
							<li><img alt="" src="img/search-list.png">	Attend Settlement - the conveyancer will organise and attend the settlement on your behalf.<span class="pri">$85</span></li>
						</ul>
					</div>
				
 <?php      	}
				else
				{?>
						<h4>Note: You can Add & Edit this section below, after saving the price for this State.</h4>
						<h3>Included Service Charges:</h3>
						<ul class="li-icon w-100 seller" style="float:none !important">
							<li><img alt="" src="img/search-list.png">    Conveyancing supervised by a fully licensed Conveyancer </li>
							<li><img alt="" src="img/search-list.png"> Preparation of Section <span>32 Vendor’s Statement.</span></li>
							<li><img alt="" src="img/search-list.png"> Preparation of <span>Contract of Sale.</span></li>
							<li><img alt="" src="img/search-list.png">  Preparation of Section 27 Deposit Release Statement.</li>
							<li><img alt="" src="img/search-list.png">  Supervision of real estate agent re legal compliance.</li>
							<li><img alt="" src="img/search-list.png">   All <span> conveyancing work</span> as defined in the Legal Practice Act 1996.</li>
							<li><img alt="" src="img/search-list.png">   All <span>legal advice</span> forming part of the <span>conveyancing work.</span></li>
							<li><img alt="" src="img/search-list.png">   All legal work forming part of the <span>conveyancing work.</span></li>
							<li><img alt="" src="img/search-list.png">   All office costs (postage, faxes, phone calls, photocopying) forming part of the <span> conveyancing work. </span></li>
							<li><img alt="" src="img/search-list.png">   Obtaining of mortgage “payout figure” from Lender.</li>
							<li><img alt="" src="img/search-list.png">   Booking of <span> settlement </span> with client’s bank or lender.</li>
							<li><img alt="" src="img/search-list.png">  <span> Settlement </span> at our office.</li>
							<li><img alt="" src="img/search-list.png">   Submission of Notices of Disposition.</li>
						</ul>
						<div class="p-last" style="float:none !important">
							<h3 class="gray-heading">Extra charges: </h3>
							<div class="rate-last">
								<ul>
									<li>Preparation of Contract of Sale. (No Real Estate agent involvement)		<span class="pric">$85</span></li>
									<li>Property sold via auction fee                                                   <span class="pric">$200</span>             </li>
								</ul>
							</div>
						</div>
	<?php		}
			}?>
	-->
	
	
	<!--<br />
	<h3>Included Service Charges</h3>
	<?php
		// $db =  JFactory::getDbo();
				// $query = "SELECT * FROM #__mt_price where user_id='".$user."' and keypoints_type ='2' and price_type='".$this->item->price_type."' and au_state='".$this->item->au_state."' order by ordering";
				// $db->setQuery($query);
				// $row = $db->loadObjectList();
				
				// $no_in_charges=count($row);
			
				// foreach($row as $key => $val)
				// {	$forms.='#form-include'.$key.' '; ?>
				 
					 <form id="form-include<?php echo $key;?>" action="<?php echo JRoute::_('index.php?option=com_mtprice&task=price.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
						<input type="hidden" name="jform[id]" value="<?php echo $val->id; ?>" />

						<input type="hidden" name="jform[ordering]" value="<?php echo $val->ordering; ?>" />

						<input type="hidden" name="jform[state]" value="<?php echo $val->state; ?>" />

						<?php if(empty($this->item->user_id)): ?>
							<input type="hidden" name="jform[user_id]" value="<?php echo JFactory::getUser()->id; ?>" />
						<?php else: ?>
							<input type="hidden" name="jform[user_id]" value="<?php echo $val->user_id; ?>" />
						<?php endif; ?>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('au_state'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('au_state',null,$val->au_state); ?></div>
						</div>
				
						<div class="control-group">
							<div class="control-label hidden"><?php echo $this->form->getLabel('description'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('description',null,$val->description); ?></div>
						</div>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('price_type'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('price_type',null,$val->price_type); ?></div>
						</div>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('keypoints_type'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('keypoints_type',null,$val->keypoints_type); ?></div>
						</div>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('price'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('price',null,$val->price); ?></div>
						</div>
							
							
							<input type="hidden" name="option" value="com_mtprice" />
							<input type="hidden" name="task" value="priceform.save" />
							<input type="hidden" name="jform[sid]" value="<?php echo $this->item->id;?>" />
							<input type="hidden" name="ptype" value="<?php echo $val->price_type;?>" />
							<input type="hidden" name="ktype" value="<?php echo $val->keypoints_type;?>" />
							<?php echo JHtml::_('form.token'); ?>
					</form>
					<hr style="border-top: 2px solid #ddd !important;"/>	
	<?php  		//}?>	
					<div class="control-group">
						<div class="controls">
							<button id="include" type="submit" class="validate btn btn-primary"><?php echo JText::_('Update Key Point'); ?></button>
							<!--<a class="btn" href="<?php echo JRoute::_('index.php?option=com_mtprice&task=priceform.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
						</div>
					</div>
					
					
	<!--Add new keypoints
					
					<form id="form-include" action="<?php echo JRoute::_('index.php?option=com_mtprice&task=price.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
						<input type="hidden" name="jform[id]" value="<?php echo $val->id; ?>" />

						<input type="hidden" name="jform[ordering]" value="<?php echo $val->ordering; ?>" />

						<input type="hidden" name="jform[state]" value="<?php echo $val->state; ?>" />

						<?php if(empty($this->item->user_id)): ?>
							<input type="hidden" name="jform[user_id]" value="<?php echo JFactory::getUser()->id; ?>" />
						<?php else: ?>
							<input type="hidden" name="jform[user_id]" value="<?php echo $val->user_id; ?>" />
						<?php endif; ?>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('au_state'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('au_state',null,$val->au_state); ?></div>
						</div>
						<div class="control-group">
							
							<div class="control-label hidden"><?php echo $this->form->getLabel('description'); ?></div>
							<div class="controls"><h4>Add New Key Points for Included Charges</h4><?php echo $this->form->getInput('description',null,''); ?></div>
						</div>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('price_type'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('price_type',null,$val->price_type); ?></div>
						</div>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('keypoints_type'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('keypoints_type',null,$val->keypoints_type); ?></div>
						</div>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('price'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('price',null,$val->price); ?></div>
						</div>
							<div class="control-group">
								<div class="controls">
									<button type="submit" class="validate btn btn-primary"><?php echo JText::_('Save New Key Points'); ?></button>
									<!--<a class="btn" href="<?php echo JRoute::_('index.php?option=com_mtprice&task=priceform.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
								</div>
							</div>
							
							<input type="hidden" name="option" value="com_mtprice" />
							<input type="hidden" name="task" value="priceform.save" />
							<input type="hidden" name="jform[sid]" value="<?php echo $this->item->id;?>" />
							<input type="hidden" name="ptype" value="<?php echo $val->price_type;?>" />
							<input type="hidden" name="ktype" value="<?php echo $val->keypoints_type;?>" />
							<?php echo JHtml::_('form.token'); ?>
					</form>
					<hr style="border-top: 3px solid #ddd !important;"/>	
					
					
					

<br />
	<h3>Extra Service Charges</h3>
	<?php
		// $db =  JFactory::getDbo();
				// $query = "SELECT * FROM #__mt_price where user_id='".$user."' and keypoints_type ='1' and price_type='".$this->item->price_type."' and au_state='".$this->item->au_state."' order by ordering";
				// $db->setQuery($query);
				// $row = $db->loadObjectList();
				
				// foreach($row as $key => $val)
				// {?>
				 
					 <form id="form-extra<?php echo $key;?>" action="<?php echo JRoute::_('index.php?option=com_mtprice&task=price.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
						<input type="hidden" name="jform[id]" value="<?php echo $val->id; ?>" />

						<input type="hidden" name="jform[ordering]" value="<?php echo $val->ordering; ?>" />

						<input type="hidden" name="jform[state]" value="<?php echo $val->state; ?>" />

						<?php if(empty($this->item->user_id)): ?>
							<input type="hidden" name="jform[user_id]" value="<?php echo JFactory::getUser()->id; ?>" />
						<?php else: ?>
							<input type="hidden" name="jform[user_id]" value="<?php echo $val->user_id; ?>" />
						<?php endif; ?>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('au_state'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('au_state',null,$val->au_state); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label hidden"><?php echo $this->form->getLabel('description'); ?></div>
							<div class="controls"><h4><?php echo $this->form->getInput('description',null,$val->description); ?></div>
						</div>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('price_type'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('price_type',null,$val->price_type); ?></div>
						</div>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('keypoints_type'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('keypoints_type',null,$val->keypoints_type); ?></div>
						</div>
						<div class="control-group ">
							<div class="control-label"><?php echo $this->form->getLabel('price'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('price',null,$val->price); ?></div>
						</div>
							<div class="control-group">
								<div class="controls">
									<button type="submit" class="validate btn btn-primary"><?php echo JText::_('Update Key Point'); ?></button>
									<!--<a class="btn" href="<?php echo JRoute::_('index.php?option=com_mtprice&task=priceform.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
								</div>
							</div>
							
							<input type="hidden" name="option" value="com_mtprice" />
							<input type="hidden" name="task" value="priceform.save" />
							<input type="hidden" name="jform[sid]" value="<?php echo $this->item->id;?>" />
							<input type="hidden" name="ptype" value="<?php echo $val->price_type;?>" />
							<input type="hidden" name="ktype" value="<?php echo $val->keypoints_type;?>" />
							<?php echo JHtml::_('form.token'); ?>
					</form>
					<hr style="border-top: 2px solid #ddd !important;"/>	
	<?php  		//}?>			
	<!--Add new keypoints
					
					<form id="form-extra" action="<?php echo JRoute::_('index.php?option=com_mtprice&task=price.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
						<input type="hidden" name="jform[id]" value="<?php echo $val->id; ?>" />

						<input type="hidden" name="jform[ordering]" value="<?php echo $val->ordering; ?>" />

						<input type="hidden" name="jform[state]" value="<?php echo $val->state; ?>" />

						<?php if(empty($this->item->user_id)): ?>
							<input type="hidden" name="jform[user_id]" value="<?php echo JFactory::getUser()->id; ?>" />
						<?php else: ?>
							<input type="hidden" name="jform[user_id]" value="<?php echo $val->user_id; ?>" />
						<?php endif; ?>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('au_state'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('au_state',null,$val->au_state); ?></div>
						</div>
						<div class="control-group">
							
							<div class="control-label hidden"><?php echo $this->form->getLabel('description'); ?></div>
							<div class="controls"><h4>Add New Key Points for Extra Charges</h4><?php echo $this->form->getInput('description',null,''); ?></div>
							
						</div>
						<div class="control-group ">
								<div class="control-label"><?php echo $this->form->getLabel('price'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('price',null,''); ?></div>
						</div>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('price_type'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('price_type',null,$val->price_type); ?></div>
						</div>
						<div class="control-group hidden">
							<div class="control-label"><?php echo $this->form->getLabel('keypoints_type'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('keypoints_type',null,$val->keypoints_type); ?></div>
						</div>

							<div class="control-group">
								<div class="controls">
									<button type="submit" class="validate btn btn-primary"><?php echo JText::_('Save New Key Points'); ?></button>
									<!--<a class="btn" href="<?php echo JRoute::_('index.php?option=com_mtprice&task=priceform.cancel'); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
								</div>
							</div>
							
							<input type="hidden" name="option" value="com_mtprice" />
							<input type="hidden" name="task" value="priceform.save" />
							<input type="hidden" name="jform[sid]" value="<?php echo $this->item->id;?>" />
							<input type="hidden" name="ptype" value="<?php echo $val->price_type;?>" />
							<input type="hidden" name="ktype" value="<?php echo $val->keypoints_type;?>" />
							<?php echo JHtml::_('form.token'); ?>
					</form>
					<hr style="border-top: 3px solid #ddd !important;"/>	-->
	
	
	
	
	<!--<br/><br/>
	<?php
	// if($key_type!='3')
	// {
	?>
	<form id="form-copy" action="<?php echo JRoute::_('index.php?option=com_mtprice&task=price.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
	<h2>Copy Previous Key Points</h2><br/>
	<label>Filter Key Points by State</label>
	<select id="filter_state" >
				<option value="">Select Au State</option>
				<option value="ACT">ACT</option>
                <option value="NSW">NSW</option>
                <option value="NT">NT</option>
                <option value="QLD">QLD</option>
                <option value="SA">SA</option>
                <option value="TAS">TAS</option>
                <option value="VIC">VIC</option>
                <option value="WA">WA</option>
	</select>
	
	<table  class="table table-striped" >
		<thead>
			<tr>
				<th class='left'>
					Copy
				</th>
				<th class='left'>
					Applicable&nbsp;State
				</th>
				<th class='left'>
					Description
				</th>
				<th class='left'>
					Price
				</th>
			</tr>
		</thead>
		
		<tbody>
			
				<?php 
				  foreach($this->prevpoints as $key => $kpoint)
				  {?>
					<tr>
						<td>
							<input name="jform[prevpoints][]" type="checkbox" value="<?php echo $kpoint->id; ?>">
						</td>
						<td>
							<?php echo $kpoint->au_state; ?>
						</td>
						<td>
							<?php echo $kpoint->description; ?>
						</td>
						<td>
							<?php echo $kpoint->price; ?>
						</td>
					</tr>
						  
			<?php }
				  ?>
			
		</tbody>
	</table>
	<button id="copy" type="submit" class="validate btn btn-primary"><?php echo JText::_('Copy selected Key Points to this State'); ?></button>
	 <input type="hidden" name="option" value="com_mtprice" />
     <input type="hidden" name="task" value="priceform.copy" />
	 <input type="hidden" name="state" value="<?php echo  $state;?>" />
	
     
	 </form>-->
	<?php
	//}
	?>
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
					<input id="signup" type="button" onClick="jQuery('#form-price').submit();" value="Sign Up Now" class="buttons_mainl">		
				</div>

			</div>
		</div>
	  
	</div>

<!--end gads signup modal-->
<div id="load" class="loader" style="display:none;" ></div>
<script> 
//gads ERROR handling


var c = jQuery.noConflict();
	c(document).ready(function() {
	c('#signupModal').modal('hide');
	//gads redirect signup
		   <?php
				//---gads redirect list with us---//
				$link_id=$this->item->link_id;
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

<?php 
	if(empty($this->item->id) && $user->name)
	{?>
		 c('#jform_price').click(function(){

			
			var state=c('#jform_au_state').val();
				 c.ajax({
					  type: "POST",
					  dataType:'text',
					  url: 'index.php?option=com_mtprice&task=nopricestate&format=raw&ktype=<?php echo $key_type;?>&ptype=<?php echo $price_type;?>&state='+state,
					   beforeSend: function() {
							// setting a timeout
							//jQuery("#url_scategory").addClass('ajaxloader');		
							},
					  success:function(data){
								
							if(data==state)
							{
								alert('Please select another State, you have already set the price for '+state+'. If you want to edit price for this State, click Cancel then go to Actions Column and Clict the edit icon.');
								c('#jform_price').blur();
								c'#jform_price').val("");
							}		
							
							
					  },
					  complete: function() {
							
						}
					});
					
			});
			
		 c('#jform_au_state').change(function(){

			
			var state=c('#jform_au_state').val();
				 c.ajax({
					  type: "POST",
					  dataType:'text',
					  url: 'index.php?option=com_mtprice&task=nopricestate&format=raw&ktype=<?php echo $key_type;?>&ptype=<?php echo $price_type;?>&state='+state,
					   beforeSend: function() {
							// setting a timeout
							//jQuery("#url_scategory").addClass('ajaxloader');		
							},
					  success:function(data){
								
							if(data==state)
							{
								alert('Please select another State, you have already set the price for '+state+'. If you want to edit price for this State, click Cancel then go to Actions Column and Clict the edit icon.');
								c('#jform_price').blur();
								c('#jform_price').val("");
							}		
							
							
					  },
					  complete: function() {
							
						}
					});
					
			});
			
			
		c('#jform_price').keyup(function(){

			var state=c('#jform_au_state').val();
				 c.ajax({
					  type: "POST",
					  dataType:'text',
					  url: 'index.php?option=com_mtprice&task=nopricestate&format=raw&ktype=<?php echo $key_type;?>&ptype=<?php echo $price_type;?>&state='+state,
					   beforeSend: function() {
							// setting a timeout
							//jQuery("#url_scategory").addClass('ajaxloader');		
							},
					  success:function(data){
								
							if(data==state)
							{
								alert('Please select another State, you have already set the price for '+state+'. If you want to edit price for this State, click Cancel then go to Actions Column and Clict the edit icon.');
								c('#jform_price').blur();
								c('#jform_price').val("");
							}		
							
							
					  },
					  complete: function() {
							
						}
					});
					
			});
<?php }?>
	
	//filte State
	// jQuery('#filter_state').change(function(){
		// var state=jQuery('#filter_state').val();
	
		 // jQuery.ajax({
			  // type: "POST",
			  // dataType:'text',
			  // url: 'index.php?option=com_mtprice&task=priceform.edit&id=<?php echo $this->item->id;?>&format=raw&filstate='+state,
			   // beforeSend: function() {
					// // setting a timeout
					// //jQuery("#url_scategory").addClass('ajaxloader');		
					// },
			  // success:function(data){
					// window.location.href = "<?php echo 'index.php?option=com_mtprice&view=priceform&layout=edit&ptype='.$this->item->price_type.'&ktype='.$this->item->keypoints_type.'&state='.$this->item->au_state.'&sid='.$this->item->id; ?>&Itemid=224&filstate="+state
			  // },
			  // complete: function() {
					
				// }
			// });
		
		
	// });
	
			
	<?php 
	if($user->name)
	{?>
			var submit=0;
			c('#submit_keypoints').click(function() {
				 c('.loader').attr("style","");
						 
								
				
				c('#add_include').click();
				c('#add_extra').click();

				 c('#jform_included_charges').val(<?php echo $include_keys;?>);
				 c('#jform_extra_charges').val(<?php echo $extra_keys;?>);
				 
				  c("textarea").filter( function() {
					return c.trim(jQuery(this).val()) == '';
				}).remove(); 

				c('#add_include').click();
				submit=1;
				c('#add_extra').click();
				
				
			});
			
			//gads add included_charges
			c('#add_include').click(function() {
					
				//jQuery('#jform_included_charges').val(<?php echo $include_keys;?>+'|');
				c('.loader').attr("style","");
				c.post("index.php?option=com_mtprice&task=add_include_charges&format=raw",
								{included_charges:<?php echo $include_keys;?>+'|',id:<?php echo $this->item->id;?>},
								function (data, status) {
									var obj = c.parseJSON(data);
									if(obj==1)
									{
										if(submit!=1)
										{
										window.location.reload();
										}
										c('.loader').attr("style","display:none;");
					
									}
									
								}
							);
			});
			
			//gads extra_charges
			c('#add_extra').click(function() {
				
				//jQuery('#jform_extra_charges').val(<?php echo $extra_keys;?>+'|=');
				c('.loader').attr("style","");
				c.post("index.php?option=com_mtprice&task=add_extra_charges&format=raw",
								{extra_charges:<?php echo $extra_keys;?>+'|',id:<?php echo $this->item->id;?>},
								function (data, status) {
									var obj = c.parseJSON(data);
									if(obj==1)
									{
										if(submit!=1)
										{
											window.location.reload();
										}
										c('.loader').attr("style","display:none;");
									}
									if(submit==1)
									{
																										
										c("#form-price").submit();
										
									}
									 
								}
							);
			});
			
<?php }?>
	//gads remove included_charges
	//---commented for now--//
	// jQuery('#add_include_remove').click(function() {
		// jQuery('.loader').attr("style","");
		// jQuery("textarea").filter( function() {
            // return jQuery.trim(jQuery(this).html()) == '';
        // }).remove();
		
		// //jQuery('#jform_included_charges').val(<?php echo $include_keys;?>);
		// jQuery.post("index.php?option=com_mtprice&task=add_include_charges&format=raw",
						// {included_charges:<?php echo $include_keys;?>,id:<?php echo $this->item->id;?>},
						// function (data, status) {
							// var obj = jQuery.parseJSON(data);
							
							// if(obj==1)
							// {
								// window.location.reload();
								// jQuery('.loader').attr("style","display:none;");
							// }
						// }
					// );
	// });
	
		//extra_charges
		//---commented for now--//
		// jQuery('#add_extra_remove').click(function() {
			// jQuery('.loader').attr("style","");
			// jQuery("textarea").filter( function() {
				// return jQuery.trim(jQuery(this).html()) == '';
			// }).remove();
			
			
			// //jQuery('#jform_extra_charges').val(<?php echo $extra_keys;?>);
			
			// jQuery.post("index.php?option=com_mtprice&task=add_extra_charges&format=raw",
							// {extra_charges:<?php echo $extra_keys;?>,id:<?php echo $this->item->id;?>},
							// function (data, status) {
								// var obj = jQuery.parseJSON(data);
								// if(obj==1)
								// {
									
									// window.location.reload();
									// jQuery('.loader').attr("style","display:none;");
								// }
								
							// }
						// );
		// });
	
	
	
</script>