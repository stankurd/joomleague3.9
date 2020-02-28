<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Ranking helper.
 *
 * You can extends this class to add functionnalities
 * @author julien
 *
 */
class JLGRanking
{
	/**
	 * project id
	 * @var int
	 */
	var $_projectid = 0;
	/**
	 * project model cache
	 * @var object
	 */
	var $_project = null;
	/**
	 * caching for the data
	 * @var object
	 */
	var $_data = null;
	/**
	 * ranking parameters
	 * @var array
	 */
	var $_params = null;
	/**
	 * criteria for ranking order
	 * @var array
	 */
	var $_criteria = null;
	/**
	 * ranking mode: 0/1/2 for normal/home/away ranking
	 * @var int
	 */
	var $_mode = 0;
	/**
	 * starting roundid for the ranking
	 * @var int
	 */
	var $_from = null;
	/**
	 * end roundid for the ranking
	 * @var int
	 */
	var $_to       = null;
	/**
	 * division id
	 * @var int
	 */
	var $_division = null;

	/**
	 * caching for heat to head ranking
	 * @var array
	 */
	var $_h2h = null;
	/**
	 * storing current group id for caching h2h collect
	 * @var array
	 */
	var $_h2h_group = 0;

	/**
	 * divisions matching _division and childs
	 * @var unknown_type
	 */
	var $_divisions = null;

	/**
	 * array of roundcodes indexed by round id
	 * @var array
	 */
	var $_roundcodes = null;

	/**
	 * get instance of ranking. Looks into extension folder too.
	 *
	 * @param string $type
	 */
	public static function getInstance($project = null)
	{
		if ($project)
		{
			$extensions = JoomleagueHelper::getExtensions($project->id);

			foreach ($extensions as $type)
			{
				$classname = 'JLGRanking'. ucfirst($type);
				if (!class_exists($classname))
				{
					$file = JLG_PATH_SITE.'/extensions/'.$type.'/ranking.php';
					if (file_exists($file))
					{
						require_once($file);
						$obj = new $classname();
						$obj->setProjectId($project->id);
						return $obj;
					}
				}
				else
				{
					$obj = new $classname();
					$obj->setProjectId($project->id);
					return $obj;
				}
			}
			$obj = new JLGRanking();
			$obj->setProjectId($project->id);
			return $obj;
		}
		$obj = new JLGRanking();
		return $obj;
	}

	/**
	 * set project id.
	 *
	 * inits project object and parameters
	 * @param int $id
	 */
	public function setProjectId($id)
	{
	    JLoader::register('JoomleagueModelProject', JLG_PATH_SITE . '/models/project.php');
		$this->_projectid = (int) $id;
		$this->_project = new JoomleagueModelProject();
		
		$this->_project->setProjectID($id);
		$this->_params = $this->_project->getTemplateConfig('ranking');

		// wipe data
		$this->_data = null;
	}

	/**
	 * sets division id
	 * if not null, the return ranking will be for this division
	 * @param $id
	 */
	public function setDivisionId($id=0)
	{
		$this->_division  = $id;
		$this->_divisions = null;
	}

	/**
	 * returns ranking
	 *
	 * @param int roundid from
	 * @param int roundid to
	 * @param int division id
	 */
	public function getRanking($from = null, $to = null, $division = 0)
	{
		$this->_from = $from;
		$this->_to   = $to;
		$this->_mode = 0;
		$this->setDivisionId($division);

		$teams = $this->_collect();

		$rankings = $this->_buildRanking($teams);

		return $rankings;
	}


	/**
	 * returns home ranking
	 *
	 * @param int roundid from
	 * @param int roundid to
	 * @param int division id
	 */
	public function getRankingHome($from = null, $to = null, $division = 0)
	{
		$this->_from = $from;
		$this->_to   = $to;
		$this->_mode = 1;
		$this->setDivisionId($division);

		$teams = $this->_collect();
		$rankings = $this->_buildRanking($teams);

		return $rankings;
	}


	/**
	 * returns away ranking
	 *
	 * @param int roundid from
	 * @param int roundid to
	 * @param int division id
	 */
	public function getRankingAway($from = null, $to = null, $division = 0)
	{
		$this->_from = $from;
		$this->_to   = $to;
		$this->_mode = 2;
		$this->setDivisionId($division);

		$teams = $this->_collect();
		$rankings = $this->_buildRanking($teams);

		return $rankings;
	}
	
	/**
	 * return games and initial team objects
	 *
	 * @return object with properties _teams and _matches
	 */
	public function _initData()
	{
		if (!$this->_projectid)
		{
		    Factory::getApplication()->enqueueMessage(Text::_('COM_JOOMLEAGUE_RANKING_ERROR_PROJECTID_REQUIRED'),'warning');
			return false;
		}

		// Get a reference to the global cache object.
		$cache = Factory::getCache('joomleague.project.'.$this->_projectid.'.division.'.$this->_division);
		// Enable caching regardless of global setting
		$params = ComponentHelper::getParams('com_joomleague');
		// TODO: ranking is not immediately updated when a match result is entered. Is this due to this forcing here?
		if ($params->get('force_ranking_cache', 1))
		{
			$cache->setCaching( 1 );
		}

		$class = get_class($this);
		$newClass = new $class();
		//$data = $cache->__call(array($newClass, '_cachedGetData'), $this->_projectid, $this->_division);
		$data = self::_cachedGetData($this->_projectid, $this->_division);
		
		return $data;
	}

