<?php use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>

<!-- Details-->
<h2><?php echo Text::_('COM_JOOMLEAGUE_MATCHREPORT_DETAILS'); ?></h2>
<table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
	<!-- Prev Match-->
	<?php
	if ($this->match->old_match_id > 0)
	{
		?>
		<tr>
			<td colspan="3" >
				<span class="label"><?php echo Text::_( 'COM_JOOMLEAGUE_MATCHREPORT_OLD_MATCH' ); ?></span>
				<span><?php echo HTMLHelper :: link(JoomleagueHelperRoute::getMatchReportRoute( $this->project->id, 
																							$this->match->old_match_id ), 
												$this->oldmatchtext); ?></span>
			</td>
		</tr>
		<?php
	}
	?>
	<!-- Next Match-->
	<?php
	if ($this->match->new_match_id > 0)
	{
		?>
		<tr>
			<td colspan="3" >
				<span class="label"><?php echo Text::_( 'COM_JOOMLEAGUE_MATCHREPORT_NEW_MATCH' ); ?></span>
				<span><?php echo HTMLHelper :: link(JoomleagueHelperRoute::getNextMatchRoute( $this->project->id, 
																							$this->match->new_match_id ), 
												$this->newmatchtext); ?></span>
			</td>
		</tr>
		<?php
	}
	?>	
	<!-- Date -->
	<?php
    if ($this->config['show_match_date'] == 1)
    {
        if ($this->match->match_date)
        {
            ?>
            <tr>
                <td colspan="3" >
                    <span class="label"><?php echo Text::_( 'COM_JOOMLEAGUE_MATCHREPORT_DATE' ); ?></span>
                    <span><?php echo JoomleagueHelper::getMatchDate($this->match, Text::_('COM_JOOMLEAGUE_MATCHREPORT_GAMES_DATE')); ?></span>
                </td>
            </tr>
            <?php
        }
    }
	?>

	<!-- Time -->
	<?php
    if ($this->config['show_match_time'] == 1)
    {
        if ($this->match->match_date)
        {
            ?>
            <tr>
                <td colspan="3" >
                    <span class="label"><?php echo Text::_( 'COM_JOOMLEAGUE_MATCHREPORT_TIME' ); ?></span>
                    <span><?php echo JoomleagueHelperHtml::showMatchTime($this->match, $this->config, $this->overallconfig, $this->project); ?></span>
                </td>
            </tr>
            <?php
        }
	?>

        <!-- present -->
        <?php if ($this->match->time_present > 0): ?>
        <tr>
            <td colspan="3" >
                <span class="label"><?php echo Text::_( 'COM_JOOMLEAGUE_MATCHREPORT_PRESENT' ); ?></span>
                <span><?php echo $this->match->time_present; ?></span>
            </td>
        </tr>
        <?php endif;
    
    }
    ?>

	<!-- match number -->
	<?php
    if ($this->config['show_match_number'] == 1)
    {
        if ($this->match->match_number > 0): ?>
        <tr>
            <td colspan="3" >
                <span class="label"><?php echo Text::_( 'COM_JOOMLEAGUE_MATCHREPORT_NUMBER' ); ?></span>
                <span><?php echo $this->match->match_number; ?></span>
            </td>
        </tr>
        <tr>
            <td colspan="3" >
            &nbsp;
            </td>
        </tr>
        <?php endif;
    }
    ?>
	<!-- playground -->
	<?php
    if ($this->config['show_match_playground'] == 1)
    {
        if ($this->match->playground_id > 0): ?>
        <?php $playground_link = JoomleagueHelperRoute::getPlaygroundRoute( $this->project->id, $this->match->playground_id);?>
        <tr>
            <td colspan="3" >
                <span class="label"><?php echo Text::_( 'COM_JOOMLEAGUE_MATCHREPORT_PLAYGROUND' ); ?></span>
                <span><?php echo HTMLHelper::link ($playground_link, $this->playground->name); ?></span>
            </td>
        </tr>
        <?php endif;
    }
    ?>
	<!-- referees -->
	<?php
    if ($this->config['show_match_referees'] == 1)
    {    
        if ( $this->matchreferees )
        {
            ?>
            <tr>
                <td colspan="3" >
                    <span class="label"><?php echo Text::_( 'COM_JOOMLEAGUE_MATCHREPORT_REFEREE' ); ?></span>
                    <?php
                    $first = true;
                    foreach ( $this->matchreferees as $referee ) : 
                        $referee_link = JoomleagueHelperRoute::getRefereeRoute( $this->project->id, $referee->id );
                        if (!$first) {
                            echo ', ';
                        }
                        $link = HTMLHelper::link( $referee_link, JoomleagueHelper::formatName(null,$referee->firstname,$referee->nickname,$referee->lastname, $this->config["name_format"]));
                        if ($this->config["show_referee_position"] == 1) $link .= ' ('.$referee->position_name.')';
                        ?><span><?php echo $link; ?></span>
                        <?php
                        $first = false;
                    endforeach;	?>
                </td>
            </tr>
            <tr>
                <td colspan="3" >
                &nbsp;
                </td>
            </tr>            
            <?php
        }
    }
    ?>
	<!-- crowd -->
	<?php
    if ($this->config['show_match_crowd'] == 1)
    {
        if ( $this->match->crowd > 0 ): ?>
            <tr>
            <td>
                <span class="label"><?php echo Text::_( 'COM_JOOMLEAGUE_MATCHREPORT_ATTENDANCES' ); ?></span>
                <span><?php echo ': ' . number_format( $this->match->crowd, 0, ',' , '.' ); ?></span>
            </td>
            </tr>
        <?php endif;
    }
    ?>
</table>
<br/>
