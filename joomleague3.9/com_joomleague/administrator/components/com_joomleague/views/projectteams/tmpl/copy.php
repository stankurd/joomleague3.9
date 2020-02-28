<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;
HTMLHelper::_('formbehavior.chosen', 'select');
?>

<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm" name="adminForm">
	<fieldset>
		<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_COPY_DEST')?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_SELECT_PROJECT' ).':'; ?></div>
			<div class="controls"><?php echo $this->lists['projects']; ?></div>
		</div>
	</fieldset>

	<?php foreach ($this->ptids as $ptid): ?>
	<input type="hidden" name="ptids[]" value="<?php echo $ptid; ?>" />
	<?php endforeach; ?>
	<input type="hidden" name="task" value="" />
</form>