	/**
	 * cached method to collect the results
	 *
	 * @param int project id
	 */
	public function _cachedGetData($pid, $division=0)
	{
		$data = new stdclass();
		$data->_teams   = self::_initTeams($pid, $division);
		$data->_matches = self::_getMatches($pid, $division);

		return $data;
	}

	/**
	 * return teams data according to match result in ranking object table
	 *
	 * @param array int project team ids: only collect for games between specified teams (usefull for head to head)
	 */
	public function _collect($ptids = null)
	{
		$mode     	= $this->_mode;
		$from     	= $this->_from;
		$to       	= $this->_to;
		$division 	= $this->_division;
		$project  	= $this->_project->getProject();
		$data 		= $this->_initData();
		foreach ((array)$data->_matches as $match)
		{

			if (!isset($data->_teams[$match->projectteam1_id]) || $data->_teams[$match->projectteam1_id]->_is_in_score === 0 ||
				!isset($data->_teams[$match->projectteam2_id]) || $data->_teams[$match->projectteam2_id]->_is_in_score === 0)
			{
				continue;
			}

			if (!$this->_countGame($match, $from, $to, $ptids))
			{
				continue;
			}
			if ($match->projectteam1_id==0 || $match->projectteam2_id==0)
			{
				continue;
			}

			$homeId = $match->projectteam1_id;
			$awayId = $match->projectteam2_id;

			if ($mode == 0 || $mode == 1)
			{
				$home = &$data->_teams[$homeId];
			}
			else
			{
				$home = new JLGRankingTeam(0); //in that case, $data wont be affected
			}
			if ($mode == 0 || $mode == 2)
			{
				$away = &$data->_teams[$awayId];
			}
			else
			{
				$away = new JLGRankingTeam(0); //in that case, $data wont be affected
			}

			$decision = $match->decision;
			if ($decision == 0)
			{
				$home_score = $match->home_score;
				$away_score = $match->away_score;
				$leg1 = $match->l1;
				$leg2 = $match->l2;
			}
			else
			{
				$home_score = $match->home_score_decision;
				$away_score = $match->away_score_decision;
				$leg1 = 0;
				$leg2 = 0;
			}

			$home->cnt_matches++;
			$away->cnt_matches++;

			$resultType = ($project->allow_add_time) ? $match->match_result_type : 0;

			$arr[0] = 0;
			$arr[1] = 0;
			$arr[2] = 0;
			switch($resultType)
			{
				case 1:
					$arr = explode(",",$project->points_after_add_time);
					break;
				case 2:
					$arr = explode(",",$project->points_after_penalty);
					break;
				default:
					$arr = explode(",",$project->points_after_regular_time);
					break;
			}
			$win_points  = (isset($arr[0])) ? $arr[0] : 3;
			$draw_points = (isset($arr[1])) ? $arr[1] : 1;
			$loss_points = (isset($arr[2])) ? $arr[2] : 0;

			$home_ot = $match->home_score_ot;
			$away_ot = $match->away_score_ot;
			$home_so = $match->home_score_so;
			$away_so = $match->away_score_so;

			if ($decision != 1)
			{
				if( $home_score > $away_score )
				{
					switch ($resultType)
					{
					case 0:
						$home->cnt_won++;
						$home->cnt_won_home++;

						$away->cnt_lost++;
						$away->cnt_lost_away++;
						break;
					case 1:
						$home->cnt_wot++;
						$home->cnt_wot_home++;
						$home->cnt_won++;
						$home->cnt_won_home++;

						$away->cnt_lot++;
						$away->cnt_lot_away++;
						//When LOT, LOT=1 but No LOSS Count(Hockey)
						//$away->cnt_lost++;
						//$away->cnt_lost_home++;
						break;
					case 2:
						$home->cnt_wso++;
						$home->cnt_wso_home++;
						$home->cnt_won++;
						$home->cnt_won_home++;

						$away->cnt_lso++;
						$away->cnt_lso_away++;
						$away->cnt_lot++;
						$away->cnt_lot_away++;
						//When LSO ,LSO=1 and LOT=1 but No LOSS Count (Hockey)
						//$away->cnt_lost++;
						//$away->cnt_lost_home++;
						break;
					}

					$home->sum_points += $win_points; //home_score can't be null...
					$away->sum_points += ( $decision == 0 || isset($away_score) ? $loss_points : 0);

					$home->neg_points += $loss_points;
					$away->neg_points += ( $decision == 0 || isset($away_score) ? $win_points : 0);
				}
				else if ( $home_score == $away_score )
				{
					switch ($resultType)
					{
						case 0:
							$home->cnt_draw++;
							$home->cnt_draw_home++;

							$away->cnt_draw++;
							$away->cnt_draw_away++;
							break;
						case 1:
							if ( $home_ot > $away_ot)
							{
								$home->cnt_won++;
								$home->cnt_won_home++;
								$home->cnt_wot++;
								$home->cnt_wot_home++;

								$away->cnt_lost++;
								$away->cnt_lost_away++;
								$away->cnt_lot++;
								$away->cnt_lot_away++;
							}
							if ( $home_ot < $away_ot)
							{
								$away->cnt_won++;
								$away->cnt_won_home++;
								$away->cnt_wot++;
								$away->cnt_wot_home++;

								$home->cnt_lost++;
								$home->cnt_lost_away++;
								$home->cnt_lot++;
								$home->cnt_lot_away++;
							}
							break;
						case 2:
							if ( $home_so > $away_so)
							{
								$home->cnt_won++;
								$home->cnt_won_home++;
								$home->cnt_wso++;
								$home->cnt_wso_home++;

								$away->cnt_lost++;
								$away->cnt_lost_away++;
								$away->cnt_lso++;
								$away->cnt_lso_away++;
							}
							if ( $home_so < $away_so)
							{
								$away->cnt_won++;
								$away->cnt_won_home++;
								$away->cnt_wso++;
								$away->cnt_wso_home++;

								$home->cnt_lost++;
								$home->cnt_lost_away++;
								$home->cnt_lso++;
								$home->cnt_lso_away++;
							}
							break;
					}
					$home->sum_points += ( $decision == 0 || isset($home_score) ? $draw_points : 0);
					$away->sum_points += ( $decision == 0 || isset($away_score) ? $draw_points : 0);

					$home->neg_points += ( $decision == 0 || isset($home_score) ? ($win_points-$draw_points): 0); // bug fixed, timoline 250709
					$away->neg_points += ( $decision == 0 || isset($away_score) ? ($win_points-$draw_points) : 0);// ex. for soccer, your loss = 2 points not 1 point
				}
				else if ( $home_score < $away_score )
				{
					switch ($resultType) {
						case 0:
							$home->cnt_lost++;
							$home->cnt_lost_home++;

							$away->cnt_won++;
							$away->cnt_won_away++;
							break;
						case 1:
							$home->cnt_lot++;
							$home->cnt_lot_home++;
							//When LOT, LOT=1 but No LOSS Count(Hockey)
							//$home->cnt_lost++;
							//$home->cnt_lost_home++;

							$away->cnt_wot++;
							$away->cnt_wot_away++;
							$away->cnt_won++;
							$away->cnt_won_away++;
							break;
						case 2:
							$home->cnt_lso++;
							$home->cnt_lso_home++;
							$home->cnt_lot++;
							$home->cnt_lot_home++;
							//When LSO ,LSO=1 and LOT=1 but No LOSS Count (Hockey)
							//$home->cnt_lost++;
							//$home->cnt_lost_home++;

							$away->cnt_wso++;
							$away->cnt_wso_away++;
							$away->cnt_won++;
							$away->cnt_won_away++;
							break;
					}

					$home->sum_points += ( $decision == 0 || isset($home_score) ? $loss_points : 0);
					$away->sum_points += $win_points;

					$home->neg_points += ( $decision == 0 || isset($home_score) ? $win_points : 0);
					$away->neg_points += $loss_points;
				}
			}
			else
			{
				//Final Win/Loss Decision
				if($match->team_won == 0)
				{
					$home->cnt_lost++;
					$away->cnt_lost++;
					//record a won on the home team
				}
				else if($match->team_won == 1)
				{
					$home->cnt_won++;
					$away->cnt_lost++;
					$home->sum_points += $win_points;
					$away->cnt_lost_home++;
					$away->neg_points += $loss_points + $match->away_bonus;
					//record a won on the away team
				}
				else if($match->team_won == 2)
				{
					$away->cnt_won++;
					$home->cnt_lost++;
					$away->sum_points += $win_points;
					$home->cnt_lost_home++;
					$home->neg_points += $loss_points + $match->home_bonus;
					//record a loss on both teams
				}
				else if($match->team_won == 3)
				{
					$home->cnt_lost++;
					$away->cnt_lost++;
					$away->cnt_lost_home++;
					$home->cnt_lost_home++;
					$home->neg_points += $loss_points + $match->home_bonus;
					$away->neg_points += $loss_points + $match->away_bonus;
					//record a won on both teams
				}
				else if($match->team_won == 4)
				{
					$home->cnt_won++;
					$away->cnt_won++;
					$home->sum_points += $win_points;
					$away->sum_points += $win_points;
					$home->neg_points += $win_points + $match->home_bonus;
					$away->neg_points += $win_points + $match->away_bonus;
				}
				else
				{
					$home->neg_points += $loss_points;
					$away->neg_points += $loss_points;
				}
			}
			/*winpoints*/
			$home->winpoints = $win_points;

			/* bonus points */
			$home->sum_points += $match->home_bonus;
			$home->bonus_points += $match->home_bonus;

			$away->sum_points += $match->away_bonus;
			$away->bonus_points += $match->away_bonus;

			/* goals for/against/diff */
			$home->sum_team1_result += $home_score;
			$home->sum_team2_result += $away_score;
			$home->diff_team_results = $home->sum_team1_result - $home->sum_team2_result;
			$home->sum_team1_legs   += $leg1;
			$home->sum_team2_legs   += $leg2;
			$home->diff_team_legs    = $home->sum_team1_legs - $home->sum_team2_legs;

			$away->sum_team1_result += $away_score;
			$away->sum_team2_result += $home_score;
			$away->diff_team_results = $away->sum_team1_result - $away->sum_team2_result;
			$away->sum_team1_legs   += $leg2;
			$away->sum_team2_legs   += $leg1;
			$away->diff_team_legs    = $away->sum_team1_legs - $away->sum_team2_legs;

			$away->sum_away_for += $away_score;
		}

		return $data->_teams;
	}

