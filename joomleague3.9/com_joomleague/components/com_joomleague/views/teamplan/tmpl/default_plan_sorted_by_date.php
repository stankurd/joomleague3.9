<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;
if (!isset($this->config['show_matchreport_column']))
{
    $this->config['show_matchreport_column'] = 0;
}
?>
<a name='jl_top' id='jl_top'></a>
<?php
$app = Factory::getApplication();
$input = $app->input;
if (!empty($this->matches))
{
    $teamid = $input->getInt('tid');
    // sort matches by dates considering the order of matchdays setting
    if ($this->config['plan_order'] == 'ASC')
    {
        usort($this->matches, function($a, $b) {
            if (empty($a->match_date)) {
                $a->match_date = new Date('9999-12-31 0:00:00', 'UTC');
            }
            if (empty($b->match_date)) {
                $b->match_date = new Date('9999-12-31 0:00:00', 'UTC');
            }
            return strnatcasecmp($a->match_date->format('Y-m-d H:i:s'), $b->match_date->format('Y-m-d H:i:s'));
        });
    }
    else
    {
        usort($this->matches, function($a, $b) {
            if (empty($a->match_date)) {
                $a->match_date = new Date('9999-12-31 0:00:00', 'UTC');
            }
            if (empty($b->match_date)) {
                $b->match_date = new Date('9999-12-31 0:00:00', 'UTC');
            }
                return strnatcasecmp($b->match_date->format('Y-m-d H:i:s'), $a->match_date->format('Y-m-d H:i:s'));
        });
    }

    $counter = 1;
    $round_date = '';
    $k = 0;
    $MatchDateLine = '';
    $MatchDay = '';
    foreach ($this->matches as $match)
    {
        ?>
        <?php if ($this->config['show_matchday'] && $MatchDay != $match->name): ?>
        <h3><?php echo $match->name;?></h3>
        <?php endif; ?>

        <table class='fixtures'>
        <?php if ($this->config['show_date'] &&
                  $MatchDateLine != JoomleagueHelper::getMatchDate($match, Text::_('COM_JOOMLEAGUE_CLUBPLAN_MATCHDATE'))): ?>
            <tr class='sectiontableheader'>
                <th class='td_l' colspan='16'>
                    <?php
                    if (JoomleagueHelper::getMatchDate($match, 'Y-m-d') == '9999-12-31') {
                        echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_NOT_YET_TERMINATED');
                    }
                    else
                    {
                        echo JoomleagueHelper::getMatchDate($match, Text::_('COM_JOOMLEAGUE_CLUBPLAN_MATCHDATE'));
                    }
                    ?>
                </th>
            </tr>
        <?php endif; ?>
        <?php
        $hometeam = $this->teams[$match->projectteam1_id];
        $home_projectteam_id = $hometeam->projectteamid;

        $guestteam = $this->teams[$match->projectteam2_id];
        $guest_projectteam_id = $guestteam->projectteamid;

        $class= ($k == 0) ? $this->config['style_class1'] : $this->config['style_class2'];
        $highlight = $match->team1==$this->favteams ? 'highlight' : $class;
        $home = $hometeam->name;
        $away = $guestteam->name;

        $homeclub = $hometeam->club_id;
        $awayclub = $guestteam->club_id;

        $favStyle = '';
        if ($this->config['highlight_fav'] == 1 && !$teamid)
        {
            $isFavTeam = in_array($hometeam->id,$this->favteams) || in_array($guestteam->id, $this->favteams);
            if ($isFavTeam && $this->project->fav_team_highlight_type == 1)
            {
                if(trim($this->project->fav_team_color) != '')
                {
                    $color = trim($this->project->fav_team_color);
                }
                $format = '%s';
                $favStyle = ' style="';
                $favStyle .= ($this->project->fav_team_text_bold != '') ? 'font-weight:bold;' : '';
                $favStyle .= (trim($this->project->fav_team_text_color) != '') ? 'color:'.trim($this->project->fav_team_text_color).';' : '';
                $favStyle .= ($color != '') ? 'background-color:' . $color . ';' : '';
                if ($favStyle != ' style="')
                {
                  $favStyle .= '"';
                }
                else {
                  $favStyle = '';
                }
            }
        }
        ?>
            <tr class='<?php echo $highlight; ?>'<?php echo $favStyle; ?>>
        <?php 
        if ($this->config['show_events'])
        {
            ?>
                <td width='5' class='ko'>
            <?php
            $events = $this->model->getMatchEvents($match->id);
            $subs   = $this->model->getMatchSubstitutions($match->id);

            if ($this->config['use_tabs_events'])
            {
                $hasEvents = (count($events) + count($subs) > 0);
            }
            else
            {
                //no subs are shown when not using tabs for displaying events so don't check for that
                $hasEvents = count($events) > 0;
            }

            if ($hasEvents)
            {
                $link = 'javascript:void(0);';
                $img = HTMLHelper::image('media/com_joomleague/jl_images/events.png', 'events.png');
                $params = array('title'   => Text::_('COM_JOOMLEAGUE_TEAMPLAN_EVENTS'),
                                'onclick' => 'switchMenu(\'info'.$match->id.'\');return false;');
                echo HTMLHelper::link($link,$img,$params);
            }
            ?>
                </td>
        <?php
        }
        else
        {
            $hasEvents = false;
        }
        // end events
        ?>

        <?php if ($this->project->project_type == 'DIVISIONS_LEAGUE' && $this->config['show_division']): ?>
                <td><?php echo JoomleagueHelperHtml::showDivisonRemark($this->project->id, $hometeam, $guestteam, $this->config); ?></td>
        <?php endif; ?>
        
        <?php if (($this->config['show_playground'] || $this->config['show_playground_alert'])): ?>
                <td><?php JoomleagueHelperHtml::showMatchPlayground($this->project->id, $this->teams, $match, $this->config); ?></td>
        <?php endif; ?>
        
        <?php if ($this->config['show_time']): ?>
                <td width='10%'>
                <?php
                if (JoomleagueHelper::getMatchDate($match, 'Y-m-d') == '9999-12-31') {
                    $timeSuffix = Text::_('COM_JOOMLEAGUE_GLOBAL_CLOCK');
                    if ($timeSuffix=='COM_JOOMLEAGUE_GLOBAL_CLOCK') {
                        $timeSuffix='%1$s&nbsp;h';
                    }
                    $matchTime='--&nbsp;:&nbsp;--';
                    if ($this->config['show_time_suffix'])
                    {
                        echo sprintf($timeSuffix,$matchTime);
                    }
                    else
                    {
                        echo $matchTime;
                    }
                }
                else
                {
                    echo JoomleagueHelperHtml::showMatchTime($match, $this->config, $this->overallconfig, $this->project);
                }
                ?>
                </td>
        <?php endif; ?>

        <?php if ($this->config['show_time_present']): ?>
                <td width='10%'><?php echo empty($match->time_present) ? '-' : $match->time_present; ?></td>
        <?php endif; ?>

        <?php
        // Define some variables which will be used
        $teamA  = '';
        $teamB  = '';
        $score  = '';

        // Check if the home and guest team should be switched arround
        if ($this->config['switch_home_guest'])
        {
            $class1 = 'left';
            $class2 = 'right';
        }
        else
        {
            $class1 = 'right';
            $class2 = 'left';
        }
        if ($this->config['show_teamplan_link'])
        {
            $homelink = JoomleagueHelperRoute::getTeamPlanRoute($this->project->slug, $hometeam->team_slug);
            $awaylink = JoomleagueHelperRoute::getTeamPlanRoute($this->project->slug, $guestteam->team_slug);
        } else {
            $homelink = null;
            $awaylink = null;
        }
        $isFavTeam = in_array($hometeam->id,$this->favteams);
        $home = JoomleagueHelper::formatTeamName($hometeam, 'g' . $match->id . 't' . $hometeam->id,
            $this->config, $isFavTeam, $homelink);
        $teamA .= '<td class="'.$class1.'">'.$home.'</td>';

        // Check if the user wants to show the club logo or country flag
        switch ($this->config['show_logo_small'])
        {
            case 1 :
                $teamA .= '<td class="'.$class1.'"> ' . JoomleagueModelProject::getClubIconHtml($hometeam, 1) . '</td>';
                $teamB .= '<td class="'.$class2.'"> ' . JoomleagueModelProject::getClubIconHtml($guestteam,1) . '</td>';
                break;

            case 2 :
                $teamA .= '<td class="'.$class1.'">' . Countries::getCountryFlag($hometeam->country) . '</td>';
                $teamB .= '<td class="'.$class2.'">' . Countries::getCountryFlag($guestteam->country) . '</td>';
                break;

            case 3:
                $teamA .= '<td class="'.$class1.'">';
                $teamA .= JoomleagueHelper::getPictureThumb($hometeam->picture, $hometeam->name,
                                    $this->config['team_picture_width'], $this->config['team_picture_height'], 1);
                $teamA .= '</td>';

                $teamB .= '<td class="'.$class2.'">';
                $teamB .= JoomleagueHelper::getPictureThumb($guestteam->picture, $guestteam->name,
                                    $this->config['team_picture_width'], $this->config['team_picture_height'], 1);
                $teamB .= '</td>';
                break;
        }

        $separator ='<td width="10">' . $this->config['seperator'] . '</td>';
        $isFavTeam = in_array($guestteam->id, $this->favteams);
        $away = JoomleagueHelper::formatTeamName($guestteam, 'g' . $match->id . 't' . $guestteam->id,
            $this->config, $isFavTeam, $awaylink);
        $teamB .= '<td class="'.$class2.'">'.$away.'</td>';

        if (!$match->cancel)
        {
            // In case show_part_results is true, then first check if the part results are available;
            // 'No part results available' occurs when teamX_result_split ONLY consists of zero or more ';'
            // (zero for projects with a single playing period, one or more for projects with two or more playing periods)
            $team1_result_split_present = preg_match('/^;*$|NULL/', $match->team1_result_split) == 0;
            $team2_result_split_present = preg_match('/^;*$|NULL/', $match->team2_result_split) == 0;

            if ($this->config['switch_home_guest'])
            {
                if (isset($match->team1_result) && isset($match->team2_result))
                {
                    $result = '<strong>' . $match->team2_result . '&nbsp;' . $this->config['seperator'] .
                        '&nbsp;' . $match->team1_result . '</strong>';
                }
                else
                {
                    $result = '_&nbsp;' . $this->config['seperator'] . '&nbsp;_';
                }

                $part_results_left = explode(';', $match->team2_result_split);
                $part_results_right = explode(';', $match->team1_result_split);

                $leftResultOT   = $match->team2_result_ot;
                $rightResultOT  = $match->team1_result_ot;
                $leftResultSO   = $match->team2_result_so;
                $rightResultSO  = $match->team1_result_so;
                $leftResultDEC  = $match->team2_result_decision;
                $rightResultDEC = $match->team1_result_decision;
            }
            else
            {
                if (isset($match->team1_result) && isset($match->team2_result))
                {
                    $result = '<strong>' . $match->team1_result . '&nbsp;' . $this->config['seperator'] .
                        '&nbsp;' . $match->team2_result . '</strong>';
                }
                else
                {
                    $result = '_&nbsp;' . $this->config['seperator'] . '&nbsp;_';
                }

                $part_results_left = explode(';', $match->team1_result_split);
                $part_results_right = explode(';', $match->team2_result_split);

                $rightResultOT  = $match->team2_result_ot;
                $leftResultOT   = $match->team1_result_ot;
                $rightResultSO  = $match->team2_result_so;
                $leftResultSO   = $match->team1_result_so;
                $rightResultDEC = $match->team2_result_decision;
                $leftResultDEC  = $match->team1_result_decision;
            }

            $SOTresult = '';
            $SOTtolltip = '';

            switch ($match->match_result_type)
            {
                case 2 :
                    $result .= $this->config['result_style'] == 1 ? '<br />' : ' ';
                    $result .= '('.Text::_('COM_JOOMLEAGUE_RESULTS_SHOOTOUT') . ')';

                    if (isset($leftResultOT))
                    {
                        $OTresultS = $leftResultOT . '&nbsp;' . $this->config['seperator'] . '&nbsp;' . $rightResultOT;
                        $SOTresult .= '<br /><span class="hasTip" title="' . Text::_('COM_JOOMLEAGUE_RESULTS_OVERTIME2') .
                            '::' . $OTresultS . '" >' . $OTresultS . '</span>';
                        $SOTtolltip = ' | ' . $OTresultS;
                    }
                    if (isset($leftResultSO))
                    {
                        $SOresultS = $leftResultSO . '&nbsp;' . $this->config['seperator'] . '&nbsp;' . $rightResultSO;
                        $SOTresult .= '<br /><span class="hasTip" title="' . Text::_('COM_JOOMLEAGUE_RESULTS_SHOOTOUT2') .
                            '::' . $SOresultS . '" >' . $SOresultS . '</span>';
                        $SOTtolltip = ' | ' . $SOresultS;
                    }
                    break;

                case 1 :
                    $result .= $this->config['result_style'] == 1 ? '<br />' : ' ';
                    $result .= '('.Text::_('COM_JOOMLEAGUE_RESULTS_OVERTIME') . ')';
                    if (isset($leftResultOT))
                    {
                        $OTresultS = $leftResultOT . '&nbsp;' . $this->config['seperator'] . '&nbsp;' . $rightResultOT;
                        $SOTresult .= '<br /><span class="hasTip" title="' . Text::_('COM_JOOMLEAGUE_RESULTS_OVERTIME2') .
                            '::' . $OTresultS . '" >' . $OTresultS . '</span>';
                        $SOTtolltip = ' | ' . $OTresultS ;
                    }
                    break;
            }

            $link = isset($match->team1_result)
                ? JoomleagueHelperRoute::getMatchReportRoute($this->project->slug,$match->id)
                : JoomleagueHelperRoute::getNextMatchRoute($this->project->slug,$match->id);
            $ResultsTooltipTitle = $result;

            if ($this->config['results_linkable'] == 1)
            {
                $result = HTMLHelper::link($link,$result);
            }

            $ResultsTooltipTp = '(';
            $PartResult = '';

            if ($team1_result_split_present && $team2_result_split_present)
            {
                //Part results
                if (!is_array($part_results_left))
                {
                    $part_results_left = array($part_results_left);
                }
                if (!is_array($part_results_right))
                {
                    $part_results_right = array($part_results_right);
                }

                for ($i = 0; $i < count($part_results_left); $i++)
                {
                    if (isset($part_results_left[$i]))
                    {
                        $resultS = $part_results_left[$i] . '&nbsp;' . $this->config['seperator'] . '&nbsp;' . $part_results_right[$i];
                        $whichPeriod = $i + 1;
                        $PartResult .= '<br /><span class="hasTip" title="' . Text::sprintf('COM_JOOMLEAGUE_GLOBAL_NPART',  "$whichPeriod") .
                            '::' . $resultS . '" >' . $resultS . '</span>';
                        $ResultsTooltipTp .= $i != 0 ? ' | ' . $resultS : $resultS;
                    }
                }
            }

            $ResultsTooltipTp .= $SOTtolltip . ')';

            if ($team1_result_split_present && $team2_result_split_present)
            {
                if ($this->config['show_part_results'])
                {
                    $result .= $PartResult . $SOTresult;
                }
                else
                {
                    //No need to show a tooltip if the parts are shown anyways
                    $result = '<span class="hasTip" title="' .$ResultsTooltipTitle . '::' . $ResultsTooltipTp . '" >' . $result . '</span>';
                }
            }

            if ($match->alt_decision)
            {
                $result = '<b style="color:red;">';
                $result .= $leftResultDEC . '&nbsp;' . $this->config['seperator'] . '&nbsp;' . $rightResultDEC;
                $result .= '</b>';
            }

            $score = "<td align='center'>" . $result . '</td>';
        }
        else
        {
            $score = '<td>' . Text::_($match->cancel_reason) . '</td>';
        }

        switch ($this->config['result_style'])
        {
            case 1 :
                echo $this->config['switch_home_guest'] ? $teamB.$score.$teamA : $teamA.$score.$teamB;
                break;

            default;
            case 0 :
                echo $this->config['switch_home_guest'] ? $teamB.$separator.$teamA.$score : $teamA.$separator.$teamB.$score;
                break;
        }
        ?>

        <?php if ($this->config['show_referee']): ?>
                <td>
        <?php
        if (isset($match->referees) && count($match->referees) > 0)
        {
            if ($this->project->teams_as_referees)
            {
                $output = '';
                $toolTipTitle = Text::_('COM_JOOMLEAGUE_TEAMPLAN_REF_TOOLTIP');
                $toolTipText = '';

                for ($i = 0; $i < count($match->referees); $i++)
                {
                    if ($match->referees[$i]->referee_name != '')
                    {
                        $output .= $match->referees[$i]->referee_name;
                        $toolTipText .= $match->referees[$i]->referee_name . '&lt;br /&gt;';
                    }
                    else
                    {
                        $output .= '-';
                        $toolTipText .= '-&lt;br /&gt;';
                    }
                }
                if ($this->config['show_referee'] == 1)
                {
                    echo $output;
                }
                elseif ($this->config['show_referee'] == 2)
                {
                ?>
                    <span class='hasTip' title='<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>'>
                        <img src='<?php echo Uri::root(); ?>media/com_joomleague/jl_images/icon-16-Referees.png' alt='' title='' />
                    </span>
                <?php
                }
            }
            else
            {
                $output = '';
                $toolTipTitle = Text::_('COM_JOOMLEAGUE_TEAMPLAN_REF_TOOLTIP');
                $toolTipText = '';

                for ($i = 0; $i < count($match->referees); $i++)
                {
                    if ($match->referees[$i]->referee_lastname != '' && $match->referees[$i]->referee_firstname)
                    {
                        $output .= '<span class="hasTip" title="' . Text::_('COM_JOOMLEAGUE_TEAMPLAN_REF_FUNCTION') .
                            '::' . $match->referees[$i]->referee_position_name . '">';
                        $ref = $match->referees[$i]->referee_lastname . ',' . $match->referees[$i]->referee_firstname;
                        $toolTipText .= $ref . ' ('.$match->referees[$i]->referee_position_name . ')' . '&lt;br /&gt;';
                        if ($this->config['show_referee_link'])
                        {
                            $link = JoomleagueHelperRoute::getRefereeRoute($this->project->slug, $match->referees[$i]->referee_id, 3);
                            $ref = HTMLHelper::link($link, $ref);
                        }
                        $output .= $ref;
                        $output .= '</span>';

                        if (($i + 1) < count($match->referees))
                        {
                            $output .= ' - ';
                        }
                    }
                    else
                    {
                        $output .= '-';
                    }
                }

                if ($this->config['show_referee'] == 1)
                {
                    echo $output;
                }
                elseif ($this->config['show_referee'] == 2)
                {
                    ?>
                    <span class='hasTip' title='<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>'>
                        <img src='<?php echo Uri::root(); ?>media/com_joomleague/jl_images/icon-16-Referees.png' alt='' title='' />
                    </span>
                    <?php
                }
            }
        }
        else
        {
            echo '-';
        }
        ?>
                </td>
        <?php endif; ?>

        <?php if ($this->config['show_thumbs_picture'] & $teamid > 0): ?>
                <td><?php echo JoomleagueHelperHtml::getThumbUpDownImg($match, $this->ptid); ?></td>
        <?php endif; ?>

        <?php if ($this->config['show_matchreport_column']): ?>
                <td>
        <?php
        if (!$match->cancel)
        {
            if (isset($match->team1_result))
            {
                $href_text = $this->config['show_matchreport_image']
                    ? HTMLHelper::image($this->config['matchreport_image'], Text::_('COM_JOOMLEAGUE_TEAMPLAN_VIEW_MATCHREPORT'))
                    : Text::_('COM_JOOMLEAGUE_TEAMPLAN_VIEW_MATCHREPORT');
                $link = JoomleagueHelperRoute::getMatchReportRoute($this->project->slug, $match->id);
            }
            else
            {
                $href_text = $this->config['show_matchreport_image']
                    ? HTMLHelper::image($this->config['matchpreview_image'], Text::_('COM_JOOMLEAGUE_TEAMPLAN_VIEW_MATCHPREVIEW'))
                    : Text::_('COM_JOOMLEAGUE_TEAMPLAN_VIEW_MATCHPREVIEW');
                $link = JoomleagueHelperRoute::getNextMatchRoute($this->project->slug, $match->id);
            }
            echo HTMLHelper::link($link, $href_text);
        }
        ?>
                </td>
        <?php endif; ?>
            </tr>

    <?php if ($hasEvents): ?>
            <!-- Show icon for editing events in edit mode -->
            <tr class="events <?php echo ($k == 0) ? '' : 'alt'; ?>">
                <td colspan='<?php echo $nbcols; ?>'>
                    <div id='info<?php echo $match->id; ?>' style='display: none;'>
                        <table class='matchreport' border='0'>
                            <tr>
                                <td>
                                    <?php echo $this->showEventsContainerInResults($match, $this->projectevents,
                                        $events, $subs, $this->config);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
    <?php endif; ?>

    <?php
    $k = 1 - $k;
    $counter++;
    $MatchDateLine = JoomleagueHelper::getMatchDate($match, Text::_('COM_JOOMLEAGUE_CLUBPLAN_MATCHDATE'));
    $MatchDay = $match->name;
    }
    ?>
        </table>
    <?php
    }
else
{
    ?>
<h3><?php echo Text::_('COM_JOOMLEAGUE_TEAMPLAN_NO_MATCHES'); ?></h3>
    <?php
}
?>