<?php use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>
<!-- EXTENDED DATA-->
<?php
if (count($this->extended->getFieldsets()) > 0)
{
	// fieldset->name is set in the backend and is localized, so we need the backend language file here
	Factory::getLanguage()->load('com_joomleague', JPATH_ADMINISTRATOR);
	
	foreach ($this->extended->getFieldsets() as $fieldset)
	{
		$fields = $this->extended->getFieldset($fieldset->name);
		if (count($fields) > 0)
		{
			// Check if the extended data contains information 
			$hasData = false;
			foreach ($fields as $field)
			{
				// TODO: backendonly was a feature of JLGExtraParams, and is not yet available.
				//       (this functionality probably has to be added later)
				$value = $field->value;	// Remark: empty($field->value) does not work, using an extra local var does
				if (!empty($value))
				{
					$hasData = true;
					break;
				}
			}
			// And if so, display this information
			if ($hasData): ?>
				<h2><?php echo '&nbsp;' . Text::_($fieldset->name); ?></h2>
				<table>
					<tbody>
				<?php
				foreach ($fields as $field):
					$value = $field->value;
					if (!empty($value)): ?>
						<tr>
							<td class='label'><?php echo $field->label; ?></td>
							<td class='data'><?php echo $field->value;?></td>
						<tr>
					<?php
					endif;
				endforeach; ?>
					</tbody>
				</table>
				<?php
			endif;
		}
	}
}
?>