	/**
	 * gets team info from db
	 *
	 * @return array of JLGRankingTeam objects
	 */
	public function _initTeams($pid, $division=0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('pt.id', 'ptid'))
			->select($db->quoteName('pt.is_in_score'))
			->select($db->quoteName('pt.start_points'))
			->select($db->quoteName('pt.division_id'))
			->select($db->quoteName('t.name'))
			->select($db->quoteName('t.id', 'teamid'))
			->select($db->quoteName('pt.neg_points_finally'))
			->select($db->quoteName('pt.use_finally'))
			->select($db->quoteName('pt.points_finally'))
			->select($db->quoteName('pt.matches_finally'))
			->select($db->quoteName('pt.won_finally'))
			->select($db->quoteName('pt.draws_finally'))
			->select($db->quoteName('pt.lost_finally'))
			->select($db->quoteName('pt.homegoals_finally'))
			->select($db->quoteName('pt.guestgoals_finally'))
			->select($db->quoteName('pt.diffgoals_finally'))
			->from($db->quoteName('#__joomleague_project_team', 'pt'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't') .
				' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('pt.team_id'))
			->where($db->quoteName('pt.project_id') . ' = ' . (int)$pid)
			->where($db->quoteName('pt.is_in_score') . ' = 1');

		if($division > 0)
		{
			$query
				->where($db->quoteName('pt.division_id') . ' = ' . (int)$division);
		}

		$db->setQuery($query);
		$res = $db->loadObjectList();

		$teams = array();
		foreach ((array) $res as $r)
		{
			$t = new JLGRankingTeam($r->ptid);
			$t->setTeamid($r->teamid);
			$t->setDivisionid($r->division_id);
			$t->setStartpoints($r->start_points);
			$t->setNegpoints($r->neg_points_finally);
			$t->setName($r->name);

// new for is_in_score
			$t->setIs_In_Score($r->is_in_score);

// new for use_finally
			$t->setuse_finally($r->use_finally);
			$t->setpoints_finally($r->points_finally);
			$t->setneg_points_finally($r->neg_points_finally);
			$t->setmatches_finally($r->matches_finally);
			$t->setwon_finally($r->won_finally);
			$t->setdraws_finally($r->draws_finally);
			$t->setlost_finally($r->lost_finally);
			$t->sethomegoals_finally($r->homegoals_finally);
			$t->setguestgoals_finally($r->guestgoals_finally);
			$t->setdiffgoals_finally($r->diffgoals_finally);

			if ( $r->use_finally )
			{
				$t->sum_points = $r->points_finally;
				$t->neg_points = $r->neg_points_finally;
				$t->cnt_matches = $r->matches_finally;
				$t->cnt_won = $r->won_finally;
				$t->cnt_draw = $r->draws_finally;
				$t->cnt_lost = $r->lost_finally;
				$t->sum_team1_result = $r->homegoals_finally;
				$t->sum_team2_result = $r->guestgoals_finally;
				$t->diff_team_results = $r->diffgoals_finally;

//       		if ( empty($t->diff_team_results) )
//       		{
//       			$t->diff_team_results = $r->homegoals_finally - $r->guestgoals_finally;
//       		}
//       		if ( empty($t->cnt_matches) )
//       		{
//       			$t->cnt_matches = $r->won_finally + $r->draws_finally + $r->lost_finally;
//       		}
			}

			$teams[$r->ptid] = $t;
		}

		return $teams;
	}

