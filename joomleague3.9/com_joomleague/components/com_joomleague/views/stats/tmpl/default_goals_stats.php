<?php use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>

<div id="jl_stats">

<div class="jl_substats">
<table cellspacing="0" border="0" width="100%">
<thead>
	<tr class="sectiontableheader">
		<th colspan="2"><?php echo Text::_('COM_JOOMLEAGUE_STATS_GOALS');?></th>
	</tr>
</thead>
<tbody>
	<tr  class="sectiontableentry1">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_GOALS_TOTAL');?>:</td>
		<td class="statvalue"><?php echo $this->totals->sumgoals;?></td>
	</tr>
	<tr  class="sectiontableentry2">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_GOALS_TOTAL_PER_MATCHDAY');?>:</td>
		<td class="statvalue"><?php
			if (	 $this->totals->playedmatches > 0 )
			{
				echo round ((($this->totals->sumgoals / $this->totals->playedmatches) *
				($this->totals->totalmatches / $this->totalrounds)),2);
			}
			else
			{
				echo '0';
			}
			?>
		</td>
	</tr>
	<tr  class="sectiontableentry1">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_GOALS_TOTAL_PER_MATCH');?>:</td>
		<td class="statvalue"><?php
			if (	 $this->totals->playedmatches>0 )
			{
				echo round (($this->totals->sumgoals / $this->totals->playedmatches),2);
			}
			else
			{
				echo '0';
			}
			?>
		</td>
	</tr>
	
	<?php	if ( $this->config['home_away_stats'] ): ?>
	<tr  class="sectiontableentry2">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_GOALS_HOME');?></td>
		<td class="statvalue"><?php echo $this->totals->homegoals;?></td>
	</tr>
	<tr  class="sectiontableentry1">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_GOALS_HOME_PER_MATCHDAY');?>:</td>
		<td class="statvalue"><?php
			if ( $this->totals->playedmatches>0 )
			{
				echo round((($this->totals->homegoals / $this->totals->playedmatches) *
				($this->totals->totalmatches / $this->totalrounds)),2);
			}
			else
			{
				echo '0';
			}
			?>
		</td>
	</tr>
	<tr  class="sectiontableentry2">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_GOALS_HOME_PER_MATCH');?>:</td>
		<td class="statvalue"><?php
			if ( $this->totals->playedmatches > 0 )
			{
				echo round(($this->totals->homegoals / $this->totals->playedmatches),2);
			}
			else
			{
				echo '0';
			}
			?>
		</td>
	</tr>
	<tr  class="sectiontableentry1">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_GOALS_AWAY');?></td>
		<td class="statvalue"><?php echo $this->totals->guestgoals;?></td>
	</tr>
	<tr  class="sectiontableentry2">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_GOALS_AWAY_PER_MATCHDAY');?>:</td>
		<td class="statvalue"><?php
			if ( $this->totals->playedmatches > 0 )
			{
				echo round((($this->totals->guestgoals / $this->totals->playedmatches) *
				($this->totals->totalmatches / $this->totalrounds)),2);
			}
			else
			{
				echo '0';
			}
			?>
		</td>
	</tr>
	<tr  class="sectiontableentry1">
		<td class="statlabel"><?php echo Text::_('COM_JOOMLEAGUE_STATS_GOALS_AWAY_PER_MATCH');?>:</td>
		<td class="statvalue"><?php
			if ( $this->totals->playedmatches > 0 )
			{
				echo round(($this->totals->guestgoals / $this->totals->playedmatches),2);
			}
			else
			{
				echo '0';
			}
			?>
		</td>
	</tr>
	<?php endif;	?>
</tbody>	
</table>
</div>

</div>
