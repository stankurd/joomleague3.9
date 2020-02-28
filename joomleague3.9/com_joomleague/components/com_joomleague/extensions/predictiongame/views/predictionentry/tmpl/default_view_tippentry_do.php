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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');


if ( $this->show_debug_info )
{
$visible = 'text';    
//echo '<br />config<pre>~' . print_r($this->config,true) . '~</pre><br />';
//echo '<br />allowedAdmin<pre>~' . print_r($this->allowedAdmin,true) . '~</pre><br />';
//echo '<br />predictionMember<pre>~' . print_r($this->predictionMember,true) . '~</pre><br />';
}
else
{
$visible = 'hidden';    
}

$this->config['show_tipp_tendence']=1;
if (((Factory::getUser()->id==0) || (!$this->model->checkPredictionMembership())) &&
	((!$this->allowedAdmin) || ($this->predictionMember->pmID==0)))
{
	if ($this->allowedAdmin)
	{
		echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_SELECT_EXISTING_MEMBER');

if ( $this->show_debug_info )
{/*		
echo '<br />allowedAdmin<pre>~' . print_r($this->allowedAdmin,true) . '~</pre><br />';
echo '<br />predictionMember<pre>~' . print_r($this->predictionMember,true) . '~</pre><br />';
echo '<br />getUser<pre>~' . print_r(Factory::getUser()->id,true) . '~</pre><br />';*/
}

	}
}
else
{
	foreach ($this->model->_predictionProjectS AS $predictionProject)
	{
		//$predictionProject->joker=0;
		$gotSettings = $predictionProjectSettings = $this->model->getPredictionProject($predictionProject->project_id);
		if ((($this->model->pjID==$predictionProject->project_id) && ($gotSettings)) || ($this->model->pjID==0))
		{
			$this->model->pjID = $predictionProject->project_id;
			$this->model->predictionProject = $predictionProject;
			$actualProjectCurrentRound = $this->model->getProjectSettings($predictionProject->project_id);
			if (!isset($this->model->roundID) || ($this->model->roundID < 1)){$this->model->roundID=$actualProjectCurrentRound;}
			if ($this->model->roundID < 1){$this->model->roundID=1;}

			if ($this->model->roundID > $this->model->getProjectRounds($predictionProject->project_id)){$this->model->roundID=$this->model->_projectRoundsCount;}

			$memberProjectJokersCount = $this->model->getMemberPredictionJokerCount($this->predictionMember->user_id,
																					$predictionProject->project_id);

      $match_ids = '';
      if ( $this->config['use_pred_select_matches'] )
      {
      //echo '<br />predictionmatchid<pre>~' . print_r($this->config['predictionmatchid'],true) . '~</pre><br />';
      $match_ids = $this->config['predictionmatchid'];
      }
      
			$roundResults = $this->model->getMatchesDataForPredictionEntry(	$this->model->predictionGameID,
																			$predictionProject->project_id,
																			$this->model->roundID,
																			$this->predictionMember->user_id,$match_ids);

			//$roundResults = null;
if ( $this->show_debug_info )
{		
echo '<br />predictionGameID<pre>~' . print_r($this->model->predictionGameID,true) . '~</pre><br />';
echo '<br />project_id<pre>~' . print_r($predictionProject->project_id,true) . '~</pre><br />';
echo '<br />roundID<pre>~' . print_r($this->model->roundID,true) . '~</pre><br />';
echo '<br />user_id<pre>~' . print_r($this->predictionMember->user_id,true) . '~</pre><br />';
echo '<br />roundResults<pre>~' . print_r($roundResults,true) . '~</pre><br />';
}			
			
			
			
			
//			if (($this->config['show_help']==0)||($this->config['show_help']==2)){echo $this->model->createHelptText($predictionProject->mode);}
			?>
			<a name='jl_top' id='jl_top'></a>
			<form name='resultsRoundSelector' method='post' onsubmit="alert(1)">
				<input type='hidden' name='option' value='com_joomleague' />
				
				<input type='hidden' name='task' value='predictionentry.selectprojectround' />
				<input type='hidden' name='prediction_id' value='<?php echo (int)$this->predictionGame->id; ?>' />
				<input type='hidden' name='p' value='<?php echo (int)$predictionProject->project_id; ?>' />
				<input type='hidden' name='r' value='<?php echo (int)$this->model->roundID; ?>' />
				<input type='hidden' name='memberID' value='<?php echo $this->predictionMember->pmID; ?>' />
				<input type='hidden' name='pjID' value='<?php echo (int)$this->model->pjID; ?>' />
				<?php echo HTMLHelper::_('form.token'); ?>


				<table class='blog' cellpadding='0' cellspacing='0'>
					<tr>
						<td class='sectiontableheader'><b><?php echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_SUBTITLE_01'); ?></b></td>
						<td class='sectiontableheader' style='text-align:right; ' width='20%' nowrap='nowrap' >
							<?php
							$rounds = JoomleagueHelper::getRoundsOptions($predictionProject->project_id);
//							$htmlRoundsOptions = HTMLHelper::_('select.genericlist',$rounds,'current_round','class="inputbox" size="1" onchange="document.forms[\'resultsRoundSelector\'].r.value=this.value;submit()"','value','text',$this->model->roundID);
							$htmlRoundsOptions = HTMLHelper::_('select.genericlist',$rounds,'r','class="inputbox" size="1" onchange="this.form.submit();"','value','text',$this->model->roundID);
							echo Text::sprintf(	'COM_JOOMLEAGUE_PRED_ENTRY_SUBTITLE_02',
													$htmlRoundsOptions,
													$this->model->createProjectSelector($this->model->_predictionProjectS,$predictionProject->project_id));
							?>
						</td>
					</tr>
				</table><br />
				<?php echo HTMLHelper::_( 'form.token' ); ?>
			</form>
			<?php $formName = 'predictionDoTipp'.$predictionProject->project_id; ?>
			<form	name='<?php echo $formName; ?>'
					id='<?php echo $formName; ?>'
					method='post' onsubmit='return chkFormular()' >

				<input type='hidden' name='task'			value='predictionentry.addtipp' />
				<input type='hidden' name='option'			value='com_joomleague' />
				
				<input type='hidden' name='prediction_id'	value='<?php echo (int)$this->model->predictionGameID; ?>' />
				<input type='hidden' name='user_id'			value='<?php echo $this->predictionMember->user_id; ?>' />
				<input type='hidden' name='memberID'		value='<?php echo $this->predictionMember->pmID; ?>' />
				<input type='hidden' name='r'		value='<?php echo $this->model->roundID; ?>' />
				<input type='hidden' name='pjID'			value='<?php echo (int)$this->model->pjID; ?>' />
				<input type='hidden' name='pids[]'			value='<?php echo $predictionProject->project_id; ?>' />
				<input type='hidden' name='ptippmode[<?php echo $predictionProject->project_id; ?>]' value='<?php echo $predictionProject->mode; ?>' />
				<input type='hidden' name='jokerCount'		value='<?php echo $memberProjectJokersCount; ?>' />
				<input type='hidden' name='maxJokerCount'	value='<?php echo $predictionProject->joker_limit; ?>' />
				<?php echo HTMLHelper::_('form.token'); ?>
                
<?php
if ( $this->show_debug_info )
{	
/*    
echo '<br />predictionDoTipp<br />';
echo '<br />prediction_id<pre>~' . print_r($this->model->predictionGameID,true) . '~</pre><br />';
echo '<br />user_id<pre>~' . print_r($this->predictionMember->user_id,true) . '~</pre><br />';
echo '<br />memberID<pre>~' . print_r($this->predictionMember->pmID,true) . '~</pre><br />';
*/
}
?>
                
				<script type='text/javascript'>
					function chkFormular()
					{
						var message = "";

						if ( parseInt(document.<?php echo $formName; ?>.jokerCount.value) > parseInt(document.<?php echo $formName; ?>.maxJokerCount.value) )
						{
							message+="<?php echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CHECK_JOKERS_COUNT'); ?>\n";
						}
						if (message==""){return true;}
						else {
						  alert(message);
						  return false;
						}
					}
				</script>
                
				<table width='100%' cellpadding='0' cellspacing='0'>
					<tr>
						<th class='sectiontableheader' style='text-align:center; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_DATE_TIME'); ?></th>
						<th class='sectiontableheader' style='text-align:center; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_DATE_TIME'); ?></th>					
						<th class='sectiontableheader' style='text-align:center; ' colspan="5" ><?php echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_MATCH'); ?></th>
						<th class='sectiontableheader' style='text-align:center; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_RESULT'); ?></th>
						<th class='sectiontableheader' style='text-align:center; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_YOURS'); ?></th>
						<?php
						if (($predictionProject->joker) && ($predictionProject->mode==0))
						{
							?><th class='sectiontableheader' style='text-align:center; '><?php
							if ($predictionProject->joker_limit > 0)
							{
								echo Text::sprintf('COM_JOOMLEAGUE_PRED_ENTRY_JOKER_COUNT',$memberProjectJokersCount,$predictionProject->joker_limit);
							}
							else
							{
								echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_JOKER');
							}
							?></th><?php
						}
						?>
						<th class='sectiontableheader' style='text-align:center; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_POINTS'); ?></th>
					</tr>
					<?php
					$k = 1;
					$disabled = '';
					$totalPoints=0;

					if (empty($this->model->_predictionMember->fav_team)){$this->model->_predictionMember->fav_team='0,0';}
					$sFavTeamsList=explode(';',$this->model->_predictionMember->fav_team);
					foreach ($sFavTeamsList AS $key => $value){$dFavTeamsList[]=explode(',',$value);}
					foreach ($dFavTeamsList AS $key => $value){$favTeamsList[$value[0]]=$value[1];}

					if (empty($this->model->_predictionMember->champ_tipp)){$this->model->_predictionMember->champ_tipp='0,0';}
					$sChampTeamsList=explode(';',$this->model->_predictionMember->champ_tipp);
					foreach ($sChampTeamsList AS $key => $value){$dChampTeamsList[]=explode(',',$value);}
					foreach ($dChampTeamsList AS $key => $value){$champTeamsList[$value[0]]=$value[1];}

					$showSaveButton=false;
					if (count($roundResults) > 0)
					{
					
          // schleife �ber die ergebnisse in der runde
					foreach ($roundResults AS $result)
					{
						$class = ($k==0) ? 'sectiontableentry1' : 'sectiontableentry2';

						$resultHome = (isset($result->team1_result)) ? $result->team1_result : '-';
						if (isset($result->team1_result_decision)){$resultHome=$result->team1_result_decision;}
						$resultAway = (isset($result->team2_result)) ? $result->team2_result : '-';
						if (isset($result->team2_result_decision)){$resultAway=$result->team2_result_decision;}
						
						$closingtime = $this->config['closing_time'] ;//3600=1 hour
						//$matchTimeDate = JoomleagueHelper::getMatchStartTimestamp($result->match_date,1,$predictionProjectSettings->timezone);
						//$thisTimeDate = JoomleagueHelper::getMatchEndTimestamp('',1,$predictionProjectSettings->timezone);
						$matchTimeDate = JoomleagueHelper::getTimestamp($result->match_date,1,$predictionProjectSettings->timezone);
						$thisTimeDate = JoomleagueHelper::getTimestamp(date("Y-m-d H:i:s"),1,$predictionProjectSettings->timezone);
						

            //   $this->config['show_help']
if ( $this->show_debug_info )
            {
 /*               
echo '<br />this->closingtime<pre>~' . print_r($closingtime,true) . '~</pre><br />';
echo '<br />this->matchTimeDate<pre>~' . print_r($matchTimeDate,true) . '~</pre><br />';
echo '<br />this->thisTimeDate<pre>~' . print_r($thisTimeDate,true) . '~</pre><br />';
            
            
echo '<br />this->allowedAdmin<pre>~' . print_r($this->allowedAdmin,true) . '~</pre><br />';
echo '<br />this->predictionMember->admintipp<pre>~' . print_r($this->predictionMember->admintipp,true) . '~</pre><br />';
echo '<br />this->use_tipp_admin<pre>~' . print_r($this->config['use_tipp_admin'],true) . '~</pre><br />';
  */        
            }
            
            $matchTimeDate = $matchTimeDate - $closingtime; 
						$tippAllowed =	( ( $thisTimeDate < $matchTimeDate ) &&
										 ($resultHome=='-') &&
										 ($resultAway=='-') ) || (($this->allowedAdmin)&&($this->predictionMember->admintipp));
						//$tippAllowed = true;
						if (!$tippAllowed){$disabled=' disabled="disabled" ';}else{$disabled=''; $showSaveButton=true;}
						
						if ( $this->show_debug_info )
            {
            /*echo '<br />this->matchTimeDate nach berechnung<pre>~' . print_r($matchTimeDate,true) . '~</pre><br />';
            echo '<br />this->thisTimeDate<pre>~' . print_r($thisTimeDate,true) . '~</pre><br />';
            echo '<br />resultHome<pre>~'.print_r($resultHome,true).'~</pre><br />';
						echo '<br />resultAway<pre>~'.print_r($resultAway,true).'~</pre><br />';
						echo '<br />tippAllowed<pre>~'.print_r($tippAllowed,true).'~</pre><br />';
						echo '<br />date<pre>~' . print_r( HTMLHelper::_('date',date('Y-m-d H:i:s', $thisTimeDate), "%Y-%m-%d %H:%M:%S"), true ) . '~</pre><br />';
						echo '<br />predictionProjectSettings<pre>~' . print_r($predictionProjectSettings->serveroffset, true ) . '~</pre><br />';
						*/}
						
            ?>
            <tr class='<?php echo $class; ?>'>
<!-- <td class="td_c"> -->
<?php
    /**
     * das datum des spiels
     */
    $jdate = Factory::getDate($result->match_date);
    $jdate->setTimezone(new DateTimeZone($predictionProjectSettings->timezone));
    //echo $jdate->format('d.m.Y H:i'); //outputs 01:00:00
    $pred_matchdatetime = $jdate->format('D  d.M. Y H:i')." </br>";
    $prev_pred_matchdatetime = '';
    if ($pred_matchdatetime != $prev_pred_matchdatetime){
        ?> <td  colspan='4' style="text-align: center;font-weight:bold; " ><?php echo $pred_matchdatetime;?></td><?php
            $prev_pred_matchdatetime = $pred_matchdatetime;
            }
    ?>
<!-- </td> -->
						<tr class='<?php echo $class; ?>'>
							<td class="td_c">
								<?php
                // das datum des spiels
 								//echo HTMLHelper::date($result->match_date,Text::_('COM_JOOMLEAGUE_GLOBAL_CALENDAR_DATE'));
 								//echo ' - ';
 				   				//echo HTMLHelper::date(date("Y-m-d H:i:s",$matchTimeDate),$this->config['time_format']); 
                                //echo $result->match_date;
                                echo HTMLHelper::date($result->match_date, 'd.m.Y H:i', false);
								?>
							</td>
								<?php
								$homeName = $this->model->getMatchTeam($result->projectteam1_id,$this->config['prediction_team_name']);
								$awayName = $this->model->getMatchTeam($result->projectteam2_id,$this->config['prediction_team_name']);
								
								?>
							<td nowrap='nowrap' class="td_r">
								<?php
								if 	((isset($favTeamsList[$predictionProject->project_id])) &&
									($favTeamsList[$predictionProject->project_id]==$result->projectteam1_id))
								{
								?>
								<span style='background-color:yellow; color:black; padding:2px; '><?php echo $homeName; ?></span>
								<?php
								}
								else
								{
									echo $homeName;
								}
								?>
							</td>
							<td nowrap='nowrap' class="td_c">
								<?php
                // clublogo oder vereinsflagge
								if ( $this->config['show_logo_small'] == 1 )
								{
									$logo_home = $this->model->getMatchTeamClubLogo($result->projectteam1_id);
									if	(($logo_home == '') || (!file_exists($logo_home)))
									{
										$logo_home = 'images/com_joomleague/database/placeholders/placeholder_small.gif';
									}
									$imgTitle = Text::sprintf('COM_JOOMLEAGUE_PRED_ENTRY_LOGO_OF', $homeName);
									echo HTMLHelper::image($logo_home,$imgTitle,array(' title' => $imgTitle));
									echo ' ';
								}
                if ( $this->config['show_logo_small'] == 2 )
								{
                $country_home = $this->model->getMatchTeamClubFlag($result->projectteam1_id);
                echo Countries::getCountryFlag($country_home);
                }
								?>
							</td>							
							<td nowrap='nowrap' class="td_c">
								<?php							
								echo '<b>' . $this->config['seperator'] . '</b> ';
								?>
							</td>
							<td nowrap='nowrap' class="td_c">
								<?php	
                // clublogo oder vereinsflagge
								if ( $this->config['show_logo_small'] == 1 )
								{
									$logo_away = $this->model->getMatchTeamClubLogo($result->projectteam2_id);
									if (($logo_away=='') || (!file_exists($logo_away)))
									{
										$logo_away = 'images/com_joomleague/database/placeholders/placeholder_small.gif';
									}
									$imgTitle = Text::sprintf('COM_JOOMLEAGUE_PRED_ENTRY_LOGO_OF', $awayName);
									echo ' ';
									echo HTMLHelper::image($logo_away,$imgTitle,array(' title' => $imgTitle));
								}
                if ( $this->config['show_logo_small'] == 2 )
								{
                $country_away = $this->model->getMatchTeamClubFlag($result->projectteam2_id);
                echo Countries::getCountryFlag($country_away);
                }
								?>
							</td>						
							<td nowrap='nowrap' class="td_l">
								<?php	
								if 	((isset($favTeamsList[$predictionProject->project_id])) &&
									($favTeamsList[$predictionProject->project_id] == $result->projectteam2_id))
								{
									?><span style='background-color:yellow; color:black; padding:2px; '><?php echo $awayName; ?></span>
								<?php
								}
								else
								{
									echo $awayName;
								}
								?>
							</td>
						<td class="td_c">
                            <td style="text-align: center;">
								<?php
								echo $resultHome . $this->config['seperator'] . $resultAway;
								?>
							</td>
							<td class="td_c">
								<?php
								echo '<input	type="hidden" name="cids[' . $predictionProject->project_id . '][]"
													value="' . $result->id . '" />';
								echo '<input	type="hidden"
													name="prids[' . $predictionProject->project_id . '][' . $result->id . ']"
													value="' . $result->prid . '" />';

                // welcher tippmodus
								if ($predictionProject->mode=='0')	// Tipp in normal mode
								{
									echo $this->createStandardTippSelect(	$result->tipp_home,$result->tipp_away,$result->tipp,
																					$predictionProject->project_id,$result->id,
																					$this->config['seperator'],$tippAllowed);
								}
								else	// Tipp in toto mode
								{
									echo $this->createTotoTippSelect(	$result->tipp_home,$result->tipp_away,$result->tipp,
																				$predictionProject->project_id,$result->id,$tippAllowed);
								}
								
								?>
							</td>
							<?php
							if (($predictionProject->joker) && ($predictionProject->mode==0))
							{
							?>
								<td class="td_c">
									<input	type='checkbox' id='cbj_<?php echo $result->id; ?>'
											name='jokers[<?php echo $predictionProject->project_id; ?>][<?php echo $result->id; ?>]'
											value='<?php echo $result->joker; ?>' <?php
											if (isset($result->joker) && ($result->joker=='1')){echo 'checked="checked" ';}
											echo $disabled; ?> onchange='	if(this.checked)
																			{
																				document.<?php echo $formName; ?>.jokerCount.value++;
																				if ( parseInt(document.<?php echo $formName; ?>.jokerCount.value) > parseInt(document.<?php echo $formName; ?>.maxJokerCount.value) )
																				{
																					alert("<?php echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_MAX_JOKER_WARNING'); ?>");
																					this.checked=false;
																					document.<?php echo $formName; ?>.jokerCount.value--;
																				}
				  															}
				  															else
																			{
																				document.<?php echo $formName; ?>.jokerCount.value--;
																			}' /><?php
									if ((!empty($disabled)) && (!empty($result->joker)))
									{
										?><input	type='hidden' id='cbj_<?php echo $result->id; ?>'
													name='jokers[<?php echo $predictionProject->project_id; ?>][<?php echo $result->id; ?>]'
													value='<?php echo $result->joker; ?>' /><?php
									}
								?>
								</td><?php
							}
							?>
							<td class="td_c"><?php
								if ((!$tippAllowed) || (($this->allowedAdmin)&&($this->predictionMember->admintipp)))
								{
									$points = $this->model->getMemberPredictionPointsForSelectedMatch($predictionProject,$result);
									$totalPoints = $totalPoints+$points;
									echo $points;
								}
								else
								{
									?>&nbsp;<?php
								}
								?>
							</td>
						</tr>
						<?php
						if ($this->config['show_tipp_tendence'])
						{
							?>
							<tr class="tipp_tendence">
								<td class="td_c"><?php
									//echo 'ChartImage';
									echo '&nbsp;'; ?></td>
								<td class="td_l">
									<?php
									$totalCount = $this->model->getTippCountTotal($predictionProject->project_id, $result->id);
									$homeCount = $this->model->getTippCountHome($predictionProject->project_id, $result->id);
									$awayCount = $this->model->getTippCountAway($predictionProject->project_id, $result->id);
									$drawCount = $this->model->getTippCountDraw($predictionProject->project_id, $result->id);
									if ($totalCount > 0)
									{
										$percentageH = round(( $homeCount * 100 / $totalCount ),2);
										$percentageD = round(( $drawCount * 100 / $totalCount ),2);
										$percentageA = round(( $awayCount * 100 / $totalCount ),2);
									}
									else
									{
										$percentageH = 0;
										$percentageD = 0;
										$percentageA = 0;
									}
									?>
									<span style='color:<?php echo $this->config['color_home_win']; ?>; ' >
									<?php echo Text::sprintf('COM_JOOMLEAGUE_PRED_ENTRY_PERCENT_HOME_WIN',$percentageH,$homeCount);?></span><br />
									<span style='color:<?php echo $this->config['color_draw']; ?>; '>
									<?php echo Text::sprintf('COM_JOOMLEAGUE_PRED_ENTRY_PERCENT_DRAW',$percentageD,$drawCount);?></span><br />
									<span style='color:<?php echo $this->config['color_guest_win']; ?>; '>
									<?php echo Text::sprintf('COM_JOOMLEAGUE_PRED_ENTRY_PERCENT_AWAY_WIN',$percentageA,$awayCount); ?></span>
								</td>
								<td colspan='8'>&nbsp;</td>
							</tr>
							<?php
						}
						else
						{
							$k = (1-$k);							
						}
						
					}
					}
					?>
					<tr>
						<?php
						if (count($roundResults)==0)
						{
							$colspan=($predictionProject->joker) ? '6' : '5';
							?>
							<td colspan='<?php echo $colspan; ?>' class="td_c" >
								<b><?php echo Text::_('COM_JOOMLEAGUE_PRED_ENTRY_NO_POSSIBLE_PREDICTIONS'); ?></b>
							</td>
							<?php
						}
						else
						{
							?>
							<td colspan='6'>&nbsp;</td>
							<td class="td_c">
								<?php
								if (count($roundResults) > 0)
								{
									if ($showSaveButton)
									{
									?><input type='submit' name='addtipp' value='<?php echo Text::_('JSAVE'); ?>' class='button' /><?php
									}
									else
									{
										?>&nbsp;<?php
									}
								}
								else
								{
									?>&nbsp;<?php
								}
								?>
							</td>
							<?php echo $colspan=($predictionProject->joker) ? '<td>&nbsp;</td>' : ''; ?>
							<td class="td_c"><?php echo Text::sprintf('COM_JOOMLEAGUE_PRED_ENTRY_TOTAL_POINTS_COUNT',$totalPoints); ?></td>
							<?php
						}
						?>
					</tr>
				</table>
               
				<?php echo HTMLHelper::_( 'form.token' ); ?>
			</form><br />
			<?php
//			if (($this->config['show_help']==1)||($this->config['show_help']==2))
//			{
//				echo $this->model->createHelptText($predictionProject->mode);
//			}
		}
	}
}
?><br />