	/**
	 * gets games from db
	 *
	 * @return array
	 */
	public function _getMatches($pid, $division=0)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('m.id'))
			->select($db->quoteName('m.projectteam1_id'))
			->select($db->quoteName('m.projectteam2_id'))
			->select($db->quoteName('m.team1_result', 'home_score'))
			->select($db->quoteName('m.team2_result', 'away_score'))
			->select($db->quoteName('m.team1_bonus', 'home_bonus'))
			->select($db->quoteName('m.team2_bonus', 'away_bonus'))
			->select($db->quoteName('m.team1_legs', 'l1'))
			->select($db->quoteName('m.team2_legs', 'l2'))
			->select($db->quoteName('m.match_result_type', 'match_result_type'))
			->select($db->quoteName('m.alt_decision', 'decision'))
			->select($db->quoteName('m.team1_result_decision', 'home_score_decision'))
			->select($db->quoteName('m.team2_result_decision', 'away_score_decision'))
			->select($db->quoteName('m.team1_result_ot', 'home_score_ot'))
			->select($db->quoteName('m.team2_result_ot', 'away_score_ot'))
			->select($db->quoteName('m.team1_result_so', 'home_score_so'))
			->select($db->quoteName('m.team2_result_so', 'away_score_so'))
			->select($db->quoteName('r.id', 'roundid'))
			->select($db->quoteName('m.team_won'))
			->select($db->quoteName('r.roundcode'))
			->from($db->quoteName('#__joomleague_match', 'm'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
				' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
			->join('INNER', $db->quoteName('#__joomleague_round', 'r') .
				' ON ' . $db->quoteName('r.id') . ' = ' . $db->quoteName('m.round_id'))
			->where($db->quoteName('m.count_result') . ' = 1')
			->where($db->quoteName('m.published') . ' = 1')
			->where($db->quoteName('m.cancel') . ' IS NULL OR ' . $db->quoteName('m.cancel') . ' = 0')
			->where($db->quoteName('m.projectteam1_id') . ' > 0')
			->where($db->quoteName('m.projectteam2_id') . ' > 0')
			->where($db->quoteName('pt1.project_id') . ' = ' . (int)$pid)
			->where('((' . $db->quoteName('m.team1_result') . ' IS NOT NULL' .
					' AND ' . $db->quoteName('m.team2_result') . ' IS NOT NULL)' .
				' OR ' . $db->quoteName('m.alt_decision') . ' = 1)')
		;

		if ($division > 0)
		{
			$query
				->where($db->quoteName('pt1.division_id') . ' = ' . (int)$division);
		}

		$db->setQuery($query);
		$matches = $db->loadObjectList('id');
		return $matches;
	}

	/**
	 * return an array of divisions matching current division id (this division, and children divisions)
	 *
	 * @return array
	 */
	public function _getSubDivisions()
	{
		if (!$this->_division)
		{
			return false;
		}
		else if (empty($this->_divisions))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('id'))
				->from($db->quoteName('#__joomleague_division'))
				->where($db->quoteName('project_id') . ' = ' . (int)$this->_projectid)
				->where($db->quoteName('parent_id') . ' = ' . (int)$this->_division);

			$db->setQuery($query);
			$res = $db->loadColumn();
			$res[] = $this->_division;
			$this->_divisions = $res;
		}
		return $this->_divisions;
	}

	/**
	 * returns true if the game matches the criteria
	 *
	 * @param object match
	 * @param int from: first round id
	 * @param int to: last round id
	 * @param int division id
	 * @param array int project team ids, game must be between team in that array
	 */
	function _countGame($game, $from = null, $to = null, $ptids = null)
	{
		$res = true;

		if ($from)
		{
			if ( $this->_getRoundcode($game->roundid) < $this->_getRoundcode($from) ) {
				return false;
			}
		}

		if ($to)
		{
			if ( $this->_getRoundcode($game->roundid) > $this->_getRoundcode($to) ) {
				return false;
			}
		}

		if ($ptids)
		{
			if (!in_array($game->projectteam1_id, $ptids) || !in_array($game->projectteam2_id, $ptids)) {
				return false;
			}
		}

		return $res;
	}

	/**
	 * return round roundcode
	 *
	 * @param int $round_id
	 * @return int roundcode
	 */
	function _getRoundcode($round_id)
	{
		if (empty($this->_roundcodes))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('r.roundcode'))
				->select($db->quoteName('r.id'))
				->from($db->quoteName('#__joomleague_round', 'r'))
				->where($db->quoteName('r.project_id') . ' = ' . (int)$this->_projectid);

			$db->setQuery($query);
			$this->_roundcodes = $db->loadAssocList('id');
		}
		if (!isset($this->_roundcodes[$round_id]))
		{
		    
		    Factory::getApplication()->enqueueMessage(0, Text::_('COM_JOOMLEAGUE_RANKING_ERROR_UNKOWN_ROUND_ID').': '.$round_id, 'warning');
			return false;
		}
		return $this->_roundcodes[$round_id];
	}

	/**
	 * Returns ranking criteria as an array of methods
	 * This method will look for method matching the specified criteria: e.g, if the criteria is 'points',
	 * it will look for _cmpPoints. If the method exists, the criteria is accepted.
	 *
	 * @return array
	 */
	function _getRankingCriteria()
	{
		if (empty($this->_criteria))
		{
			// get the values from ranking template setting
			$values = explode(',', $this->_params['ranking_order']);
			$crit = array();
			foreach ($values as $v)
			{
				$v = ucfirst(strtolower(trim($v)));
				if (method_exists($this, '_cmp'.$v))
				{
					$crit[] = '_cmp'.$v;
				}
				else
				{
				    Factory::getApplication()->enqueueMessage(0, Text::_('COM_JOOMLEAGUE_RANKING_NOT_VALID_CRITERIA').': '.$v, 'warnning');
				}
			}
			// set a default criteria if empty
			if (!count($crit))
			{
				$crit[] = '_cmpPoints';
			}
			$this->_criteria = $crit;
		}
		return $this->_criteria;
	}

	/**
	 * returns the ranking for selected teams
	 *
	 * @param array teams objects
	 */
	function _buildRanking($teams)
	{
		// division filtering
		$teams = array_filter($teams, array($this, "_filterdivision"));

		// initial group contains all teams, unordered, indexed by starting rank 1
		$groups = array( 1 => $teams );

		// each criteria will sort teams. All teams that are 'equal' for a given criteria are put in a 'group'
		// the next criteria will sort inside each of the previously obtained groups
		// in the end, we obtain a certain number of groups, indexed by rank. this can contain one to several teams of same rank
		foreach ($this->_getRankingCriteria() as $c)
		{
			$newgroups = array(); // groups that will be used for next loop
			foreach ($groups as $rank => $teams)
			{
				// for head to head, we have to init h2h values that are used to collect results just for this group
				$this->_h2h_group = $teams; // teans of the group
				$this->_h2h = null;         // wipe h2h data cache for group

				$newrank = $rank;
				$current = $rank;

				uasort($teams, array($this, $c)); // sort

				$prev = null;
				foreach ($teams as $k => $team)
				{
					if (!$prev || $this->$c($team, $prev) != 0)  // teams are not 'equal', create new group
					{
						$newgroups[$newrank] = array($k => $team);
						$current = $newrank;
					}
					else                                         // teams still have the same rank, add to current group
					{
						$newgroups[$current][$k] = $team;
					}
					$prev = $team;
					$newrank++;
				}
			}
			$groups = $newgroups;
		}

		// now, let's just sort by name for the team still tied, and output to single array
		$res = array();
		foreach ($groups as $rank => $teams)
		{
			uasort($teams, array($this, '_cmpAlpha'));
			foreach ($teams as $ptid => $t)
			{
				$t->rank = $rank;
				$res[$ptid] = $t;
			}
		}

		return $res;
	}

	/**
	 * for head to head ranking, we collect game data for current ranking group
	 */
	function _geth2h()
	{
		if (empty($this->_h2h))
		{
			$teams = $this->_h2h_group;

			if (empty($teams))
			{
				return false;
			}
			$ptids = array();
			foreach ($teams as $t)
			{
				$ptids[] = $t->_ptid;
			}
			$this->_h2h = $this->_collect($ptids);
		}
		return $this->_h2h;
	}

	/**
	 * callback function to filter teams not in divisions
	 * @param array $team
	 */
	function _filterdivision($team)
	{
		$divs = $this->_getSubDivisions();
		if (!$divs)
		{
			return true;
		}
		return (in_array($team->_divisionid, $divs));
	}

	/*****************************************************************************
	 *
	 * Compare functions (callbacks for uasort)
	 *
	 * You can add more criteria by just adding more _cmpXxxx functions, with Xxxx
	 * being the name of your criteria to be set in ranking template setting
	 *
	 *****************************************************************************/

	/**
	 * alphanumerical comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpAlpha($a, $b)
	{
		$res = strcasecmp($a->getName(), $b->getName());
		return $res;
	}

	/**
	 * Point comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpPoints($a, $b)
	{
		$res = -($a->getPoints() - $b->getPoints());
		return (int)$res;
	}

	/**
	 * NegPoint comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpNegpoints($a, $b)
	{
		$res = -($a->getNegpoints() - $b->getNegpoints());
		return (int)$res;
	}

	/**
	 * Bonus points comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpBonus($a, $b)
	{
		$res = -($a->bonus_points - $b->bonus_points);
		return $res;
	}

	/**
	 * Score difference comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpDiff($a, $b)
	{
		$res = -($a->diff_team_results - $b->diff_team_results);
		return $res;
	}

	/**
	 * Score for comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpFor($a, $b)
	{
		$res = -($a->sum_team1_result - $b->sum_team1_result);
		return $res;
	}

	/**
	 * Score against comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpAgainst($a, $b)
	{
		$res = ($a->sum_team2_result - $b->sum_team2_result);
		return $res;
	}

	/**
	 * Scoring average comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpScoreAvg($a, $b)
	{
		$res = -($a->scoreAvg() - $b->scoreAvg());
		return $res;
	}

	/**
	 * Scoring percentage comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpScorePct($a, $b)
	{
		$res = -($a->scorePct() - $b->scorePct());
		return $res;
	}


	/**
	 * Winning percentage comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpWinpct($a, $b)
	{
		$res = -($a->winPct() - $b->winPct());
		if ($res != 0)
		{
			$res=($res >= 0 ? 1 : -1);
		}
		return $res;
	}

	/**
	 * Gameback comparison (US sports)
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpGb($a, $b)
	{
		$res = -(($a->cnt_won - $b->cnt_won) + ($b->cnt_lost - $a->cnt_lost));
		return $res;
	}

	/**
	 * Score away comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpAwayfor($a, $b)
	{
		$res = -($a->sum_away_for - $b->sum_away_for);
		return $res;
	}

	/**
	 * Head to Head points comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpH2h($a, $b)
	{
		$teams = $this->_geth2h();
		// we do not include start points in h2h comparison
		$res = -($teams[$a->_ptid]->getPoints(false) - $teams[$b->_ptid]->getPoints(false));
		return $res;
	}

	/**
	 * Head to Head score difference comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpH2h_diff($a, $b)
	{
		$teams = $this->_geth2h();
		return $this->_cmpDiff($teams[$a->_ptid], $teams[$b->_ptid]);
	}

	/**
	 * Head to Head score for comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpH2h_for($a, $b)
	{
		$teams = $this->_geth2h();
		return $this->_cmpFor($teams[$a->_ptid], $teams[$b->_ptid]);
	}

	/**
	 * Head to Head scored away comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpH2h_away($a, $b)
	{
		$teams = $this->_geth2h();
		return $this->_cmpAwayfor($teams[$a->_ptid], $teams[$b->_ptid]);
	}

	/**
	 * Legs diff comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpLegs_diff($a, $b)
	{
		$res = -($a->diff_team_legs - $b->diff_team_legs);
		return $res;
	}

	/**
	 * Legs ratio comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpLegs_ratio($a, $b)
	{
		$res = -($a->legsRatio() - $b->legsRatio());
		if ($res != 0)
		{
			$res=($res >= 0 ? 1 : -1);
		}
		return $res;
	}

	/**
	 * Legs wins comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpLegs_win($a, $b)
	{
		$res = -($a->sum_team1_legs - $b->sum_team1_legs);
		return $res;
	}

	/**
	 * Total wins comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpWins($a, $b)
	{
		$res = -($a->cnt_won - $b->cnt_won);
		return $res;
	}

	/**
	 * Games played comparison, more games played, higher in rank
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpPlayed($a, $b)
	{
		$res = -($a->cnt_matches - $b->cnt_matches);
		return $res;
	}

		/**
	 * Games played ASC comparison, less games played, higher in rank
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpPlayedasc($a, $b)
	{
		$res = -($this->_cmpPlayed($a, $b));
		return $res;
	}
	/**
	 * Points ratio comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpPoints_ratio($a, $b)
	{
		$res = -($a->pointsRatio() - $b->pointsRatio());
		if ($res != 0)
		{
			$res = ($res >= 0 ? 1 : -1);
		}
		return $res;
	}
	/**
	 * OT_wins comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpWOT($a, $b)
	{
		$res = -($a->cnt_wot - $b->cnt_wot);
		return $res;
	}

	/**
	 * SO_wins comparison
	 * @param JLGRankingTeam a
	 * @param JLGRankingTeam b
	 * @return int
	 */
	function _cmpWSO($a, $b)
	{
		$res = -($a->cnt_wso - $b->cnt_wso);
		return $res;
	}
}

