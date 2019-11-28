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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_mtprice');
$canEdit    = $user->authorise('core.edit', 'com_mtprice');
$canCheckin = $user->authorise('core.manage', 'com_mtprice');
$canChange  = $user->authorise('core.edit.state', 'com_mtprice');
$canDelete  = $user->authorise('core.delete', 'com_mtprice');

$jinput = JFactory::getApplication()->input;
		
$state=$jinput->get('state', '', '');

$filter_au_state = $this->state->get("filter.au_state");

if($filter_au_state=='' or !empty($this->prices))
{
	$pstate='New State';
}
else{
	
	$pstate=$filter_au_state;
}

$doc = JFactory::getDocument();
$doc->addCustomTag("<meta name=\"robots\" content=\"noindex,nofollow\">");

?>

<form action="<?php echo JRoute::_('index.php?option=com_mtprice&view=prices'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="upgrade_subscription"></div>
	
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
	
	<div class="wbc_h2container"><span>Price for Buyers</span></div>
	<div class="top_subheading_center">(List of Pricing by State)</div>
	<div class="faq_table_container">
		<div class="price_buyer_boxes faq_topleft_border pi_heading">Applicable State</div>
		<div class="price_buyer_boxes faq_topleft_border pi_heading">Price</div>
		<div class="price_buyer_boxes faq_topleft_border_last pi_heading">Actions</div>
		<div class="clear"></div>
		<!--<div class="faq_bottom_heading_border  pi_heading"></div>-->
		
		<?php foreach ($this->prices as $i => $item) : ?>
			<?php $gray_bg = ''; if ($i % 2 == 1) $gray_bg = 'faq_gray_bg'; ?>
			<div class="price_buyer_boxes faq_topleft_border pi_normal border_top_mobile <?php echo $gray_bg; ?>"><?php echo $item->au_state; ?></div>
			<div class="price_buyer_boxes faq_topleft_border pi_normal border_top_mobile <?php echo $gray_bg; ?>"><?php echo $item->price; ?></div>
			<div class="price_buyer_boxes faq_topleft_border_last pi_normal border_bottom_mobile lesspadding_img <?php echo $gray_bg; ?>">
				<?php if($item->state !=2){?>
					<?php if ($canEdit || $canDelete): ?>
							<?php if ($canEdit): ?>
								<a href="<?php echo JRoute::_('index.php?option=com_mtprice&task=priceform.edit&Itemid=224&ptype=b&ktype=3&id=' . $item->id); ?>">EDIT</a>
							<?php endif; ?>
							<?php if ($canDelete): ?>
								<a data-item-id="<?php echo $item->id; ?>" href="<?php echo JRoute::_('index.php?option=com_mtprice&task=priceform.remove&id=' . $item->id); ?>">DELETE</a>
							<?php endif; ?>
					<?php endif; ?>
				<?php }?>
				
			</div>
			<div class="clear"></div>
		<?php endforeach; ?>
		<div class="faq_bottom_heading_border"></div>
	</div>
	<br/>
	<br/>
	<?php if ($canCreate): ?>
		<div class="buttons_containers_pro">
		<a href="<?php echo JRoute::_('index.php?option=com_mtprice&task=priceform.edit&Itemid=224&ptype=b&ktype=3&id=0&state='.$filter_au_state); ?>" class="buttons_main blue_bg long_button_adjust">
		<?php echo JText::sprintf('ADD_PRICES',$pstate); ?></a>
		 </div>
	<?php endif; ?>    

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {
		var item_id = jQuery(this).attr('data-item-id');
		if (confirm("<?php echo JText::_('COM_MTPRICE_DELETE_MESSAGE'); ?>")) {
			window.location.href = '<?php echo JRoute::_('index.php?option=com_mtprice&task=priceform.remove&id=') ?>'+item_id;
		}
		else{
			event.preventDefault();
		}
	}
	
	var $=jQuery.noConflict();
	$(window).load(function(){
		var state=$('#filter_au_state').val();
		var pricestate='<?php echo $state;?>';
		$(".js-stools-container-filters").attr('style','display:block;');
		
		if(state=="" && pricestate=='')
		{
				$('#kpoints').attr('style','display:none;');
				
		}else
		{
			$('#kpoints').attr('style','');
		}
	});
	
	// $('#filter_au_state').change(function(){

	
	// var state=$('#filter_au_state').val();
		 // $.ajax({
			  // type: "POST",
			  // dataType:'text',
			  // url: 'index.php?option=com_mtprice&task=priceform.edit&format=raw&state='+state,
			   // beforeSend: function() {
					// // setting a timeout
					// //$("#url_scategory").addClass('ajaxloader');		
					// },
			  // success:function(data){
					
			  // },
			  // complete: function() {
					
				// }
			// });
			
	// });
</script>


