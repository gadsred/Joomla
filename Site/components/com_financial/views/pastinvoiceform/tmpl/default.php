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
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/components/com_financial/assets/js/form.js');


?>
</style>
<script type="text/javascript">
    if (jQuery === 'undefined') {
        document.addEventListener("DOMContentLoaded", function(event) { 
            jQuery('#form-pastinvoice').submit(function(event) {
                
            });

            
        });
    } else {
        jQuery(document).ready(function() {
            jQuery('#form-pastinvoice').submit(function(event) {
                
            });

            
        });
    }
</script>
<div class="wbc_h2container">
<span>
<?php if (!empty($this->item->id)): ?>
	Edit Past Invoice
<?php else: ?>
	Add Past Invoice
<?php endif; ?>
</span>
</div>
<br/>
<form id="form-pastinvoice" action="<?php echo JRoute::_('index.php?option=com_financial&task=pastinvoice.save'); ?>" method="post" enctype="multipart/form-data">
	
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

	<?php if(empty($this->item->created_by)): ?>
		<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />
	<?php else: ?>
		<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />
	<?php endif; ?>

	<div class="profile_left_lable"><?php echo $this->form->getLabel('invoice_id'); ?></div>
	<div class="profile_left_txtbox"><?php echo $this->form->getInput('invoice_id'); ?></div>
	<div class="clear"></div>

	<div class="profile_left_lable"><?php echo $this->form->getLabel('date'); ?></div>
	<div class="profile_left_txtbox"><?php echo $this->form->getInput('date'); ?></div>
	<div class="clear"></div>

	<div class="profile_left_lable"><?php echo $this->form->getLabel('charges'); ?></div>
	<div class="profile_left_txtbox"><?php echo $this->form->getInput('charges'); ?></div>
	<div class="clear"></div>

	<div class="profile_left_lable"><?php echo $this->form->getLabel('payments'); ?></div>
	<div class="profile_left_txtbox"><?php echo $this->form->getInput('payments'); ?></div>
	<div class="clear"></div>

	<div class="profile_left_lable"><?php echo $this->form->getLabel('paid'); ?></div>
	<div class="profile_left_txtbox"><?php echo $this->form->getInput('paid'); ?></div>
	<div class="clear"></div>

	<div class="profile_left_lable"><?php echo $this->form->getLabel('paid_on'); ?></div>
	<div class="profile_left_txtbox"><?php echo $this->form->getInput('paid_on'); ?></div>
	<div class="clear"></div>

	<div class="buttons_containers_pro">
		<button type="submit" class="buttons_main blue_bg"><?php echo JText::_('JSUBMIT'); ?></button>
		<button type="button" onclick="history.back();" class="buttons_main black_bg"><?php echo JText::_('JCANCEL'); ?></button>
	</div>
	
	<input type="hidden" name="option" value="com_financial" />
	<input type="hidden" name="task" value="pastinvoiceform.save" />
	<?php echo JHtml::_('form.token'); ?>
</form>