/**
 * Ranking team class
 * Support class for ranking helper
 */
class JLGRankingTeam
{

// new for use_finally
	var $_use_finally = 0;
	var $_points_finally = 0;
	var $_neg_points_finally = 0;
	var $_matches_finally = 0;
	var $_won_finally = 0;
	var $_draws_finally = 0;
	var $_lost_finally = 0;
	var $_homegoals_finally = 0;
	var $_guestgoals_finally = 0;
	var $_diffgoals_finally = 0;

	// new for is_in_score
	var $_is_in_score = 0;

	/**
	 * project team id
	 * @var int
	 */
	var $_ptid = 0;
	/**
	 * team id
	 * @var int
	 */
	var $_teamid = 0;
	/**
	 * division id
	 * @var int
	 */
	var $_divisionid = 0;
	/**
	 * start point / penalty
	 * @var int
	 */
	var $_startpoints = 0;
	/**
	 * team name
	 * @var string
	 */
	var $_name = null;

	var $cnt_matches   	= 0;
	var $cnt_won       	= 0;
	var $cnt_draw      	= 0;
	var $cnt_lost      	= 0;
	var $cnt_won_home  	= 0;
	var $cnt_draw_home 	= 0;
	var $cnt_lost_home 	= 0;
	var $cnt_won_away  	= 0;
	var $cnt_draw_away 	= 0;
	var $cnt_lost_away 	= 0;

