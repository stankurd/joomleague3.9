<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
$colspan	= 4;
$show_icons	= 0;
if ($this->config['show_picture_thumb'] == 1) $colspan++;
if ($this->config['show_nation'] == 1) $colspan++;
if ($this->config['show_icons'] == 1) $show_icons = 1;
?>

<?php foreach ($this->eventtypes AS $rows): ?>
<?php if ($this->multiple_events == 1) :?>
<h2><?php echo Text::_($rows->name); ?></h2>
<?php endif; ?>
<table class="eventsranking">
	<thead>
	<tr class="sectiontableheader">
		<th class="rank"><?php echo Text::_('COM_JOOMLEAGUE_EVENTSRANKING_RANK'); ?></th>

		<?php if ($this->config['show_picture_thumb'] == 1): ?>
		<th class="td_c">&nbsp;</th>
		<?php endif; ?>

		<th class="td_l"><?php echo Text::_('COM_JOOMLEAGUE_EVENTSRANKING_PLAYER_NAME'); ?></th>

		<?php if($this->config['show_nation'] == 1): ?>
		<th class="td_c">&nbsp;</th>
		<?php endif; ?>

		<th class="td_l"><?php echo Text::_('COM_JOOMLEAGUE_EVENTSRANKING_TEAM'); ?></th>


		<?php if ($show_icons == 1): ?>
		<th class="td_c" nowrap="nowrap">
			<?php
				$iconPath=$rows->icon;
				if (!strpos(' '.$iconPath,'/')){$iconPath='media/com_joomleague/events/'.$iconPath;}
				echo HTMLHelper::image($iconPath,Text::_($rows->name),array('title'=> Text::_($rows->name),'align'=> 'top','hspace'=> '2'));
			?>
			</th>
		<?php else: ?>
		<th class="td_c" nowrap="nowrap"><?php	echo Text::_($rows->name); ?></th>
		<?php endif; ?>
	</tr>
	</thead>
	<tbody>
	<?php
	if (count($this->eventranking[$rows->id]) > 0)
	{
		$k=0;
		$lastrank=0;
		foreach($this->eventranking[$rows->id] as $row)
		{
			if ($lastrank == $row->rank)
			{
				$rank='-';
			}
			else
			{
				$rank = $row->rank;
			}
			$lastrank = $row->rank;

			$class=$this->config['style_class2'];
			if ($k==0)
			{
				$class=$this->config['style_class1'];
			}
			$favStyle = '';
			$isFavTeam = in_array($row->tid,$this->favteams);
			$highlightFavTeam = $this->config['highlight_fav'] == 1 && $isFavTeam;
			if ($highlightFavTeam && $this->project->fav_team_highlight_type == 1)
			{
				$format = "%s";
				$favStyle = ' style="';
				$favStyle .= ($this->project->fav_team_text_bold != '') ? 'font-weight:bold;' : '';
				$favStyle .= (trim($this->project->fav_team_text_color) != '') ? 'color:'.trim($this->project->fav_team_text_color).';' : '';
				$favStyle .= (trim($this->project->fav_team_color) != '') ? 'background-color:' . trim($this->project->fav_team_color) . ';' : '';
				if ($favStyle != ' style="')
				{
				  $favStyle .= '"';
				}
				else {
				  $favStyle = '';
				}
			}

			?>
	<tr class="<?php echo $class; ?>"<?php echo $favStyle; ?>>
		<td class="rank"><?php echo $rank; ?></td>
		<?php $playerName = JoomleagueHelper::formatName(null, $row->fname, $row->nname, $row->lname, $this->config['name_format']); ?>
		<?php if ($this->config['show_picture_thumb']==1): ?>
		<td class="td_c playerpic">
		<?php
 		$picture = isset($row->teamplayerpic) ? $row->teamplayerpic : null;
 		if ((empty($picture)) || ($picture == JoomleagueHelper::getDefaultPlaceholder("player") ))
 		{
 			$picture = $row->picture;
 		}
 		if ( !file_exists( $picture ) )
 		{
 			$picture = JoomleagueHelper::getDefaultPlaceholder("player");
 		}
		echo JoomleagueHelper::getPictureThumb($picture, $playerName,
												$this->config['player_picture_width'],
												$this->config['player_picture_height']);
		?>
		</td>
		<?php endif; ?>

		<td class="td_l playername" width="30%">
		<?php			
			if ($this->config['link_to_player'] == 1)
			{
				$link=JoomleagueHelperRoute::getPlayerRoute($this->project->id, $row->tid, $row->pid);
				echo HTMLHelper::link($link, $playerName);
			}
			else
			{
				echo $playerName;
			}
		?>
		</td>

		<?php if ($this->config['show_nation']==1): ?>
		<td class="td_c playercountry"><?php echo Countries::getCountryFlag($row->country); ?></td>
		<?php endif; ?>

		<td class="td_l playerteam" width="30%">
			<?php
			$team=$this->teams[$row->tid];
			if (($this->config['link_to_team']==1) &&
				($this->project->id > 0) && ($row->tid > 0)) {
				$link = JoomleagueHelperRoute::getTeamInfoRoute($this->project->id, $row->tid);
			} else {
				$link = null;
			} 
			$teamName = JoomleagueHelper::formatTeamName($team,"t".$row->tid,$this->config, $highlightFavTeam, $link);
			echo $teamName;
			?>
		</td>

		<?php
		$value=($row->p > 9) ? $row->p : '&nbsp;'.$row->p;
		?>
		<td class="td_c playertotal"><?php echo $value; ?></td>
	</tr>
	<?php
		$k=(1-$k);
		}
	}
	?>
	</tbody>
</table>
<?php if ($this->multiple_events == 1):?>
<div class="fulltablelink">
<?php echo HTMLHelper::link($link=JoomleagueHelperRoute::getEventsRankingRoute($this->project->id, (isset($this->division->id) ? $this->division->id : 0), $this->teamid, $rows->id, (isset($this->matchid) ? $this->matchid : 0)), Text::_('COM_JOOMLEAGUE_EVENTSRANKING_MORE')); ?>
</div>
<?php else: ?>
<div class="pageslinks">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>

<p class="pagescounter">
	<?php echo $this->pagination->getPagesCounter(); ?>
</p>
<?php endif;?>

<?php endforeach; ?>
