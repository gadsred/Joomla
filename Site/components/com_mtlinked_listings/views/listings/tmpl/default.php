<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Mtlinked_listings
 * @author     gadiel_Rojo <gadsred@gmail.com>
 * @copyright  Copyright (C) 2016. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_mtlinked_listings');
$canEdit    = $user->authorise('core.edit', 'com_mtlinked_listings');
$canCheckin = $user->authorise('core.manage', 'com_mtlinked_listings');
$canChange  = $user->authorise('core.edit.state', 'com_mtlinked_listings');
$canDelete  = $user->authorise('core.delete', 'com_mtlinked_listings');

$old_id=JRequest::getVar('old_id');

if($this->current_link_id != $this->master_link_id)
{
	$app = JFactory::getApplication();
	// $msg="In order to give you access to the Multiple Location tab we require you to access this via your Master Account. 
			// We have now automatically switched you over. Please click the Add Multiple locations to continue.";
	// $app->redirect("index.php?option=com_mtlinked_listings&task=changelisting&id=$this->master_link_id",$msg);
		
	//gads get master
	$db = JFactory::getDBO();
	$query = "Select main_link From #__mt_linked_listings Where user_id='$userId' group by main_link";
	$db->setQuery($query);
	$master_id = $db->loadObject()->main_link;
	
	//auto change to master listing
	$db =  JFactory::getDbo();
	$query = "Update #__users Set 
					link_id='$master_id'
				Where id='$userId'";
	$db->setQuery($query);
	$db->execute();
	
	$app->redirect("index.php?option=com_mtlinked_listings&Itemid=298&lang=en&view=listings");
	
}

//---gads redirect list with us---//
			$db =  JFactory::getDbo();
			$query= "Select link_published,sub_id,invite,invite_code,invite_open From #__mt_links Where link_id='{$this->current_link_id}'";
			$db->setQuery($query);
			$link_status = $db->loadObject();
			if($link_status->link_published !='1' || !$link_status->sub_id)
			{
				// $app=JFactory::getApplication();
				// $app->redirect('index.php?option=com_chargify&view=registers&link_id='.$data['link_id']);
			}
	
?>

<style>
.col-md-6.sp1 ,.sign-up .col-md-6.sp2{
    background: #dbb003 none repeat scroll 0 0;
    padding:20px;
    width: 48%;
}

.col-md-6.sp2 {
    background: #20414c;
    padding: 20px;
	left: 10px;
}