	var $cnt_wot		= 0;
	var $cnt_wso		= 0;
	var $cnt_lot		= 0;
	var $cnt_lso		= 0;
	var $cnt_wot_home	= 0;
	var $cnt_wso_home	= 0;
	var $cnt_lot_home	= 0;
	var $cnt_lso_home	= 0;
	var $cnt_wot_away	= 0;
	var $cnt_wso_away	= 0;
	var $cnt_lot_away	= 0;
	var $cnt_lso_away	= 0;

	var $sum_points    	= 0;
	var $neg_points    	= 0;
	var $bonus_points  	= 0;
	var $sum_team1_result = 0;
	var $sum_team2_result = 0;
	var $sum_away_for   = 0;
	var $sum_team1_legs = 0;
	var $sum_team2_legs = 0;
	var $diff_team_results = 0;
	var $diff_team_legs = 0;
	var $round          = 0;
	var $rank           = 0;

	/**
	 * contructor requires ptid
	 * @param int $ptid
	 */
	function __construct($ptid)
	{
		$this->setPtid($ptid);
	}
/*
function JLGRankingTeam($ptid)
	{
		$this->setPtid($ptid);
	}
*/
// new for is_in_score
	function setis_in_score($val)
	{
		$this->_is_in_score = (int)$val;
	}

// new for use finally
	function setuse_finally($val)
	{
		$this->_use_finally = (int)$val;
	}
	function setpoints_finally($val)
	{
		$this->_points_finally = (int)$val;
	}
	function setneg_points_finally($val)
	{
		$this->_neg_points_finally = (int)$val;
	}
	function setmatches_finally($val)
	{
		$this->_matches_finally = (int)$val;
	}
	function setwon_finally($val)
	{
		$this->_won_finally = (int)$val;
	}
	function setdraws_finally($val)
	{
		$this->_draws_finally = (int)$val;
	}
	function setlost_finally($val)
	{
		$this->_lost_finally = (int)$val;
	}
	function sethomegoals_finally($val)
	{
		$this->_homegoals_finally = (int)$val;
	}
	function setguestgoals_finally($val)
	{
		$this->_guestgoals_finally = (int)$val;
	}
	function setdiffgoals_finally($val)
	{
		$this->_diffgoals_finally = (int)$val;
	}

