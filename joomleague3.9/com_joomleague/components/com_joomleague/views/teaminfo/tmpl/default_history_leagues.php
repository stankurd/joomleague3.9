<?php use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access'); ?>

<table width="100%" class="contentpaneopen">
	<tr>
		<td class="contentheading"><?php echo '&nbsp;' . Text::_('COM_JOOMLEAGUE_TEAMINFO_HISTORY_PER_LEAGUE_SUMMARY'); ?>
		</td>
	</tr>
</table>

<fieldset>
<legend>
<strong>
<?php echo '&nbsp;' . Text::_('COM_JOOMLEAGUE_TEAMINFO_HISTORY_OVERVIEW_SUMMARY'); ?>
</strong>
</legend>


<table class='adminlist' width="100%">

<thead>
<tr class="sectiontableheader">
<th class="title" nowrap="nowrap" style="vertical-align:top;background:#BDBDBD; ">
<?PHP echo Text::_( 'COM_JOOMLEAGUE_TEAMINFO_LEAGUE' ); ?>
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top;background:#BDBDBD; ">
<?PHP echo Text::_( 'COM_JOOMLEAGUE_TEAMINFO_TOTAL_GAMES' ); ?>
</th>
<th class="title" nowrap="nowrap" style="vertical-align:top;background:#BDBDBD; ">
<?PHP echo Text::_( 'COM_JOOMLEAGUE_TEAMINFO_TOTAL_WDL' ); ?>
</th>

<th class="title" nowrap="nowrap" style="vertical-align:top;background:#BDBDBD; ">
<?PHP echo Text::_( 'COM_JOOMLEAGUE_TEAMINFO_TOTAL_GOALS' ); ?>
</th>

</tr>
</thead>

<?php
	$k=0;
	foreach ($this->leaguerankoverviewdetail as $league => $summary)
	{
	?>
	<tr
  class="<?php echo ($k == 0)? 'sectiontableentry1' : 'sectiontableentry2'; ?>">
	<td><?php echo $league; ?></td>
	<td><?php echo $summary->match; ?></td>
	
	<td><?php echo $summary->won; echo ' / '; ?>
	<?php echo $summary->draw; echo ' / '; ?>
	<?php echo $summary->loss; ?></td>
	
	<td><?php echo $summary->goalsfor; echo ' : '; ?>
	<?php echo $summary->goalsagain; ?></td>
	
	</tr>
	<?php
	$k = 1 - $k;
	}
	?>

</table>
</fieldset>


