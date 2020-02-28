<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

//echo '<br /><pre>~' . print_r($this->model->pjID,true) . '~</pre><br />';

?>
<h2><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_PERS_DATA'); ?></h2>
<?php
if ($this->config['show_full_name']){$outputUserName=$this->predictionMember->name;}else{$outputUserName=$this->predictionMember->username;}
if ($this->model->pjID > 0){$showProjectID=$this->model->pjID;}else{$showProjectID=null;}

$memberPredictionPoints = $this->model->getPredictionMembersResultsList($showProjectID,1,null,$this->predictionMember->user_id);

$predictionsCount=0;
$totalPoints=0;
$totalTop=0;
$totalDiff=0;
$totalTend=0;
$totalJoker=0;
if (!empty($memberPredictionPoints))
{
	foreach ($memberPredictionPoints AS $memberPredictionPoint)
	{
		if ((!is_null($memberPredictionPoint->homeResult)) ||
			(!is_null($memberPredictionPoint->awayResult)) ||
			(!is_null($memberPredictionPoint->homeDecision)) ||
			(!is_null($memberPredictionPoint->awayDecision)))
		{
			$predictionsCount++;
			if (!is_null($memberPredictionPoint->prPoints)){$totalPoints=$totalPoints+$memberPredictionPoint->prPoints;}
			if (!is_null($memberPredictionPoint->prJoker)){$totalJoker=$totalJoker+$memberPredictionPoint->prJoker;}
			if (!is_null($memberPredictionPoint->prTop)){$totalTop=$totalTop+$memberPredictionPoint->prTop;}
			if (!is_null($memberPredictionPoint->prDiff)){$totalDiff=$totalDiff+$memberPredictionPoint->prDiff;}
			if (!is_null($memberPredictionPoint->prTend)){$totalTend=$totalTend+$memberPredictionPoint->prTend;}
		}
	}
}