.buttons_mainl {
	padding: 10px 15px;
    margin-top: 20px;
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
.buttons_mainl:disabled {
	padding: 10px 15px;
    margin-top: 20px;
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

p.up-arrow {
	display: inline-block;
	position: relative;
	background: #00576E;
	color: #FFF;
	text-decoration: none;
	padding: 20px;
	left:70%;
	width:33%;
	text-align:left;
	border-radius: 10px;
}
p.up-arrow:after {
	content: '';
	display: block;  
	position: absolute;
	left: 45%;
	bottom: 100%;
	width: 0;
	height: 0;
	border-bottom: 20px solid #00576E;
	border-top: 10px solid transparent;
	border-left: 10px solid transparent;
	border-right: 10px solid transparent;
	
}

</style>

<?php //---gads sneak peek banner---//
	$user=JFactory::getUser();
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
	<?php if($this->listings)
			{ ?>
				<h4 style="color: #2d566c;">We have looked through our Database and found the following listings that you may wish to link to:</h4>
				<div class="faq_table_container">
					<div class="staff_profile_row">
					<div class="staff_profile_box1 faq_topleft_border pi_heading"><?php echo JText::_('NAME'); ?></div>
					<div class="staff_profile_box2 faq_topleft_border pi_heading"><?php echo JText::_('ADDRESS'); ?></div>
					<div class="staff_profile_box4 faq_topleft_border_last pi_heading"><?php echo JText::_('ACTION'); ?></div>
					</div>
					<?php 
						foreach($this->listings as $i => $listing)
						{
							$gray_bg = ''; if ($i % 2 == 1) $gray_bg = 'faq_gray_bg';
							$address = ($listing->address ? $listing->address.', ': '');
							$city = ($listing->city ? $listing->city.', ': '');
							$state = ($listing->state ? $listing->state.', ': '');
							$postcode = ($listing->postcode ? $listing->postcode: '');
							$formated_address = $address.$city.$state.$postcode;
					?>
						<div class="staff_profile_row">
						<div class="staff_profile_box1 faq_topleft_border pi_normal border_top_mobile <?php echo $gray_bg; ?>"><?php echo $listing->link_name  ?></div>
						<div class="staff_profile_box2 faq_topleft_border faq_topleft_border pi_normal <?php echo $gray_bg; ?>"><?php echo $formated_address;  ?></div>
						<div class="staff_profile_box4 faq_topleft_border_last pi_normal border_bottom_mobile <?php echo $gray_bg; ?>">
							<button 
								onClick="$('#jform_address').val(this.value);$('#jform_sub_link').val(this.id);"
								id="<?php echo $listing->link_id ?>"
								value="<?php echo $listing->link_name.' '.$listing->address.', '.$listing->city.', '.$listing->state.', '.$listing->postcode;  ?>" 
								type="button" data-toggle="modal" data-target="#linkModal"
								class="btn btn-primary">
								<?php echo JText::_('Link'); ?>
							</button>
						</div>
						</div>
					<?php 
						}
					?>
					<div class="faq_bottom_heading_border"></div>
				</div>
				<!--<table class="table table-striped" id="listingList">
						<thead>
							<tr>
								<th width="" style="color: #2d566c;">
									<?php echo JText::_('NAME'); ?>
								</th>
								<th width="" style="color: #2d566c;">
									<?php echo JText::_('ADDRESS'); ?>
								</th>
								<th width="" style="color: #2d566c;">
									<?php echo JText::_('ACTION'); ?>
								</th>
							</tr>
						</thead>
						<tbody>
						<?php 
							foreach($this->listings as $listing)
							{
						?>
								<tr>
									<td>
										<?php echo $listing->link_name  ?>
									</td>
									<td>
										<?php echo $listing->address.', '.$listing->city.', '.$listing->state.', '.$listing->postcode;  ?>
									</td>
									<td>
										<button 
											onClick="$('#jform_address').val(this.value);$('#jform_sub_link').val(this.id);"
											id="<?php echo $listing->link_id ?>"
											value="<?php echo $listing->link_name.' '.$listing->address.', '.$listing->city.', '.$listing->state.', '.$listing->postcode;  ?>" 
											type="button" data-toggle="modal" data-target="#linkModal"
											class="btn btn-primary">
											<?php echo JText::_('Link'); ?>
										</button>
									</td>
								</tr>
						<?php 
							}
						?>
						</tbody>
				</table>-->
	<?php   } ?>
	
	
<form action="<?php echo JRoute::_('index.php?option=com_mtlinked_listings&view=listings'); ?>" method="post"
      name="adminForm" id="adminForm">

	<?php //echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
<?php if($this->items)
			{ ?>	
				<h4 style="color: #2d566c;">Listings you have linked:</h4>
				<div class="faq_table_container">
					<div class="staff_profile_row">
					<div class="staff_profile_box1 faq_topleft_border pi_heading"><?php echo JText::_('NAME'); ?></div>
					<div class="staff_profile_box2 faq_topleft_border pi_heading"><?php echo JText::_('ADDRESS'); ?></div>
					<div class="staff_profile_box4 faq_topleft_border_last pi_heading"><?php echo JText::_('ACTION'); ?></div>
					</div>
					<?php 
						foreach($this->items as $i => $item)
						{
							$gray_bg = ''; if ($i % 2 == 1) $gray_bg = 'faq_gray_bg';
							$address = ($item->address ? $item->address.', ': '');
							$city = ($item->city ? $item->city.', ': '');
							$state = ($item->state ? $item->state.', ': '');
							$postcode = ($item->postcode ? $item->postcode: '');
							$formated_address = $address.$city.$state.$postcode;
					?>
						<div class="staff_profile_row">
						<div class="staff_profile_box1 faq_topleft_border pi_normal border_top_mobile <?php echo $gray_bg; ?>"><?php echo $item->link_name  ?></div>
						<div class="staff_profile_box2 faq_topleft_border faq_topleft_border pi_normal <?php echo $gray_bg; ?>">
							<?php 
								if(!empty($item->city))
								{
									echo $formated_address;  
								}
								else
								{
									echo '<p style="color: #41565b;font-size: 16px;">Press Login to update address</p>';
								}
							?>
						</div>
						<div class="staff_profile_box4 faq_topleft_border_last pi_normal border_bottom_mobile <?php echo $gray_bg; ?>">
							<?php
								$db =  JFactory::getDbo();
								$query = "Select id From #__mt_price Where link_id ='$item->link_id'";
								$db->setQuery($query);
								$pricing = $db->loadObjectList();
								
								if($pricing) 
								{?>
									<a href="<?php echo 'index.php?option=com_mtree&task=editlisting&link_id='.$item->link_id;?>"
										class="btn btn-success btn-small"type="button">
										<?php echo JText::_('Login'); ?>
									</a>
						<?php 	}
								else
								{?>	
									<a 
										href="<?php echo JRoute::_('#', false, 2); ?>" 
										class="btn btn-success btn-small"
										onClick="$('#no').attr('href','<?php echo 'index.php?option=com_mtlinked_listings&task=changelisting&id='.$item->link_id;?>');
												 $('#yes').attr('href','<?php echo 'index.php?option=com_mtlinked_listings&task=copydata&id='.$item->link_id;?>');"
										id="<?php echo $item->link_id ?>"
										type="button" data-toggle="modal" data-target="#loginModal"
									>
									<?php echo JText::_('Login'); ?>
									</a>
						<?php 	}?>
						</div>
						</div>
					<?php }	?>
						<div class="faq_bottom_heading_border"></div>
						<?php
								if(!empty($old_id))
								{?>
									<p class="up-arrow" >
										Success, your new listing has been created. 
										Press Login to update your listing. 
									</p>
						<?php 	}?>
				</div>
				
				<!--<table class="table table-striped" id="listingList">
					<thead>
						
						<tr>
							<th width="" style="color: #2d566c;">
								<?php echo JText::_('NAME'); ?>
							</th>
							<th width="" style="color: #2d566c;">
								<?php echo JText::_('ADDRESS'); ?>
							</th>
							<th width="" style="color: #2d566c;">
								<?php echo JText::_('ACTION'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
							<?php 
								foreach($this->items as $i => $item)
								{
							?>
									<tr>
										<td>
											<?php echo $item->link_name  ?>
										</td>
										<td>
											<?php echo $item->address.', '.$item->city.', '.$item->state.', '.$item->postcode;  ?>
										</td>
										<td>
											<?php
												$db =  JFactory::getDbo();
												$query = "Select id From #__mt_price Where link_id ='$item->link_id'";
												$db->setQuery($query);
												$pricing = $db->loadObjectList();
												
												if($pricing)
												{?>
													<a href="<?php echo JRoute::_('index.php?option=com_mtlinked_listings&task=changelisting&id='.$item->link_id);?>"
														class="btn btn-success btn-small"type="button">
														<?php echo JText::_('Login'); ?>
													</a>
										<?php 	}
												else
												{?>	
													<a 
														href="<?php echo JRoute::_('#', false, 2); ?>"
														class="btn btn-success btn-small"
														onClick="$('#no').attr('href','<?php echo JRoute::_('index.php?option=com_mtlinked_listings&task=changelisting&id='.$item->link_id);?>');
																 $('#yes').attr('href','<?php echo JRoute::_('index.php?option=com_mtlinked_listings&task=copydata&id='.$item->link_id);?>');"
														id="<?php echo $item->link_id ?>"
														type="button" data-toggle="modal" data-target="#loginModal"
													>
													<?php echo JText::_('Login'); ?>
													</a>
										<?php 	}?>
										</td>
									</tr>
							<?php 
								}
							?>
					</tbody>
				</table>-->
	<?php   }?>	
	<br/>
	<div class="wbc_down_heding" style="margin-bottom: 0px;">
		10% off all subscription prices when you add Multiple Office Locations
	</div>
	<br/>
	<div class="buttons_containers_pro">
		<a href="#" data-toggle="modal" data-target="#linkModal"
		   class="buttons_main blue_bg long_button_adjust">
			<?php echo JText::_('Add New Location'); ?>
		</a>
	</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>


	<!--start gads updagrade modal-->
		<!-- Modal -->
		<div class="modal fade" id="linkModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="width: 38%;left: 70%;">
			<form action="<?php echo JRoute::_('index.php?option=com_mtlinked_listings&task=linked'); ?>" method="post" enctype="multipart/form-data" name="mtForm" id="mtForm" class="form-horizontal form-validate">
				<div class="modal-content" style="">
				  <div class="modal-header">
							<button type="button" class="close" onClick=" $('.modal-backdrop').remove();" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h3 style="line-height: 1.5;color: #41565b;font-weight: bold;">
								10% off when you add Multiple Office Locations
							</h3>
							<h4 style="font-size: 15px;line-height: 1.5;color: #41565b;font-weight: bold;">
								<p style="text-align: justify;">
									To activate this listing please select your preferred subscription package.<br>
									You will have the ability to copy all the data from your master listing across to other location listings.
									Prices below include the 10% discount. These discounted rates will apply to every location you add via your master listing.
								</p>
							</h4>
				  </div>
				  <div class="modal-body" style="display: inline-block;left: 5px;">

						

							<h4 class="s-h4">Select Subscription</h4>

							<div class="col-md-6 sp1">
								<img style="width: 57px;position: absolute;left: 73%;top: 3%;" src="http://6419f65a4520cf4dfc3a-3cce78da1b8c608e47a7455fa6a3be6b.r87.cf1.rackcdn.com/10discountv4.png">
								<div class="s-b">
									<input type="checkbox" 
											id="chk_standard" 
											name="jform[membership]" 
											data-monthly="13.50" 
											data-yearly="148.50"
											value="1" <?php if(!empty($data) and $data['membership']=='1'){echo 'checked';}?>>
									<label for="chk_standard">Standard</label>
									<!--<input type="checkbox"><label>Standard</label>-->
									<span class="sp1">$<span class="s-rate"> 13.50</span> <small>per month</small></span>
								</div>
							</div>
							<div class="col-md-6 sp2">
								<img style="width: 57px;position: absolute;left: 75%;top: 3%;" src="http://6419f65a4520cf4dfc3a-3cce78da1b8c608e47a7455fa6a3be6b.r87.cf1.rackcdn.com/10discountv4.png">
								<div class="s-b">
									<input type="checkbox" 
											id="chk_premium" 
											name="jform[membership]" 
											data-monthly="22.50" 
											data-yearly="296.51"
											value="2" <?php if(!empty($data) and $data['membership']=='2'){echo 'checked';}?>>
									<label for="chk_premium">Premium</label>
									<!--<input type="checkbox"><label>Premium</label>-->
									<span class="sp1">$ <span class="p-rate">22.50</span>  <small>per month</small></span>
								</div>
							</div>

							<div class="sub2">
								<h4 class="s-h4">Select Billing Plan</h4>
								<div class="sub2-i ">
									<input type="checkbox" id="chk_monthly" name="jform[billing_plan]" value="1" <?php if(!empty($data) and $data['billing_plan']=='1'){echo 'checked';}?>>
									<label for="chk_monthly"> Monthly   <b id="m-price"></b></label>
								</div>
								<div class="sub2-i mrg-0">
									<input type="checkbox" id="chk_yearly" name="jform[billing_plan]" value="2" <?php if(!empty($data) and $data['billing_plan']=='2'){echo 'checked';}?>>
									<label for="chk_yearly"> Yearly     	 <b id="y-price"></b></label>
								</div>
							</div>

							<span class="mname">GET 1 month free </span>
						
				  </div>
				  <div class="modal-footer" style="text-align:left !important;">
					
					<div id="foot" class="col-md sp2">
						<!--<div class="s-b">
							<input type="checkbox" id="accept" name="jform[accept]" value="1" class="hidden">
							<label for="accept" style="color:#000000 !important;">&nbsp;I wish to upgrade to a Premium listing and authorise my credit card &nbsp;to be charged&nbsp;<input disabled type="text" style="background-color:#f5f5f5;border:none;font-weight:bold;color:#265a88;font-size: 17px;position:relative;" id="upval" ></label>
						</div>
						<br/>-->
					
					<input id="linked" type="submit" value="Activate? Select subcription & billing" type="submit" class="buttons_mainl" disabled />
					</div>
					
				  </div>
				</div>
				<input type="hidden"  id="jform_address" name="jform[address]" value="" />
				<input type="hidden"  id="jform_sub_link" name="jform[sub_link]" value="" />
				<input type="hidden" name="option" value="com_mtlinked_listings" />
				<input type="hidden" name="task" value="linked" />
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
	
		<!--end gads updgrade modal-->

		

<!--gads login modal-->

	<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="width: 38%;position:fixed;left: 70%;">
	  
		<div class="modal-content" style="">
		  <div class="modal-header">
			<button type="button" class="close" onClick=" $('.modal-backdrop').remove();" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 style="font-size: 18px;color: #41565b;font-weight: bold;">
				NOTE
			</h4>
		  </div>
		  <div class="modal-body" style="display: inline-block;left: 5px;">
					
					<h4 style="font-size: 15px;line-height: 1.5;color: #41565b;font-weight: normal !important;">
						Your newly created location is linked to a master listing, would you like to have all the information from your linked listing copied across (includes pricing, about us, overview and FAQ)?
						<br/>Fields associated with the Address will not be copied across.
						<br/>You can continue to edit all fields as required.

					</h4>
		  </div>
		  <div class="modal-footer" style="text-align:center !important;">
			<div id="foot" class="col-md sp2">
			<a id="yes" href="#" class="buttons_mainl" >Yes Please</a>
			<a id="no" href=""  class="buttons_mainl" >No, I will update everything manually</a>
			</div>
		  </div>
		</div>
	  
	</div>

<!--end gads login modal-->

<script type="text/javascript">
(function($){
	$(document).ready(function () {
		$('.delete-button').click(deleteItem);
		
		$("#linkModal").modal("hide");
		$("#loginModal").modal("hide");
		
		
			//add gads standard checkbox
			$("#chk_standard").change(function () {
				if($('#chk_standard').is(':checked') && $('#chk_monthly').is(':checked') || $('#chk_yearly').is(':checked')){

					$("#linked").prop('disabled', false);
				}
				else
				{
					$("#linked").prop('disabled', true);
					$("#linked").val('Activate? Select subscription & billing');
				}



				if ($(this).prop('checked')) {

					$(".sub2-i").removeClass("chglclr2");
					$('#chk_premium').attr('checked', false); // Checks it
					$(".sub2-i").addClass("chglclr1");
					month_sub = $(this).data('monthly'); //$(".s-rate").html();
					year_sub =  $(this).data('yearly'); //(parseFloat(month_sub) * 11);
					$("#m-price").html("$" + month_sub);
					$("#y-price").html("$" + year_sub);
				} else {
					$("#m-price").empty();
					$("#y-price").empty();
					$(".sub2-i").removeClass("chglclr1");
				}

			});
			
			//add gads premium checkbox
			$("#chk_premium").change(function () {
			
				if($('#chk_premium').is(':checked') && $('#chk_monthly').is(':checked') || $('#chk_yearly').is(':checked')){
					
					$("#linked").prop('disabled', false);
				}
				else
				{
					$("#linked").prop('disabled', true);
				}
			


			if ($(this).prop('checked')) {
				$(".sub2-i").removeClass("chglclr1");
				$('#chk_standard').attr('checked', false); // Checks it
				$(".sub2-i").addClass("chglclr2");
				month_sub = $(this).data('monthly'); //$(".s-rate").html();
				year_sub =  $(this).data('yearly'); //(parseFloat(month_sub) * 11);
				$("#m-price").html("$" + month_sub);
				$("#y-price").html("$" + year_sub);

			} else {
				$(".sub2-i").removeClass("chglclr2");
				$("#m-price").empty();
				$("#y-price").empty();

			}

			});


			//add gads monthly checkbox
			$("#chk_monthly").change(function () {

				$('#chk_yearly').attr('checked', false); // Checks it

				
				if($('#chk_monthly').is(':checked') && $('#chk_standard').is(':checked') || $('#chk_premium').is(':checked')){
					
					$("#linked").prop('disabled', false);
				}
				else
				{	
					$("#linked").prop('disabled', true);
					$("#linked").val('Activate? Select subscription & billing');
				}
				
			});
			
			//add gads yearly
			$("#chk_yearly").change(function () {
				$('#chk_monthly').attr('checked', false); // Checks it

				
				if($('#chk_yearly').is(':checked') && $('#chk_premium').is(':checked') || $('#chk_standard').is(':checked')){

						$("#linked").prop('disabled', false);
					}
					else
					{
						$("#linked").prop('disabled', true);
						$("#linked").val('Activate? Select subscription & billing');
					}
				//end
			});
		
	});
})(jQuery);
	function deleteItem() {
		var item_id = jQuery(this).attr('data-item-id');
		<?php if($canDelete) : ?>
		if (confirm("<?php echo JText::_('COM_MTLINKED_LISTINGS_DELETE_MESSAGE'); ?>")) {
			window.location.href = '<?php echo JRoute::_('index.php?option=com_mtlinked_listings&task=listingform.remove&id=', false, 2) ?>' + item_id;
		}
		<?php endif; ?>
	}
	
jQuery('#linked').click(function()
{
	// var data_ready =jQuery('#jform_sub_link').val();
	// if(data_ready=='')
	// {
		// event.preventDefault();
	// }
	
});
</script>


