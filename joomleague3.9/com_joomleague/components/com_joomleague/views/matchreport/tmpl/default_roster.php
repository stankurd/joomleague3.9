<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<!-- START: game roster -->
<!-- Show Match players -->
<?php
if (!empty($this->matchplayerpositions))
{
?>

<h2><?php echo Text::_('COM_JOOMLEAGUE_MATCHREPORT_STARTING_LINE-UP'); ?></h2>		
	<table class="matchreport">
		<?php
		foreach ($this->matchplayerpositions as $pos)
		{
			$personCount=0;
			foreach ($this->matchplayers as $player)
			{
				if ($player->pposid==$pos->pposid)
				{
					$personCount++;
				}
			}

			if ($personCount > 0)
			{
				?>
				<tr><td colspan="2" class="positionid"><?php echo Text::_($pos->name); ?></td></tr>
				<tr>
					<!-- list of home-team -->
					<td class="list">
						<div style="text-align: right; ">
							<ul style="list-style-type: none;">
								<?php
								foreach ($this->matchplayers as $player)
								{
									if ($player->pposid==$pos->pposid && $player->ptid==$this->match->projectteam1_id)
									{
										?>
										<li <?php echo ($this->config['show_player_picture'] == 2 ? 'class="list_pictureonly_left"' : 'class="list"') ?>>
											<?php
											$player_link=JoomleagueHelperRoute::getPlayerRoute($this->project->slug,$player->team_slug,$player->person_slug);
											$prefix = $player->jerseynumber ? $player->jerseynumber."." : null;
											$match_player=JoomleagueHelper::formatName($prefix,$player->firstname,$player->nickname,$player->lastname, $this->config["name_format"]).' ';
											$isFavTeam = in_array( $player->team_id, explode(",",$this->project->fav_team));

                                            if ( ($this->config['show_player_profile_link'] == 1) || (($this->config['show_player_profile_link'] == 2) && ($isFavTeam)) )
                                            {
												if ($this->config['show_player_picture'] == 2) {
													echo '';
												} else {
													echo HTMLHelper::link($player_link,$match_player);
												}
                                            } else {
												if ($this->config['show_player_picture'] == 2) {
													echo '';
												} else {
													echo $match_player;
												}
                                            }

                                            if (($this->config['show_player_picture'] == 1) || ($this->config['show_player_picture'] == 2))
                                            {
                                                $imgTitle=($this->config['show_player_profile_link'] == 1) ? Text::sprintf('COM_JOOMLEAGUE_MATCHREPORT_PIC', $match_player) : $match_player;
                                                $picture=$player->picture;
                                                if ((empty($picture)) || ($picture == JoomleagueHelper::getDefaultPlaceholder("player") ) || !file_exists( $picture ) )
                                                {
                                                    $picture = $player->ppic;
                                                }
                                                if ( !file_exists( $picture ) )
                                                {
                                                    $picture = JoomleagueHelper::getDefaultPlaceholder("player");
                                                }
                                                if ( ($this->config['show_player_picture'] == 2) && ($this->config['show_player_profile_link'] == 1) ){
													echo HTMLHelper::link($player_link,JoomleagueHelper::getPictureThumb($picture,
																													$imgTitle,
																													$this->config['player_picture_width'],
																													$this->config['player_picture_height']));
												} else {
													echo JoomleagueHelper::getPictureThumb($picture,
																							$imgTitle,
																							$this->config['player_picture_width'],
																							$this->config['player_picture_height']);
													echo '&nbsp;';
                                                }
                                            }
											?>
										</li>
									<?php
									}
								}
								?>
							</ul>
						</div>
					</td>
					<!-- list of guest-team -->
					<td class="list">
						<div style="text-align: left;">
							<ul style="list-style-type: none;">
								<?php
								foreach ($this->matchplayers as $player)
								{
									if ($player->pposid==$pos->pposid && $player->ptid==$this->match->projectteam2_id)
									{
										?>
										<li <?php echo ($this->config['show_player_picture'] == 2 ? 'class="list_pictureonly_right"' : 'class="list"') ?>>
											<?php
											$player_link=JoomleagueHelperRoute::getPlayerRoute($this->project->slug,$player->team_slug,$player->person_slug);
											$prefix = $player->jerseynumber ? $player->jerseynumber."." : null;
											$match_player=' '.JoomleagueHelper::formatName($prefix,$player->firstname,$player->nickname,$player->lastname, $this->config["name_format"]);
											$isFavTeam = in_array( $player->team_id, explode(",",$this->project->fav_team));

                                            if (($this->config['show_player_picture'] == 1) || ($this->config['show_player_picture'] == 2))
                                            {
                                                $imgTitle=($this->config['show_player_profile_link'] == 1) ? Text::sprintf('COM_JOOMLEAGUE_MATCHREPORT_PIC', $match_player) : $match_player;
                                                $picture=$player->picture;
                                                if ((empty($picture)) || ($picture == JoomleagueHelper::getDefaultPlaceholder("player") ) || !file_exists( $picture ) )
                                                {
                                                    $picture = $player->ppic;
                                                }
                                                if ( !file_exists( $picture ) )
                                                {
                                                    $picture = JoomleagueHelper::getDefaultPlaceholder("player");
                                                }
                                                 if ( ($this->config['show_player_picture'] == 2) && ($this->config['show_player_profile_link'] == 1) ){
													echo HTMLHelper::link($player_link,JoomleagueHelper::getPictureThumb($picture,
																													$imgTitle,
																													$this->config['player_picture_width'],
																													$this->config['player_picture_height']));
												} else {
													echo JoomleagueHelper::getPictureThumb($picture,
																							$imgTitle,
																							$this->config['player_picture_width'],
																							$this->config['player_picture_height']);
													echo '&nbsp;';
                                                }
                                            }

											if ( ($this->config['show_player_profile_link'] == 1) || (($this->config['show_player_profile_link'] == 2) && ($isFavTeam)) )
                                            {
												if ($this->config['show_player_picture'] == 2) {
													echo '';
												} else {
													echo HTMLHelper::link($player_link,$match_player);
												}
                                            } else {
												if ($this->config['show_player_picture'] == 2) {
													echo '';
												} else {
													echo $match_player;
												}
                                            }
											?>
										</li>
										<?php
									}
								}
								?>
							</ul>
						</div>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</table>
	<?php
}
?>
<!-- END of Match players -->
<br />

<!-- END: game roster -->
