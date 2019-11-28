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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_financial');
$canEdit    = $user->authorise('core.edit', 'com_financial');
$canCheckin = $user->authorise('core.manage', 'com_financial');
$canChange  = $user->authorise('core.edit.state', 'com_financial');
$canDelete  = $user->authorise('core.delete', 'com_financial');


// $jinput=JFactory::getApplication()->input;
// $invoice_id=$jinput->get('','','')

?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="frminvoice">
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
	<?PHP if (count($this->invoices) > 0): ?>
	<div class="wbc_h2container"><span>past invoices</span></div>
	<div class="faq_table_container">
		<div class="past_invoice_box1 faq_topleft_border pi_heading">Invoice Id </div>
		<div class="past_invoice_box2 faq_topleft_border  pi_heading">Date </div>
		<div class="past_invoice_box3 faq_topleft_border pi_heading">Charges</div>
		<!--<div class="past_invoice_box4 faq_topleft_border pi_heading">Payments</div>-->
		<div class="past_invoice_box5 faq_topleft_border pi_heading">Paid? </div>
		<div class="past_invoice_box6 faq_topleft_border_last pi_heading">Paid On</div>
		<div class="clear"></div>
		<!--<div class="faq_bottom_heading_border  pi_heading">&nbsp;</div>-->
			
		<?php 
	
			foreach ($this->invoices as $key => $invoice): ?>
			<?php $gray_bg = ''; if ($i % 2 == 1) $gray_bg = 'faq_gray_bg'; ?>
			<div class="past_invoice_box1 faq_left_border pi_normal faq_topleft_border <?php echo $gray_bg; ?>">
				<?php if (isset($item->checked_out) && $item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'pastinvoices.', $canCheckin); ?>
				<?php endif; ?>
				<!--<a href="index.php?option=com_financial&task=invoice&id=<?php echo $item->statement->id;?>"></a>-->
				<button class="btn btn-link" link="<?php echo $invoice->link; ?>" id="<?php echo $invoice->id;?>" onclick="event.preventDefault(); window.open(jQuery(this).attr('link'), 'Download Invoice');" >
				<!-- <button class="btn btn-link" id="<?php echo $invoice->id;?>" onclick="event.preventDefault();jQuery('#invoice_id').val(jQuery(this).attr('id'));jQuery('#frminvoice').submit();" > -->
				<?php echo $invoice->id; ?></button>
			</div>
			
			<div class="past_invoice_box2 faq_left_border pi_normal faq_topleft_border <?php echo $gray_bg; ?>">
				<?php echo $invoice->date; ?>
			</div>
			<div class="past_invoice_box3 faq_left_border pi_normal faq_topleft_border <?php echo $gray_bg; ?>">
				<?php echo  $invoice->total; ?>
			</div>
			<div class="past_invoice_box5 faq_left_border pi_normal faq_topleft_border <?php echo $gray_bg; ?> lesspadding_img_past_invoice"><img src="<?php echo JURI::base(); ?>/templates/property/img/tick.png" width="13" height="12" alt=""></div>
			<div class="past_invoice_box6 faq_left_border_last pi_normal  border_bottom_mobile faq_topleft_border <?php echo $gray_bg; ?>"><?php echo $invoice->paidAt; ?></div>
			<div class="clear"></div>
		<?php endforeach ?>
		

		<div class="faq_bottom_heading_border"></div>
	</div>

	<?PHP else: ?>
			<h2 class="text-center"><?PHP echo $this->message; ?></h2>
		<?PHP endif; ?>
	<br/>
	<br/>
	<?php if ($canCreate): ?>
		<div class="buttons_containers_pro">
		<!--<a href="<?php //echo JRoute::_('index.php?option=com_financial&task=pastinvoiceform.edit&id=0', false, 2); ?>" class="buttons_main blue_bg long_button_adjust">
		<?php //echo JText::_('ADD MORE'); ?></a>-->
		 </div>
	<?php endif; ?>  
	
	<input type="hidden" name="task" value="invoice" />
	<input type="hidden" name="option" value="com_financial" />
	<input type="hidden" id="invoice_id" name="id" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>


<?php

		// $authenticate= base64_encode("wm8JLGxlrKaKgcZyXDd:x");
		// $url = "https://propertyinvestorsonly.chargify.com/statements/".$iid.".json";
		// $headers = array("Authorization: Basic ".$authenticate);
		// $ch = curl_init();
		// curl_setopt($ch,CURLOPT_URL, $url);
		// curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1);
		// $result = curl_exec($ch);
		// curl_close($ch);
		// $invoice = json_decode($result);
?>

<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {
		var item_id = jQuery(this).attr('data-item-id');
		if (confirm("<?php echo JText::_('COM_FINANCIAL_DELETE_MESSAGE'); ?>")) {
			window.location.href = '<?php echo JRoute::_('index.php?option=com_financial&task=pastinvoiceform.remove&id=', false, 2) ?>' + item_id;
		}
	}
</script>


