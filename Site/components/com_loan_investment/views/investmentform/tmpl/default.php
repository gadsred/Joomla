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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'ar-body-wrapper select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_loan_investment', JPATH_SITE);
$doc = JFactory::getDocument();
$doc->addScript(JUri::base() . '/media/com_loan_investment/js/form.js');

$user    = JFactory::getUser();
$canEdit = Loan_investmentHelpersLoan_investment::canUserEdit($this->item, $user);


?>

<div class="investment-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(JText::_('COM_LOAN_INVESTMENT_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo JText::sprintf('COM_LOAN_INVESTMENT_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo JText::_('COM_LOAN_INVESTMENT_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-investment"
			  action="<?php echo JRoute::_('index.php?option=com_loan_investment&task=investment.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

				<?php echo $this->form->getInput('user_id'); ?>
	<?php echo $this->form->renderField('provider_name'); ?>

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
				<div class="fltlft" <?php if (!JFactory::getUser()->authorise('core.admin','loan_investment')): ?> style="display:none;" <?php endif; ?> >
                <?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
                <?php echo JHtml::_('sliders.panel', JText::_('ACL Configuration'), 'access-rules'); ?>
                <fieldset class="panelform">
                    <?php echo $this->form->getLabel('rules'); ?>
                    <?php echo $this->form->getInput('rules'); ?>
                </fieldset>
                <?php echo JHtml::_('sliders.end'); ?>
            </div>
				<?php if (!JFactory::getUser()->authorise('core.admin','loan_investment')): ?>
                <script type="text/javascript">
                    jQuery.noConflict();
                    jQuery('.tab-pane select').each(function(){
                       var option_selected = jQuery(this).find(':selected');
                       var input = document.createElement("input");
                       input.setAttribute("type", "hidden");
                       input.setAttribute("name", jQuery(this).attr('name'));
                       input.setAttribute("value", option_selected.val());
                       document.getElementById("form-investment").appendChild(input);
                    });
                </script>
             <?php endif; ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<?php echo JText::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn"
					   href="<?php echo JRoute::_('index.php?option=com_loan_investment&task=investmentform.cancel'); ?>"
					   title="<?php echo JText::_('JCANCEL'); ?>">
						<?php echo JText::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_loan_investment"/>
			<input type="hidden" name="task"
				   value="investmentform.save"/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
