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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_faq');
$canEdit    = $user->authorise('core.edit', 'com_faq');
$canCheckin = $user->authorise('core.manage', 'com_faq');
$canChange  = $user->authorise('core.edit.state', 'com_faq');
$canDelete  = $user->authorise('core.delete', 'com_faq');

//---gads redirect list with us---//
			$user_name=JFactory::getUser()->username;
			// $db =  JFactory::getDbo();
			// $query= "Select link_published,sub_id,invite,invite_code,invite_open From #__mt_links Where link_id='{$link_id}'";
			// $db->setQuery($query);
			// $link_status = $db->loadObject();
			if(!$user_name)
			{
				// $app = JFactory::getApplication();
				// $app->redirect('index.php?option=com_users&Itemid=116&lang=en&view=login');
			}
?>
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
<div class="wbc_h2container"><span>Frequently asked questions</span></div>
<form action="<?php echo JRoute::_('index.php?option=com_faq&view=faqs'); ?>" method="post" name="adminForm" id="adminForm">
	
	<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
	
	<?php foreach ($this->items as $i => $item) : ?>
	
		<div class="faq_question_bar"><?php echo $this->escape($item->question); ?></div>
		<div class="faq_nor_text"><?php echo $item->answer; ?></div>
		<?php if ($canCreate): ?>
		<div class="edit_button_faq"><a href="<?php echo JRoute::_('index.php?option=com_faq&task=faqform.edit&id=' . $item->id.'&Itemid=220'); ?>">EDIT</div>
		<?php endif; ?>
		<?php if ($canCreate): ?>
			<div class="delete_button_faq" data-item-id="<?php echo $item->id; ?>" ><a id="delete" href="#">DELETE</a></div>
		<?php endif; ?>
		<div class="clear"></div>
	<?php endforeach; ?>
	<?php if ($canCreate): ?>
		<div class="buttons_containers_pro">
          <button type="button" class="buttons_main blue_bg" onclick="window.location.href = '<?php echo JRoute::_("index.php?option=com_faq&task=faqform.edit&id=0&Itemid=220"); ?>';">Add New FAQ</button>
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
		<?php
			if($user->name)
			{?>
				jQuery('.delete_button_faq').click(deleteItem);
	<?php 	}?>
		// jQuery('#add_new_faq').click(
			// window.location.href = '<?php echo JRoute::_("index.php?option=com_faq&task=faqform.edit&id=0", false, 2); ?>';
		// );
	
	});

	function deleteItem() {
		var item_id = jQuery(this).attr('data-item-id');
		if (confirm("<?php echo JText::_('COM_FAQ_DELETE_MESSAGE'); ?> ")) {
			window.location.href = '<?php echo 'index.php?option=com_faq&task=faqform.remove&id=' ?>' + item_id;
		}
	}
</script>


