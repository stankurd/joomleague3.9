<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

if (count($this->historyPlayer) > 0)
{
	?>
	<!-- Player history START -->
	<h2><?php echo Text::_('COM_JOOMLEAGUE_PERSON_PLAYING_CAREER'); ?></h2>
	<table style="width:96%;align:center;border:0;cellpadding:0;cellspacing:0">
		<tr>
			<td>
				<table id="playerhistory" class="table">
					<tr class="sectiontableheader">
						<th class="td_l"><?php echo Text::_('COM_JOOMLEAGUE_PERSON_COMPETITION');
							?></th>
						<th class="td_l"><?php echo Text::_('COM_JOOMLEAGUE_PERSON_SEASON');
							?></th>
						<th class="td_l"><?php echo Text::_('COM_JOOMLEAGUE_PERSON_TEAM');
							?></th>
						<th class="td_l"><?php echo Text::_('COM_JOOMLEAGUE_PERSON_POSITION');
							?></th>
					</tr>
					<?php
					$k=0;
					foreach ($this->historyPlayer AS $station)
					{
						$link1=JoomleagueHelperRoute::getPlayerRoute($station->project_slug,$station->team_slug,$this->person->slug);
						$link2=JoomleagueHelperRoute::getTeamInfoRoute($station->project_slug,$station->team_slug);
						?>
						<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">
							<td class="td_l">
							<?php 
								echo HTMLHelper::link($link1,$station->project_name);
							?></td>
							<td class="td_l"><?php echo $station->season_name;
								?></td>
							<td class="td_l"><?php 
							if ($this->config['show_playercareer_teamlink'] == 1) {
								echo HTMLHelper::link($link2,$station->team_name);
							} else {
								echo $station->team_name;
							}
							?></td>
							<td class="td_l"><?php echo Text::_($station->position_name);
								?></td>
						</tr>
						<?php
						$k=(1-$k);
					}
					?>
				</table>
			</td>
		</tr>
	</table>

	<!-- Player history END -->
	<?php
}
?>
