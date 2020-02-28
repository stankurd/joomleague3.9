<?php
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
?>
<script>
<!--
	function submitbutton(pressbutton)
	{
		if (pressbutton == 'saveassigned')
		{
			submitform(pressbutton);
		}
	}
//-->
</script>
<form action="<?php echo $this->request_url; ?>" method="post"
	id="adminForm" name="adminForm">
	<fieldset>
		<legend>
			<?php
			echo Text::sprintf('Assign persons to a team or the project [%1$s]','<i>' . $this->projectname . '</i>');
			?>
		</legend>
		<ul>
			<?php
			foreach($this->persons as $p)
			{
				?>
				<li><input type="hidden" name="pid" value="<?php echo $p->id ?>" />
					<?php echo JoomleagueHelper::formatName(null, $p->firstname, $p->nickname, $p->lastname, 0)?>
				</li>
				<?php
			}
			?>
		</ul>
		<p class="instructions">
			<?php
			echo Text::_('Assign selected persons as a player,staff or referee');
			?>
		</p>
		<?php
		echo $this->lists['type'];
		?>
		<p class="instructions">
			<?php
			echo Text::_('Select the team to assign the selected persons to if you want to assign players or staff.');
			echo '<br />';
			echo Text::_('Assigning Referees needs the following selection to be left untouched!');
			?>
		</p>
		<?php
		echo $this->lists['teams'];
		?>
	</fieldset>
	<input type="hidden" name="project_id"
		value="<?php echo Factory::getApplication()->input->get('project_id'); ?>" /> <input
		type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>