	/**
	 * set project team id
	 * @param int ptid
	 */
	function setPtid($ptid)
	{
		$this->_ptid = (int)$ptid;
	}

	/**
	 * set team id
	 * @param int id
	 */
	function setTeamid($id)
	{
		$this->_teamid = (int)$id;
	}

	/**
	 * returns project team id
	 * @return int id
	 */
	function getPtid()
	{
		return $this->_ptid;
	}

	/**
	 * returns team id
	 * @return int id
	 */
	function getTeamid()
	{
		return $this->_teamid;
	}

	/**
	 * set team division id
	 * @param int val
	 */
	function setDivisionid($val = 0)
	{
		$this->_divisionid = (int)$val;
	}

	/**
	 * return team division id
	 * @return int id
	 */
	function getDivisionid()
	{
		return $this->_divisionid;
	}

	/**
	 * set team start points
	 * @param int val
	 */
	function setStartpoints($val)
	{
		$this->_startpoints = $val;
	}

	/**
	 * set team neg points
	 * @param int val
	 */
	function setNegpoints($val)
	{
		$this->neg_points = $val;
	}

	/**
	 * set team name
	 * @param string val
	 */
	function setName($val)
	{
		$this->_name = $val;
	}

	/**
	 * return winning percentage
	 *
	 * @return float
	 */
	function winPct()
	{
		if ($this->cnt_won + $this->cnt_lost + $this->cnt_draw == 0)
		{
			return 0;
		}
		else
		{
			return ($this->cnt_won / ($this->cnt_won + $this->cnt_lost + $this->cnt_draw)) * 100;
		}
	}


