<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once('prediction.php');
//require_once(JLG_PATH_SITE . DS . 'helpers' . DS . 'pagination.php');

/**
 * Joomleague Component prediction Results Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
class JoomleagueModelPredictionResults extends JoomleagueModelPrediction
{

	function __construct()
	{
		parent::__construct();
	}

	function getMatches($roundID,$project_id)
	{
		if ($roundID==0){
			$roundID=1;
		}
		$query = 	"	SELECT	m.id AS mID,
								m.match_date,
								m.team1_result AS homeResult,
								m.team2_result AS awayResult,
								m.team1_result_decision AS homeDecision,
								m.team2_result_decision AS awayDecision,
								t1.name AS homeName,
								t2.name AS awayName,
								c1.logo_small AS homeLogo,
								c2.logo_small AS awayLogo

						FROM #__joomleague_match AS m

						INNER JOIN #__joomleague_round AS r ON	r.id=m.round_id AND
																r.project_id=$project_id AND
																r.id=$roundID
						LEFT JOIN #__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id
						LEFT JOIN #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id
						LEFT JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id
						LEFT JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id
						LEFT JOIN #__joomleague_club AS c1 ON c1.id=t1.club_id
						LEFT JOIN #__joomleague_club AS c2 ON c2.id=t2.club_id
						WHERE (m.cancel IS NULL OR m.cancel = 0)
						AND m.published=1
						ORDER BY m.match_date, m.id ASC";
		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();
		return $results;
	}

	function showClubLogo($clubLogo,$teamName)
	{
		$output = '';
		if ((!isset($clubLogo)) || ($clubLogo=='') || (!file_exists($clubLogo)))
		{
			$clubLogo='media/com_joomleague/placeholders/placeholder_small.gif';
		}
		$imgTitle = Text::sprintf('JL_PRED_RESULTS_LOGO_OF',$teamName);
		$output .= HTMLHelper::image($clubLogo,$imgTitle,array(' height' => 25, ' title' => $imgTitle));
		return $output;
	}

}
?>