?>
<table class='plgeneralinfo'>
	<tr>
		<td class='picture'>
    <?php
    // das userbild
    JoomleagueModelPredictionUsers::showMemberPicture($outputUserName,$this->predictionMember->user_id); 
    ?>
    </td>
		<td class='info'>
			<table class='plinfo'>
				<tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_NAME'); ?></td>
					<td class='data'>
						<?php
						$outputName = Text::sprintf('%1$s %2$s', $outputUserName, '');
						if ($this->predictionMember->user_id)
						{
							switch ($this->config['show_user_profile'])
							{
								case 1:	 // Link to Joomla Contact Page
											$link = JoomleagueHelperRoute::getContactRoute($this->predictionMember->user_id);
											$outputName = HTMLHelper::link($link, $outputName);
											break;

								case 2:	 // Link to CBE User Page with support for JoomLeague Tab
											$link = JoomleagueHelperRoute::getUserProfileRouteCBE(	$this->predictionMember->user_id,
																									$this->predictionGame->id,
																									$this->predictionMember->pmID);
											$outputName = HTMLHelper::link($link, $outputName);
											break;

								default:	break;
							}
						}
						echo $outputName;
						?>
					</td>
				</tr>
				<?php
					if ($this->config['show_register_date'])
					{
						//echo '<br /><pre>~' . print_r($this->predictionMember,true) . '~</pre><br />';
						?>
						<tr>
							<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_MEMBERSHIP'); ?></td>
							<td class='data'>
								<?php
								echo	($this->predictionMember->pmRegisterDate != '0000-00-00 00:00:00' ?
										HTMLHelper::date($this->predictionMember->pmRegisterDate,Text::_('COM_JOOMLEAGUE_GLOBAL_CALENDAR_DATE')) :
										Text::_('COM_JOOMLEAGUE_PRED_USERS_UNKNOWN'));
								?>
							</td>
						</tr>
						<?php
					}
				?>
				<?php
					if ($this->config['show_slogan'])
					{
						?>
						<tr>
							<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_SLOGAN'); ?></td>
							<td class='data'><?php
								//echo strip_tags($this->predictionMember->slogan);
								echo (!empty($this->predictionMember->slogan)) ? strip_tags($this->predictionMember->slogan) : Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_NO_SLOGAN')
								?></td>
						</tr>
						<?php
					}
				?>
				<?php
					if ($this->config['show_lasttip'])
					{
						?>
						<tr>
							<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_LAST_PRED'); ?></td>
							<td class='data'>
								<?php
								echo	( !empty($this->predictionMember->last_tipp) && ( $this->predictionMember->last_tipp != '0000-00-00 00:00:00') ) ?
										HTMLHelper::date($this->predictionMember->last_tipp,Text::_('COM_JOOMLEAGUE_GLOBAL_CALENDAR_DATE')) : Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_NEVER');
								?>
							</td>
						</tr>
						<?php
					}
				?>
				<?php
				if ($this->config['show_fav_team'])
				{
					$found=false;
					?>
					<tr>
						<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_FAVTEAMS'); ?></td>
						<td class='data'><?php
							foreach ($this->predictionProjectS AS $predictionProject)
							{
								if (($this->model->pjID==0) || ($this->model->pjID==$predictionProject->project_id))
								{
									if ($predictionProjectSettings = $this->model->getPredictionProject($predictionProject->project_id))
									{
										if ($res=$this->model->getPredictionProjectTeams($predictionProject->project_id))
										{
											foreach ($res AS $team)
											{
												foreach ($this->favTeams AS $key => $value)
												{
													if ($team->value == $value)
													{
														$found=true;
														?>
														<span class='hasTip' title="<?php
																echo Text::sprintf('COM_JOOMLEAGUE_PRED_USERS_FAVTEAM_IN_PROJECT',$predictionProjectSettings->name);
																?>"><?php
															echo $team->text . '<br />';
															?></span>
														<?php
														break;
													}
												}
											}
										}
									}
								}
							}
							if (!$found){echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_NO_FAVTEAM');}
							?></td>
					</tr>
					<?php
				}
				?>
				
        <tr>
					<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_CHAMPIONS'); /*Meistertipp*/ ?></td>
					<td class='data'><?php
						//echo '<br /><pre>~' . print_r($this->model->pjID,true) . '~</pre><br />';
						$found=false;

						if (!isset($this->predictionMember->champ_tipp))
						{
							$this->predictionMember->champ_tipp=Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_NO_CHAMP');
						}

						$champShown=false;
						$dummyOutputShown=false;
						foreach ($this->predictionProjectS AS $predictionProject)
						{
							if (($this->model->pjID==0) || ($this->model->pjID==$predictionProject->project_id))
							{
								if ($predictionProjectSettings = $this->model->getPredictionProject($predictionProject->project_id))
								{
									//$predictionProjectSettings->start_date='2010-08-08';
									//$time=time();
									$time=strtotime($predictionProjectSettings->start_date);
									//$date=date("Y-m-d",$time);
									//echo $date.'/';
									$time += 86400; // Ein Tag in Sekunden
									$showDate=date("Y-m-d",$time);
									//echo $showDate;
									$thisTimeDate = JoomleagueHelper::getTimestamp('',1,$predictionProjectSettings->timezone);
									//$competitionStartTimeDate = JoomleagueHelper::getTimestamp($predictionProjectSettings->start_date,1,$predictionProjectSettings->serveroffset);
									$competitionStartTimeDate = JoomleagueHelper::getTimestamp($showDate,1,$predictionProjectSettings->timezone);
									$showChamp = ($thisTimeDate > $competitionStartTimeDate);
									//if (($showChamp) || ($this->showediticon))
									
if ( $this->show_debug_info )
            {
echo '<br />predictionuser info -> time <pre>~' . print_r($time,true) . '~</pre><br />';
echo '<br />predictionuser info -> showDate <pre>~' . print_r($showDate,true) . '~</pre><br />';
echo '<br />predictionuser info -> thisTimeDate <pre>~' . print_r($thisTimeDate,true) . '~</pre><br />';
echo '<br />predictionuser info -> competitionStartTimeDate <pre>~' . print_r($competitionStartTimeDate,true) . '~</pre><br />';
echo '<br />predictionuser info -> showChamp <pre>~' . print_r($showChamp,true) . '~</pre><br />';
            }
            									
									if (($showChamp))
									{
										if ($res=$this->model->getPredictionProjectTeams($predictionProject->project_id))
										{
											foreach ($res AS $team)
											{
												foreach ($this->champTeams AS $key => $value)
												{
													if ($team->value == $value)
													{
														$found=true;
														$champShown=true;
														?>
														<span class='hasTip' title="<?php
																echo Text::sprintf('COM_JOOMLEAGUE_PRED_USERS_CHAMPION_IN_PROJECT',$predictionProjectSettings->name);
																?>"><?php
															echo $team->text . '<br />';
															?></span>
														<?php
														break;
													}
												}
											}
										}
									}
									else
									{
										if (!$dummyOutputShown)
										{
											echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_SHOW_AFTER_START') . '<br />';
										}
										$dummyOutputShown=true;
									}
								}
							}
						}
						if ((!$found)&&($champShown)){echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_NO_CHAMP');}
						?></td>
				</tr>
			</table>
		</td>
		<td class='info'>
			<table class='plinfo'>
				<?php
				if (count($this->model->_predictionProjectS) > 1)
				{
					?>
					<tr>
						<td class='data' style='text-align:center; ' colspan='2' >
							<form name='resultsRoundSelector' method='post' >
								<input type='hidden' name='prediction_id' value='<?php echo (int)$this->predictionGame->id; ?>' />
								<input type='hidden' name='project_id' value='<?php echo (int)$this->model->pjID; ?>' />
								<input type='hidden' name='uid' value='<?php echo (int)$this->predictionMember->pmID; ?>' />
								<input type='hidden' name='pjID' value='<?php echo (int)$this->model->pjID; ?>' />
								<input type='hidden' name='task' value='predictionusers.selectprojectround' />
								<input type='hidden' name='option' value='com_joomleague' />
								
								<?php echo HTMLHelper::_('form.token'); ?>

								<?php echo $this->model->createProjectSelector(	$this->model->_predictionProjectS,
																				$this->model->pjID,
																				1); ?>
							</form>
						</td>
					</tr>
					<?php
				}
				?>
				<?php
					/*
					if ($this->config['show_ranking'])
					{
						?>
						<tr>
							<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_RANK'); ?></td>
							<td class='data'><?php echo Text::sprintf('COM_JOOMLEAGUE_PRED_USERS_INFO_RANK_OUTPUT',$this->memberData->rankingAll); ?></td>
						</tr>
						<?php
					}
					*/
				?>
				<?php
					if ($this->config['show_totalpoints'])
					{
						?>
						<tr>
							<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_TOTAL_POINTS'); ?></td>
							<td class='data'><?php
								// Add Link to totalranking
								echo $totalPoints;
								?></td>
						</tr>
						<?php
					}
				?>
				<?php
					/* only works if game has just one project or we have a project selector
					if (($this->config['show_lastpoints']) && (count($this->predictionProjectS)==1))
					{
						?>
						<tr>
							<td class='label'><?php
								echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_LAST_ROUND');
								?></td>
							<td class='data'><?php
								//add link to last round ranking
								echo $this->memberData->lastTipp;
								?></td>
						</tr>
						<?php
					}
					*/
				?>
				<?php
					if ($this->config['show_counttipps'])
					{
						?>
						<tr>
							<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_PRED_COUNT'); ?></td>
							<td class='data'><?php echo $predictionsCount; ?></td>
						</tr>
						<?php
					}
				?>
				<?php
					if ($this->config['show_averagepoints'])
					{
						?>
						<tr>
							<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_AVERAGE_POINTS'); ?></td>
							<td class='data'><?php
								if ($predictionsCount > 0)
								{
									echo number_format(round($totalPoints/$predictionsCount,2),2);
								}
								else
								{
									echo number_format(0,2);
								}
								?></td>
						</tr>
						<?php
					}
				?>
				<?php
					if ($this->config['show_toptipps'])
					{
						?>
						<tr>
							<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_TOPS'); ?></td>
							<td class='data'><?php
								if ($predictionsCount > 0)
								{
									$percent = round($totalTop*100/$predictionsCount,2);
								}
								else
								{
									$percent = number_format(0,2);
								}
								echo Text::sprintf('%1$s (%2$s%%)',$totalTop,$percent);
								?></td>
						</tr>
						<?php
					}
				?>
				<?php
					if ($this->config['show_difftipps'])
					{
						?>
						<tr>
							<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_MARGINS'); ?></td>
							<td class='data'><?php
								if ($predictionsCount > 0)
								{
									$percent = round($totalDiff*100/$predictionsCount,2);
								}
								else
								{
									$percent = number_format(0,2);
								}
								echo Text::sprintf('%1$s (%2$s%%)',$totalDiff,$percent);
								?></td>
						</tr>
						<?php
					}
				?>
				<?php
					if ($this->config['show_tendtipps'])
					{
						?>
						<tr>
							<td class='label'><?php echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_TENDENCIES'); ?></td>
							<td class='data'><?php
								if ($predictionsCount > 0)
								{
									$percent = round($totalTend*100/$predictionsCount,2);
								}
								else
								{
									$percent = number_format(0,2);
								}
								echo Text::sprintf('%1$s (%2$s%%)',$totalTend,$percent);
								?></td>
						</tr>
						<?php
					}
				?>
				<?php
					/* only works if game has just one project or we have a project selector
					if (($this->config['show_form']) && (count($this->predictionProjectS)==1))
					{
						?>
						<tr>
							<td class='label'><?php
								echo Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_PRED_FORM');
								?></td>
							<td class='data'>
								<?php
								$imgTitle = Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_UP'); $picture = 'media/com_joomleague/jl_images/up.png';
								echo HTMLHelper::image($picture, $imgTitle, array(' title' => $imgTitle));
								$imgTitle = Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_SAME'); $picture = 'media/com_joomleague/jl_images/same.png';
								echo HTMLHelper::image($picture, $imgTitle, array(' title' => $imgTitle));
								$imgTitle = Text::_('COM_JOOMLEAGUE_PRED_USERS_INFO_DOWN'); $picture = 'media/com_joomleague/jl_images/down.png';
								echo HTMLHelper::image($picture, $imgTitle, array(' title' => $imgTitle));
								?>
							</td>
						</tr>
						<?php
					}
					*/
				?>
			</table>
		</td>
	</tr>
</table>