	/**
	 * return scoring average
	 *
	 * @return float
	 */
	function scoreAvg()
	{
		if ($this->sum_team2_result == 0)
		{
			return $this->sum_team1_result / 1;
		}
		else
		{
			return $this->sum_team1_result / $this->sum_team2_result;
		}
	}

	/**
	 * return scoring percentage
	 *
	 * @return float
	 */
	function scorePct()
	{
		$result = $this->scoreAvg() * 100;
		return $result;
	}


	/**
	 * return leg ratio
	 *
	 * @return float
	 */
	function legsRatio()
	{
		if ($this->sum_team2_legs == 0)
		{
			return $this->sum_team1_legs / 1;
		}
		else
		{
			return $this->sum_team1_legs / $this->sum_team2_legs;
		}
	}

	/**
	 * return points ratio
	 *
	 * @return float
	 */
	function pointsRatio()
	{
		if ($this->neg_points == 0)
		{
			// we do not include start points
			return $this->getPoints(false) / 1;
		}
		else
		{
			// we do not include start points
			return $this->getPoints(false) / $this->neg_points;
		}
	}

	/**
	 * return points quot
	 *
	 * @return float
	 */
	function pointsQuot()
	{
		if ($this->cnt_matches == 0)
		{
			// we do not include start points
			return $this->getPoints(false) / 1;
		}
		else
		{
			// we do not include start points
			return $this->getPoints(false) / $this->cnt_matches;
		}
	}


	function getName()
	{
		return $this->_name;
	}

	/**
	 * return points total
	 *
	 * @param boolean include start points, default true
	 */
	function getPoints($include_start = true)
	{
		if ($include_start)
		{
			return $this->sum_points + $this->_startpoints;
		}
		else
		{
			return $this->sum_points;
		}
	}

	/**
	 * return negpoints total
	 *
	 * @param boolean include start negpoints, default true
	 */
	function getNegpoints($include_start = true)
	{
		if ($include_start)
		{
			return $this->neg_points + $this->_neg_points_finally;
		}
		else
		{
			return $this->neg_points;
		}
	}



	/**
	 * GFA:Goal For Average per match = Goal for / played matches
	 *
	 * @return float
	 */
	function getGFA()
	{
		if ($this->cnt_matches == 0)
		{
			return $this->sum_team1_result / 1;
		}
		else
		{
			return $this->sum_team1_result / $this->cnt_matches;
		}
	}

	/**
	 * GAA:Goal Against Average per match = Goal against / played matches
	 *
	 * @return float
	 */
	function getGAA()
	{
		if ($this->cnt_matches == 0)
		{
			return $this->sum_team2_result / 1;
		}
		else
		{
			return $this->sum_team2_result / $this->cnt_matches;
		}
	}

	/**
	 * PpG:Points per Game = points / played matches
	 *
	 * @return float
	 */
	function getPPG()
	{
		if ($this->cnt_matches == 0)
		{
			return $this->getPoints(false) / 1;
		}
		else
		{
			return $this->getPoints(false) / $this->cnt_matches;
		}
	}

	/**
	 * %PP:Team points in relation into max points = (points / (played matches*win points))*100
	 *
	 * @return float
	 */
	function getPPP()
	{
		if (($this->cnt_matches * $this->winpoints) == 0)
		{
			return $this->getPoints(false) / 1;
		}
		else
		{
			return ($this->getPoints(false) / ($this->cnt_matches * $this->winpoints)) * 100;
		}
	}
}

