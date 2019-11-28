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

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_faq');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_faq')) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">
        <table class="table">
            <tr>
			<th><?php echo JText::_('COM_FAQ_FORM_LBL_FAQ_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FAQ_FORM_LBL_FAQ_LINK_ID'); ?></th>
			<td><?php echo $this->item->link_id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FAQ_FORM_LBL_FAQ_QUESTION'); ?></th>
			<td><?php echo $this->item->question; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FAQ_FORM_LBL_FAQ_ANSWER'); ?></th>
			<td><?php echo $this->item->answer; ?></td>
</tr>

        </table>
    </div>
    <?php if($canEdit): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_faq&task=faq.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_FAQ_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_faq')):?>
									<a class="btn" href="<?php echo JRoute::_('index.php?option=com_faq&task=faq.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_FAQ_DELETE_ITEM"); ?></a>
								<?php endif; ?>
    <?php
else:
    echo JText::_('COM_FAQ_ITEM_NOT_LOADED');
endif;
?>
