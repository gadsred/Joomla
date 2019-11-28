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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_staff');
$canEdit    = $user->authorise('core.edit', 'com_staff');
$canCheckin = $user->authorise('core.manage', 'com_staff');
$canChange  = $user->authorise('core.edit.state', 'com_staff');
$canDelete  = $user->authorise('core.delete', 'com_staff');

$session =& JFactory::getSession();
$session->clear('image_cloud');
$session->clear('name');
$session->clear('title');

//---gads redirect list with us---//
			$user_name=JFactory::getUser()->id;
			// $db =  JFactory::getDbo();
			// $query= "Select link_published,sub_id,invite,invite_code,invite_open From #__mt_links Where link_id='{$link_id}'";
			// $db->setQuery($query);
			// $link_status = $db->loadObject();
			if(!$user_name)
			{
				$app = JFactory::getApplication();
				$app->redirect('index.php?option=com_users&Itemid=116&lang=en&view=login');
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
<div class="wbc_h2container"><span>staff profile</span></div>
<form action="<?php echo JRoute::_('index.php?option=com_staff&view=staffs'); ?>" method="post" name="adminForm" id="adminForm">
	
	<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
	<div class="faq_table_container">
		<div class="staff_profile_row">
		<div class="staff_profile_box1 faq_topleft_border pi_heading">Name</div>
		<div class="staff_profile_box2 faq_topleft_border pi_heading">Position/Title</div>
		<div class="staff_profile_box3 faq_topleft_border pi_heading">Photo</div>
		<div class="staff_profile_box4 faq_topleft_border_last pi_heading">Actions</div>
		</div>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php $canEdit = $user->authorise('core.edit', 'com_staff'); ?>
			<?php if (!$canEdit && $user->authorise('core.edit.own', 'com_staff')): ?>
				<?php $canEdit = JFactory::getUser()->id == $item->created_by; ?>
			<?php endif; ?>
			<?php if (isset($this->items[0]->state)): ?>
				<?php $class = ($canEdit || $canChange) ? 'active' : 'disabled'; ?>
			<?php endif; ?>
			<?php $gray_bg = ''; if ($i % 2 == 1) $gray_bg = 'faq_gray_bg'; ?>
			<div class="staff_profile_row">
			<div class="staff_profile_box1 faq_topleft_border pi_normal border_top_mobile <?php echo $gray_bg; ?>"><?php echo $this->escape($item->name); ?></div>
			<div class="staff_profile_box2 faq_topleft_border faq_topleft_border pi_normal <?php echo $gray_bg; ?>"><?php echo $item->title; ?></div>
			<div class="staff_profile_box3 faq_topleft_border faq_topleft_border pi_normal <?php echo $gray_bg; ?>">
				<?php //if (isset($this->items[0]->id)): ?>
					<?php //echo (int) $item->id; ?>
				<?php //endif; ?>
				<img id="image" style="width:50%" src="<?php if(empty($item->image)){if(!empty($image_cloud)){echo $image_cloud;}else{echo '../images/faq-img.png';}}else{ echo str_replace("http://ed725d7aef4e3a5ec4d4-4cd0c9f2d4e37bb3c4bf33aaa42f24ff.r3.cf1.rackcdn.com","https://fb40f2b7bfb26e4d4d15-4cd0c9f2d4e37bb3c4bf33aaa42f24ff.ssl.cf1.rackcdn.com",$item->image);}?>" >
			</div>
			<div class="staff_profile_box4 faq_topleft_border_last pi_normal border_bottom_mobile <?php echo $gray_bg; ?>">
				<?php if ($canEdit || $canDelete): ?>
					<?php if ($canEdit): ?>
						<a href="<?php echo JRoute::_('index.php?option=com_staff&task=staffform.edit&Itemid=221&id=' . $item->id); ?>"><?php echo JText::_('COM_STAFF_EDIT_ITEM'); ?></a>
					<?php endif; ?>
					<?php if ($canDelete): ?>
						<a data-item-id="<?php echo $item->id; ?>" href="<?php echo JRoute::_('index.php?option=com_staff&task=staffform.remove&id=' . $item->id); ?>">DELETE</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			</div>
		<?php endforeach; ?>
		
		<div class="faq_bottom_heading_border"></div>
	</div>
	</br>
	</br>
	<?php if ($canCreate): ?>
		<div class="buttons_containers_pro">
		<a href="<?php echo JRoute::_('index.php?option=com_staff&task=staffform.edit&Itemid=221&id=0'); ?>" class="buttons_main blue_bg long_button_adjust" type="button">
			<?php echo JText::_('COM_STAFF_ADD_ITEM'); ?>
		</a>
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
		if (confirm("<?php echo JText::_('COM_STAFF_DELETE_MESSAGE'); ?>")) {
			window.location.href = '<?php echo JRoute::_('index.php?option=com_staff&task=staffform.remove&id=', false, 2) ?>' + item_id;
		}
	}
</script>


