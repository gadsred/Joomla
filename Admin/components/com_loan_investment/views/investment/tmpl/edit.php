<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Loan_investment
 * @author     gadiel_Rojo <gadsred@gmail.com>
 * @copyright  2016 gadiel_Rojo
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_loan_investment/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'investment.cancel') {
			Joomla.submitform(task, document.getElementById('investment-form'));
		}
		else {
			
			if (task != 'investment.cancel' && document.formvalidator.isValid(document.id('investment-form'))) {
				
				Joomla.submitform(task, document.getElementById('investment-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_loan_investment&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="investment-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_LOAN_INVESTMENT_TITLE_INVESTMENT', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

				<?php echo $this->form->renderField('user_id'); ?>				<?php echo $this->form->renderField('provider_name'); ?>
				<?php echo $this->form->renderField('loan_display_name'); ?>
				<?php echo $this->form->renderField('maximum_lvr'); ?>
				<?php echo $this->form->renderField('loan_term'); ?>
				<?php echo $this->form->renderField('borrowing_amount_range'); ?>
				<?php echo $this->form->renderField('refinance'); ?>
				<?php echo $this->form->renderField('line_of_credit'); ?>
				<?php echo $this->form->renderField('self_managed_super'); ?>
				<?php echo $this->form->renderField('interest_rate_structure'); ?>
				<?php echo $this->form->renderField('interest_only'); ?>
				<?php echo $this->form->renderField('loan_allows_split_interest_rate'); ?>
				<?php echo $this->form->renderField('principal_interest'); ?>
				<?php echo $this->form->renderField('states_applicable'); ?>
				<?php echo $this->form->renderField('redraw_facility'); ?>
				<?php echo $this->form->renderField('redraw_fee'); ?>
				<?php echo $this->form->renderField('extra_repayments'); ?>
				<?php echo $this->form->renderField('weekly_repayments'); ?>
				<?php echo $this->form->renderField('fortnightly_repayments'); ?>
				<?php echo $this->form->renderField('monthly_repayments'); ?>
				<?php echo $this->form->renderField('date_created'); ?>
				<?php echo $this->form->renderField('date_modified'); ?>
				<?php echo $this->form->renderField('created_by'); ?>


					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if (JFactory::getUser()->authorise('core.admin','loan_investment')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true)); ?>
		<?php echo $this->form->getInput('rules'); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
