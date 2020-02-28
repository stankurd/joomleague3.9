<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

$maxImportTime=ComponentHelper::getParams('com_joomleague')->get('max_import_time',0);
if (empty($maxImportTime))
{
	$maxImportTime=900;
}
if ((int)ini_get('max_execution_time') < $maxImportTime){@set_time_limit($maxImportTime);}

$maxImportMemory=ComponentHelper::getParams('com_joomleague')->get('max_import_memory',0);
if (empty($maxImportMemory))
{
	$maxImportMemory='150M';
}
if ((int)ini_get('memory_limit') < (int)$maxImportMemory){@ini_set('memory_limit',$maxImportMemory);}

jimport('joomla.filesystem.file');

/**
 * JLXML-Import Model
 *
 * @author	Zoltan Koteles
 * @author	Kurt Norgaz
 */
class JoomleagueModelJLXMLImport extends JLGModel
{
	var $_datas=array();
	var $_league_id=0;
	var $_season_id=0;
	var $_sportstype_id=0;
	var $import_version='';
	var $storeFailedColor = 'red';
	var $storeSuccessColor = 'green';
	var $existingInDbColor = 'orange';

	private function _getXml()
	{
		if (File::exists(JPATH_SITE.'/tmp/joomleague_import.jlg'))
		{
			if (function_exists('simplexml_load_file'))
			{
				return @simplexml_load_file(JPATH_SITE.'/tmp/joomleague_import.jlg','SimpleXMLElement',LIBXML_NOCDATA);
			}
			else
			{
			    Factory::getApplication()->enqueueMessage(Text::_('<a href="http://php.net/manual/en/book.simplexml.php" target="_blank">SimpleXML</a> does not exist on your system!'), 'warning');
			}
		}
		else
		{
		    Factory::getApplication()->enqueueMessage(Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR', 'Missing import file') , 'warning');
			echo "<script> alert('".Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR', 'Missing import file')."'); window.history.go(-1); </script>\n";
		}
	}

	public function getData()
	{
		libxml_use_internal_errors(true);
		if (!$xmlData=$this->_getXml())
		{
			$errorFound=false;
			echo Text::_('Load of the importfile failed:').'<br />';
			foreach(libxml_get_errors() as $error)
			{
				echo "<br>",$error->message;
				$errorFound=true;
			}
			if (!$errorFound){echo ' '.Text::_('Unknown error :-(');}
		}
		$i=0;
		$j=0;
		$k=0;
		$l=0;
		$m=0;
		$n=0;
		$o=0;
		$p=0;
		$q=0;
		$r=0;
		$s=0;
		$t=0;
		$u=0;
		$v=0;
		$w=0;
		$x=0;
		$y=0;
		$z=0;
		$mp=0;
		$ms=0;
		$mr=0;
		$me=0;
		$pe=0;
		$et=0;
		$ps=0;
		$mss=0;
		$mst=0;
		$tto=0;
		$ttn=0;
		$ttm=0;
		$tt=0;

		if ((isset($xmlData->record)) && (is_object($xmlData->record)))
		{
			foreach ($xmlData->record as $value)
			{
				// collect the project data of a .jlg file of JoomLeague <1.5x
				if ($xmlData->record[$i]['object']=='JoomLeagueVersion')
				{
					$this->_datas['exportversion']=$xmlData->record[$i];
				}

				// collect the project data of a .jlg file of JoomLeague <1.5x
				if ($xmlData->record[$i]['object']=='JoomLeague')
				{
					$this->_datas['project']=$xmlData->record[$i];
					$this->import_version='OLD';
					Factory::getApplication()->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_RENDERING_093'), 'notice');
				}

				// collect the project data of a .jlg file of JoomLeague 1.5x
				if ($xmlData->record[$i]['object']=='JoomLeague15')
				{
					$this->_datas['project']=$xmlData->record[$i];
					$this->import_version='NEW';
					Factory::getApplication()->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_RENDERING_15'), 'notice');
				}

				// collect the project data of a .jlg file of JoomLeague 1.5x
				if ($xmlData->record[$i]['object']=='JoomLeague20')
				{
					$this->_datas['project']=$xmlData->record[$i];
					$this->import_version='NEW';
					Factory::getApplication()->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_RENDERING_20'), 'notice');
				}

				// collect the division data
				if ($xmlData->record[$i]['object']=='LeagueDivision')
				{
					$this->_datas['division'][$j]=$xmlData->record[$i];
					$j++;
				}

				// collect the club data
				if ($xmlData->record[$i]['object']=='Club')
				{
					$this->_datas['club'][$k]=$xmlData->record[$i];
					$k++;
				}

				// collect the team data
				if ($xmlData->record[$i]['object']=='JL_Team')
				{
					$this->_datas['team'][$l]=$xmlData->record[$i];
					$l++;
				}

				// collect the projectteam data
				if ($xmlData->record[$i]['object']=='ProjectTeam')
				{
					$this->_datas['projectteam'][$m]=$xmlData->record[$i];
					$m++;
				}

				// collect the projectteam data of old export file / Here TeamTool instead of projectteam
				if ($xmlData->record[$i]['object']=='TeamTool')
				{
					$this->_datas['teamtool'][$m]=$xmlData->record[$i];
					$m++;
				}

				// collect the round data
				if ($xmlData->record[$i]['object']=='Round')
				{
					$this->_datas['round'][$n]=$xmlData->record[$i];
					$n++;
				}

				// collect the match data
				if ($xmlData->record[$i]['object']=='Match')
				{
					$this->_datas['match'][$o]=$xmlData->record[$i];
					$o++;
				}

				// collect the playgrounds data
				if ($xmlData->record[$i]['object']=='Playground')
				{
					$this->_datas['playground'][$p]=$xmlData->record[$i];
					$p++;
				}

				// collect the template data
				if ($xmlData->record[$i]['object']=='Template')
				{
					$this->_datas['template'][$q]=$xmlData->record[$i];
					$q++;
				}

				// collect the events data
				if ($xmlData->record[$i]['object']=='EventType')
				{
					$this->_datas['event'][$et]=$xmlData->record[$i];
					$et++;
				}

				// collect the positions data
				if ($xmlData->record[$i]['object']=='Position')
				{
					$this->_datas['position'][$t]=$xmlData->record[$i];
					$t++;
				}

				// collect the positions data
				if ($xmlData->record[$i]['object']=='ParentPosition')
				{
					$this->_datas['parentposition'][$z]=$xmlData->record[$i];
					$z++;
				}

				// collect the League data
				if ($xmlData->record[$i]['object']=='League')
				{
					$this->_datas['league']=$xmlData->record[$i];
				}

				// collect the Season data
				if ($xmlData->record[$i]['object']=='Season')
				{
					$this->_datas['season']=$xmlData->record[$i];
				}

				// collect the SportsType data
				if ($xmlData->record[$i]['object']=='SportsType')
				{
					$this->_datas['sportstype']=$xmlData->record[$i];
				}

				// collect the projectreferee data
				if ($xmlData->record[$i]['object']=='ProjectReferee')
				{
					$this->_datas['projectreferee'][$w]=$xmlData->record[$i];
					$w++;
				}

				// collect the projectposition data
				if ($xmlData->record[$i]['object']=='ProjectPosition')
				{
					$this->_datas['projectposition'][$x]=$xmlData->record[$i];
					$x++;
				}

				// collect the person data
				if ($xmlData->record[$i]['object']=='Person')
				{
					$this->_datas['person'][$y]=$xmlData->record[$i];
					$y++;
				}

				// collect the TeamPlayer data
				if ($xmlData->record[$i]['object']=='TeamPlayer')
				{
					$this->_datas['teamplayer'][$v]=$xmlData->record[$i];
					$v++;
				}

				// collect the TeamStaff data
				if ($xmlData->record[$i]['object']=='TeamStaff')
				{
					$this->_datas['teamstaff'][$u]=$xmlData->record[$i];
					$u++;
				}

				// collect the TeamTraining data
				if ($xmlData->record[$i]['object']=='TeamTraining')
				{
					$this->_datas['teamtraining'][$tt]=$xmlData->record[$i];
					$tt++;
				}

				// collect the MatchPlayer data
				if ($xmlData->record[$i]['object']=='MatchPlayer')
				{
					$this->_datas['matchplayer'][$mp]=$xmlData->record[$i];
					$mp++;
				}

				// collect the MatchStaff data
				if ($xmlData->record[$i]['object']=='MatchStaff')
				{
					$this->_datas['matchstaff'][$ms]=$xmlData->record[$i];
					$ms++;
				}

				// collect the MatchReferee data
				if ($xmlData->record[$i]['object']=='MatchReferee')
				{
					$this->_datas['matchreferee'][$mr]=$xmlData->record[$i];
					$mr++;
				}

				// collect the MatchEvent data
				if ($xmlData->record[$i]['object']=='MatchEvent')
				{
					$this->_datas['matchevent'][$me]=$xmlData->record[$i];
					$me++;
				}

				// collect the PositionEventType data
				if ($xmlData->record[$i]['object']=='PositionEventType')
				{
					$this->_datas['positioneventtype'][$pe]=$xmlData->record[$i];
					$pe++;
				}

				// collect the Statistic data
				if ($xmlData->record[$i]['object']=='Statistic')
				{
					$this->_datas['statistic'][$s]=$xmlData->record[$i];
					$s++;
				}

				// collect the PositionStatistic data
				if ($xmlData->record[$i]['object']=='PositionStatistic')
				{
					$this->_datas['positionstatistic'][$ps]=$xmlData->record[$i];
					$ps++;
				}

				// collect the MatchStaffStatistic data
				if ($xmlData->record[$i]['object']=='MatchStaffStatistic')
				{
					$this->_datas['matchstaffstatistic'][$mss]=$xmlData->record[$i];
					$mss++;
				}

				// collect the MatchStatistic data
				if ($xmlData->record[$i]['object']=='MatchStatistic')
				{
					$this->_datas['matchstatistic'][$mst]=$xmlData->record[$i];
					$mst++;
				}

				// collect the Treeto data
				if ($xmlData->record[$i]['object']=='Treeto')
				{
					$this->_datas['treeto'][$tto]=$xmlData->record[$i];
					$tto++;
				}

				// collect the TreetoNode data
				if ($xmlData->record[$i]['object']=='TreetoNode')
				{
					$this->_datas['treetonode'][$ttn]=$xmlData->record[$i];
					$ttn++;
				}

				// collect the TreetoMatch data
				if ($xmlData->record[$i]['object']=='TreetoMatch')
				{
					$this->_datas['treetomatch'][$ttm]=$xmlData->record[$i];
					$ttm++;
				}

				$i++;
			}

			if (isset($this->_datas['teamtool']) && is_array($this->_datas['teamtool']) && count($this->_datas['teamtool']) > 0)
			{
				$i=0;
				$m=0;
				foreach ($xmlData->record as $value)
				{
					if ($xmlData->record[$i]['object']=='TeamTool')
					{
						$this->_datas['projectteam'][$m]=$xmlData->record[$i];
						$m++;
					}
					$i++;
				}
			}

			return $this->_datas;
		}
		else
		{
		    Factory::getApplication()->enqueueMessage(Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR', 'Something is wrong inside the import file'), 'warning');
			return false;
		}
	}

	public function getLeagueList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id,name FROM #__joomleague_league ORDER BY name ASC';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getSportsTypeList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id,name,name AS text FROM #__joomleague_sports_type ORDER BY name ASC';
		$db->setQuery($query);
		$result=$db->loadObjectList();
		foreach ($result as $sportstype){$sportstype->name=Text::_($sportstype->name);}
		return $result;
	}

	public function getSeasonList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id,name FROM #__joomleague_season ORDER BY name';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getUserList($is_admin=false)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__users'));
		$db->setQuery($query);
		$user_ids = $db->loadColumn(0);
		
		$users = array();
		
		foreach ($user_ids AS $user_id) 
		{
			$user = Factory::getUser($user_id);
			
			if ($is_admin) {
				$admin = $user->authorise('core.admin');
				if ($admin) {
					$userArray = array();
					$userArray['id'] = $user->id;
					$userArray['username'] = $user->username;
					$users[] = $userArray;
				}
			} else {
				$userArray = array();
				$userArray['id'] = $user->id;
				$userArray['username'] = $user->username;
				$users[] = $userArray;
			}
		}	

		return $users;
	}

	public function getTemplateList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id,name FROM #__joomleague_project WHERE master_template=0 ORDER BY name ASC';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTeamList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id,name,club_id,short_name,middle_name,info FROM #__joomleague_team ORDER BY name';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTeamListSelect()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query="SELECT id AS value,name,info,club_id,short_name, middle_name FROM #__joomleague_team ORDER BY name";
		$db->setQuery($query);
		if ($results=$db->loadObjectList())
		{
			foreach ($results AS $team)
			{
				$team->text=$team->name.' - ('.$team->info.')';
			}
			return $results;
		}
		return false;
	}

	public function getClubList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id,name,standard_playground,country FROM #__joomleague_club ORDER BY name ASC';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getClubListSelect()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id AS value,name AS text,country,standard_playground FROM #__joomleague_club ORDER BY name';
		$db->setQuery($query);
		if ($results=$db->loadObjectList())
		{
			return $results;
		}
		return false;
	}

	public function getNewClubList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id,name,country FROM #__joomleague_club ORDER BY name ASC';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getNewClubListSelect()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id AS value, name AS text, country FROM #__joomleague_club ORDER BY name';
		$db->setQuery($query);
		if ($results=$db->loadObjectList())
		{
			return $results;
		}
		return false;
	}

	public function getClubAndTeamList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query  = ' SELECT id, c.name AS club_name, t.name AS team_name, c.country'
				. ' FROM #__joomleague_club'
				. ' INNER JOIN #__joomleague_team AS t ON t.club_id=c.id'
				. ' ORDER BY c.name, t.name ASC';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getClubAndTeamListSelect()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query  = ' SELECT t.id AS value, CONCAT(c.name, " - ", t.name , " (", t.info , ")" ) AS text, t.club_id, c.name AS club_name, t.name AS team_name, c.country'
				. ' FROM #__joomleague_club AS c'
				. ' INNER JOIN #__joomleague_team AS t ON t.club_id=c.id'
				. ' ORDER BY c.name, t.name';
		$db->setQuery($query);
		if ($results=$db->loadObjectList())
		{
			return $results;
		}
		return false;
	}

	public function getPersonList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query="	SELECT *,
					LOWER(lastname) AS low_lastname,
					LOWER(firstname) AS low_firstname,
					LOWER(nickname) AS low_nickname
					FROM #__joomleague_person WHERE firstname<>'!Unknown' AND lastname<>'!Player' AND nickname<>'!Ghost' ORDER BY lastname";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getPersonListSelect()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query ="	SELECT id AS value,firstname,lastname,nickname,birthday
						FROM #__joomleague_person
						WHERE firstname<>'!Unknown' AND lastname<>'!Player' AND nickname<>'!Ghost'
						ORDER BY lastname,firstname";
		$db->setQuery($query);
		if ($results=$db->loadObjectList())
		{
			foreach ($results AS $person)
			{
				$textString=$person->lastname.','.$person->firstname;
				if (!empty($person->nickname))
				{
					$textString .= " '".$person->nickname."'";
				}
				if ($person->birthday!='0000-00-00')
				{
					$textString .= " (".$person->birthday.")";
				}
				$person->text=$textString;
			}
			return $results;
		}
		return false;
	}

	public function getPlaygroundList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id,name,short_name,club_id FROM #__joomleague_playground ORDER BY name';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getPlaygroundListSelect()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id AS value,name AS text,short_name,club_id FROM #__joomleague_playground ORDER BY name';
		$db->setQuery($query);
		if ($results=$db->loadObjectList())
		{
			return $results;
		}
		return false;
	}

	public function getEventList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT * FROM #__joomleague_eventtype ORDER BY name';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getEventListSelect()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id AS value,name AS text FROM #__joomleague_eventtype ORDER BY name';
		$db->setQuery($query);
		$result=$db->loadObjectList();
		foreach ($result as $event){$event->text=Text::_($event->text);}
		return $result;
	}

	public function getPositionList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT * FROM #__joomleague_position ORDER BY name';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getPositionListSelect()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id AS value,name AS text FROM #__joomleague_position ORDER BY name';
		$db->setQuery($query);
		$result=$db->loadObjectList();
		foreach ($result as $position){$position->text=Text::_($position->text);}
		return $result;
	}

	public function getParentPositionList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT * FROM #__joomleague_position WHERE parent_id=0 ORDER BY name';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getParentPositionListSelect()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id AS value,name AS text FROM #__joomleague_position WHERE parent_id=0 ORDER BY name';
		$db->setQuery($query);
		$result=$db->loadObjectList();
		foreach ($result as $position){$position->text=Text::_($position->text);}
		return $result;
	}

	public function getStatisticList()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT * FROM #__joomleague_statistic ORDER BY name';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getStatisticListSelect()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id AS value,name AS text,short,class,note FROM #__joomleague_statistic ORDER BY name';
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	// Should be called as the last function in importData() to delete
	private function _deleteImportFile()
	{
		$importFileName=JPATH_SITE.'/tmp/joomleague_import.jlg';
		if (File::exists($importFileName)){File::delete($importFileName);}
		return true;
	}

	/**
	 * _getDataFromObject
	 *
	 * Get data from object
	 *
	 * @param object $obj object where we find the key
	 * @param string $key key what we find in the object
	 *
	 * @access private
	 *
	 * @return void
	 */
	private function _getDataFromObject(&$obj,$key)
	{
		if (is_object($obj))
		{
			$t_array=get_object_vars($obj);

			if (array_key_exists($key,$t_array))
			{
				return $t_array[$key];
			}
			return false;
		}
		return false;
	}

	private function _getPersonFromTeamStaff($teamstaff_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query="	SELECT	ppl.firstname,
					ppl.lastname
				FROM #__joomleague_person as ppl
				INNER JOIN #__joomleague_team_staff AS r on r.person_id=ppl.id
				WHERE r.id=".(int)$teamstaff_id;
		$db->setQuery($query);
		$db->execute();
		if ($db->getAffectedRows())
		{
			$result=$db->loadObject();
			return $result;
		}
		return (object)array("firstname" => "", "lastname" => "");
	}

	private function _getPersonFromTeamPlayer($teamplayer_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query ="	SELECT	ppl.firstname,
					ppl.lastname
				FROM #__joomleague_person as ppl
				INNER JOIN #__joomleague_team_player AS r on r.person_id=ppl.id
				WHERE r.id=".(int)$teamplayer_id;
		$db->setQuery($query);
		$db->execute();
		if ($db->getAffectedRows())
		{
			$result=$db->loadObject();
			return $result;
		}
		return (object)array("firstname" => "", "lastname" => "");
	}

	private function _getPersonFromProjectReferee($project_referee_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query ="	SELECT	ppl.firstname,
					ppl.lastname
				FROM #__joomleague_person as ppl
				INNER JOIN #__joomleague_project_referee AS pr on pr.person_id=ppl.id
				WHERE pr.id=".(int)$project_referee_id;
		$db->setQuery($query);
		$db->execute();
		if ($db->getAffectedRows())
		{
			$result=$db->loadObject();
			return $result;
		}
		return (object)array("firstname" => "", "lastname" => "");
	}

	private function _getPersonName($person_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT lastname,firstname FROM #__joomleague_person WHERE id='.(int)$person_id;
		$db->setQuery($query);
		$db->execute();
		if ($db->getAffectedRows())
		{
			$result=$db->loadObject();
			return $result;
		}
		return (object)array("firstname" => "", "lastname" => "");
	}

	private function _getClubName($club_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT name FROM #__joomleague_club WHERE id='.(int)$club_id;
		$db->setQuery($query);
		$db->execute();
		if ($db->getAffectedRows())
		{
			$result=$db->loadResult();
			return $result;
		}
		return '#Error in _getClubName#';
	}

	private function _getTeamName($team_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='	SELECT t.name
				FROM #__joomleague_team AS t
				INNER JOIN #__joomleague_project_team AS pt on pt.id='.(int)$team_id.' WHERE t.id=pt.team_id';
		$db->setQuery($query);
		$db->execute();
		if ($object=$db->loadObject())
		{
			return $object->name;
		}
		return '#Error in _getTeamName#';
	}

	private function _getTeamName2($team_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT name FROM #__joomleague_team WHERE id='.(int)$team_id;
		$db->setQuery($query);
		$db->execute();
		if ($db->getAffectedRows())
		{
			$result=$db->loadResult();
			return $result;
		}
		return '#Error in _getTeamName2#';
	}

	private function _getPlaygroundRecord($id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT * FROM #__joomleague_playground WHERE id='.(int)$id;
		$db->setQuery($query);
		$db->execute();
		if ($object=$db->loadObject()){return $object;}
		return null;
	}

	private function _updatePlaygroundRecord($club_id,$playground_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='UPDATE #__joomleague_playground SET club_id='.(int)$club_id.' WHERE id='.(int)$playground_id;
		$db->setQuery($query);
		return $db->execute();
	}

	private function _getRoundName($round_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT name FROM #__joomleague_round WHERE id='.(int)$round_id;
		$db->setQuery($query);
		$db->execute();
		if ($db->getAffectedRows())
		{
			$result=$db->loadResult();
			return $result;
		}
		return null;
	}

	private function _getProjectPositionName($project_position_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query  = ' SELECT pos.name'
			. ' FROM #__joomleague_project_position AS ppos'
			. ' INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id'
			. ' WHERE ppos.id='.(int)$project_position_id;
		$db->setQuery($query);
		$db->execute();
		if ($object=$db->loadResult()){return $object;}
		return null;
	}

	/**
	 * _getCountryByOldid
	 *
	 * Get ISO-Code for countries to convert in old jlg import file
	 *
	 * @param object $obj object where we find the key
	 * @param string $key key what we find in the object
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function getCountryByOldid()
	{
		$country['0']='';
		$country['1']='AFG';
		$country['2']='ALB';
		$country['3']='DZA';
		$country['4']='ASM';
		$country['5']='AND';
		$country['6']='AGO';
		$country['7']='AIA';
		$country['8']='ATA';
		$country['9']='ATG';
		$country['10']='ARG';
		$country['11']='ARM';
		$country['12']='ABW';
		$country['13']='AUS';
		$country['14']='AUT';
		$country['15']='AZE';
		$country['16']='BHS';
		$country['17']='BHR';
		$country['18']='BGD';
		$country['19']='BRB';
		$country['20']='BLR';
		$country['21']='BEL';
		$country['22']='BLZ';
		$country['23']='BEN';
		$country['24']='BMU';
		$country['25']='BTN';
		$country['26']='BOL';
		$country['27']='BIH';
		$country['28']='BWA';
		$country['29']='BVT';
		$country['30']='BRA';
		$country['31']='IOT';
		$country['32']='BRN';
		$country['33']='BGR';
		$country['34']='BFA';
		$country['35']='BDI';
		$country['36']='KHM';
		$country['37']='CMR';
		$country['38']='CAN';
		$country['39']='CPV';
		$country['40']='CYM';
		$country['41']='CAF';
		$country['42']='TCD';
		$country['43']='CHL';
		$country['44']='CHN';
		$country['45']='CXR';
		$country['46']='CCK';
		$country['47']='COL';
		$country['48']='COM';
		$country['49']='COG';
		$country['50']='COK';
		$country['51']='CRI';
		$country['52']='CIV';
		$country['53']='HRV';
		$country['54']='CUB';
		$country['55']='CYP';
		$country['56']='CZE';
		$country['57']='DNK';
		$country['58']='DJI';
		$country['59']='DMA';
		$country['60']='DOM';
		$country['61']='TMP';
		$country['62']='ECU';
		$country['63']='EGY';
		$country['64']='SLV';
		$country['65']='GNQ';
		$country['66']='ERI';
		$country['67']='EST';
		$country['68']='ETH';
		$country['69']='FLK';
		$country['70']='FRO';
		$country['71']='FJI';
		$country['72']='FIN';
		$country['73']='FRA';
		$country['74']='FXX';
		$country['75']='GUF';
		$country['76']='PYF';
		$country['77']='ATF';
		$country['78']='GAB';
		$country['79']='GMB';
		$country['80']='GEO';
		$country['81']='DEU';
		$country['82']='GHA';
		$country['83']='GIB';
		$country['84']='GRC';
		$country['85']='GRL';
		$country['86']='GRD';
		$country['87']='GLP';
		$country['88']='GUM';
		$country['89']='GTM';
		$country['90']='GIN';
		$country['91']='GNB';
		$country['92']='GUY';
		$country['93']='HTI';
		$country['94']='HMD';
		$country['95']='HND';
		$country['96']='HKG';
		$country['97']='HUN';
		$country['98']='ISL';
		$country['99']='IND';
		$country['100']='IDN';
		$country['101']='IRN';
		$country['102']='IRQ';
		$country['103']='IRL';
		$country['104']='ISR';
		$country['105']='ITA';
		$country['106']='JAM';
		$country['107']='JPN';
		$country['108']='JOR';
		$country['109']='KAZ';
		$country['110']='KEN';
		$country['111']='KIR';
		$country['112']='PRK';
		$country['113']='KOR';
		$country['114']='KWT';
		$country['115']='KGZ';
		$country['116']='LAO';
		$country['117']='LVA';
		$country['118']='LBN';
		$country['119']='LSO';
		$country['120']='LBR';
		$country['121']='LBY';
		$country['122']='LIE';
		$country['123']='LTU';
		$country['124']='LUX';
		$country['125']='MAC';
		$country['126']='MKD';
		$country['127']='MDG';
		$country['128']='MWI';
		$country['129']='MYS';
		$country['130']='MDV';
		$country['131']='MLI';
		$country['132']='MLT';
		$country['133']='MHL';
		$country['134']='MTQ';
		$country['135']='MRT';
		$country['136']='MUS';
		$country['137']='MYT';
		$country['138']='MEX';
		$country['139']='FSM';
		$country['140']='MDA';
		$country['141']='MCO';
		$country['142']='MNG';
		$country['143']='MSR';
		$country['144']='MAR';
		$country['145']='MOZ';
		$country['146']='MMR';
		$country['147']='NAM';
		$country['148']='NRU';
		$country['149']='NPL';
		$country['150']='NLD';
		$country['151']='ANT';
		$country['152']='NCL';
		$country['153']='NZL';
		$country['154']='NIC';
		$country['155']='NER';
		$country['156']='NGA';
		$country['157']='NIU';
		$country['158']='NFK';
		$country['159']='MNP';
		$country['160']='NOR';
		$country['161']='OMN';
		$country['162']='PAK';
		$country['163']='PLW';
		$country['164']='PAN';
		$country['165']='PNG';
		$country['166']='PRY';
		$country['167']='PER';
		$country['168']='PHL';
		$country['169']='PCN';
		$country['170']='POL';
		$country['171']='PRT';
		$country['172']='PRI';
		$country['173']='QAT';
		$country['174']='REU';
		$country['175']='ROM';
		$country['176']='RUS';
		$country['177']='RWA';
		$country['178']='KNA';
		$country['179']='LCA';
		$country['180']='VCT';
		$country['181']='WSM';
		$country['182']='SMR';
		$country['183']='STP';
		$country['184']='SAU';
		$country['185']='SEN';
		$country['186']='SYC';
		$country['187']='SLE';
		$country['188']='SGP';
		$country['189']='SVK';
		$country['190']='SVN';
		$country['191']='SLB';
		$country['192']='SOM';
		$country['193']='ZAF';
		$country['194']='SGS';
		$country['195']='ESP';
		$country['196']='LKA';
		$country['197']='SHN';
		$country['198']='SPM';
		$country['199']='SDN';
		$country['200']='SUR';
		$country['201']='SJM';
		$country['202']='SWZ';
		$country['203']='SWE';
		$country['204']='CHE';
		$country['205']='SYR';
		$country['206']='TWN';
		$country['207']='TJK';
		$country['208']='TZA';
		$country['209']='THA';
		$country['210']='TGO';
		$country['211']='TKL';
		$country['212']='TON';
		$country['213']='TTO';
		$country['214']='TUN';
		$country['215']='TUR';
		$country['216']='TKM';
		$country['217']='TCA';
		$country['218']='TUV';
		$country['219']='UGA';
		$country['220']='UKR';
		$country['221']='ARE';
		$country['222']='GBR';
		$country['223']='USA';
		$country['224']='UMI';
		$country['225']='URY';
		$country['226']='UZB';
		$country['227']='VUT';
		$country['228']='VAT';
		$country['229']='VEN';
		$country['230']='VNM';
		$country['231']='VGB';
		$country['232']='VIR';
		$country['233']='WLF';
		$country['234']='ESH';
		$country['235']='YEM';
		$country['238']='ZMB';
		$country['239']='ZWE';
		$country['240']='ENG';
		$country['241']='SCO';
		$country['242']='WAL';
		$country['243']='ALA';
		$country['244']='NEI';
		$country['245']='MNE';
		$country['246']='SRB';
		return $country;
	}

	private function _checkProject()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		/*
		TO BE FIXED again
		$query="	SELECT id
					FROM #__joomleague_project
					WHERE name='$this->_name' AND league_id='$this->_league_id' AND season_id='$this->_season_id'";
		*/
		$query="SELECT id FROM #__joomleague_project WHERE name='".addslashes(stripslashes($this->_name))."'";
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows() > 0){return false;}
		return true;
	}

	private function _getObjectName($tableName, $id=0, $usedFieldName='')
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if($id>0) {
			$fieldName=($usedFieldName=='') ? 'name' : $usedFieldName;
			$query="SELECT $fieldName FROM #__joomleague_$tableName WHERE id=$id";
			$db->setQuery($query);
			if ($result=$db->loadResult()) {
				return $result;
			}
			return Text::sprintf('Item with ID [%1$s] not found inside [#__joomleague_%2$s]',$id,$tableName);
		}
	}

	private function _importSportsType()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!empty($this->_sportstype_new))
		{
			$query="SELECT id FROM #__joomleague_sports_type WHERE name='".addslashes(stripslashes($this->_sportstype_new))."'";
			$db->setQuery($query);
			if ($sportstypeObject=$db->loadObject())
			{
				$this->_sportstype_id=$sportstypeObject->id;
				$my_text .= '<span style="color:orange">';
				$my_text .= Text::sprintf('Using existing sportstype data: %1$s',"</span><strong>$this->_sportstype_new</strong>");
				$my_text .= '<br />';
			}
			else
			{
				$p_sportstype = $this->getTable('SportsType');
				$p_sportstype->set('name',trim($this->_sportstype_new));

				if ($p_sportstype->store()===false)
				{
					$my_text .= '<span style="color:'.$this->storeFailedColor.'"><strong>';
					$my_text .= Text::_('Error in function _importSportsType').'</strong></span><br />';
					$my_text .= Text::sprintf('Sportstypename: %1$s',Text::_($this->_sportstype_new)).'<br />';
					$my_text .= Text::sprintf('Error-Text #%1$s#',$db->getErrorMsg()).'<br />';
					$my_text .= '<pre>'.print_r($p_sportstype,true).'</pre>';
					$this->_success_text['Importing sportstype data:']=$my_text;
					return false;
				}
				else
				{
					$insertID=$p_sportstype->id; 
					$this->_sportstype_id=$insertID;
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf('Created new sportstype data: %1$s',"</span><strong>$this->_sportstype_new</strong>");
					$my_text .= '<br />';
				}
			}
		}
		else
		{
			$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
			$my_text .= Text::sprintf(	'Using existing sportstype data: %1$s',
										'</span><strong>'.Text::_($this->_getObjectName('sports_type',$this->_sportstype_id)).'</strong>');
			$my_text .= '<br />';
		}
		$this->_success_text['Importing sportstype data:']=$my_text;
		return true;
	}

	private function _importLeague()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!empty($this->_league_new))
		{
			$query="SELECT id FROM #__joomleague_league WHERE name='".addslashes(stripslashes($this->_league_new))."'";
			$db->setQuery($query);

			if ($leagueObject=$db->loadObject())
			{
				$this->_league_id=$leagueObject->id;
				$my_text .= '<span style="color:orange">';
				$my_text .= Text::sprintf('Using existing league data: %1$s',"</span><strong>$this->_league_new</strong>");
				$my_text .= '<br />';
			}
			else
			{
				$p_league = $this->getTable('League');
				$p_league->set('name',trim($this->_league_new));
				$p_league->set('alias',OutputFilter::stringURLSafe($this->_league_new));
				//$p_league->set('country',$this->_league_new_country);

				if ($p_league->store()===false)
				{
					$my_text .= '<span style="color:'.$this->storeFailedColor.'"><strong>';
					$my_text .= Text::_('Error in function _importLeague').'</strong></span><br />';
					$my_text .= Text::sprintf('Leaguenname: %1$s',$this->_league_new).'<br />';
					$my_text .= Text::sprintf('Error-Text #%1$s#',$db->getErrorMsg()).'<br />';
					$my_text .= '<pre>'.print_r($p_league,true).'</pre>';
					$this->_success_text['Importing league data:']=$my_text;
					return false;
				}
				else
				{
					$insertID=$p_league->id; 
					$this->_league_id=$insertID;
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf('Created new league data: %1$s',"</span><strong>$this->_league_new</strong>");
					$my_text .= '<br />';
				}
			}
		}
		else
		{
			$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
			$my_text .= Text::sprintf(	'Using existing league data: %1$s',
										'</span><strong>'.$this->_getObjectName('league',$this->_league_id).'</strong>');
			$my_text .= '<br />';
		}
		$this->_success_text['Importing league data:']=$my_text;
		return true;
	}

	private function _importSeason()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!empty($this->_season_new))
		{
			$query="SELECT id FROM #__joomleague_season WHERE name='".addslashes(stripslashes($this->_season_new))."'";
			$db->setQuery($query);

			if ($seasonObject=$db->loadObject())
			{
				$this->_season_id=$seasonObject->id;
				$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
				$my_text .= Text::sprintf('Using existing season data: %1$s',"</span><strong>$this->_season_new</strong>");
				$my_text .= '<br />';
			}
			else
			{
				$p_season = $this->getTable('Season');
				$p_season->set('name',trim($this->_season_new));
				$p_season->set('alias',OutputFilter::stringURLSafe($this->_season_new));

				if ($p_season->store()===false)
				{
					$my_text .= '<span style="color:'.$this->storeFailedColor.'"><strong>';
					$my_text .= Text::_('Error in function _importSeason').'</strong></span><br />';
					$my_text .= Text::sprintf('Seasonname: %1$s',$this->_season_new).'<br />';
					$my_text .= Text::sprintf('Error-Text #%1$s#',$db->getErrorMsg()).'<br />';
					$my_text .= '<pre>'.print_r($p_season,true).'</pre>';
					$this->_success_text['Importing season data:']=$my_text;
					return false;
				}
				else
				{
					$insertID=$p_season->id; 
					$this->_season_id=$insertID;
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf('Created new season data: %1$s',"</span><strong>$this->_season_new</strong>");
					$my_text .= '<br />';
				}
			}
		}
		else
		{
			$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
			$my_text .= Text::sprintf(	'Using existing season data: %1$s',
										'</span><strong>'.$this->_getObjectName('season',$this->_season_id).'</strong>');
			$my_text .= '<br />';
		}
		$this->_success_text['Importing season data:']=$my_text;
		return true;
	}

	private function _importEvents()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['event']) || count($this->_datas['event'])==0){return true;}
		if ((!isset($this->_neweventsid) || count($this->_neweventsid)==0) &&
			(!isset($dbeventsid) || count($dbeventsid)==0)){return true;}
		if (!empty($dbeventsid))
		{
			foreach ($dbeventsid AS $key => $id)
			{
				if(empty($id)) continue;
				$oldID=$this->_getDataFromObject($this->_datas['event'][$key],'id');
				$this->_convertEventID[$oldID]=$id;
				$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
				$my_text .= Text::sprintf(	'Using existing event data: %1$s',
											'</span><strong>'.Text::_($this->_getObjectName('eventtype',$id)).'</strong>');
				$my_text .= '<br />';
			}
		}

		if (!empty($this->_neweventsid))
		{
			foreach ($this->_neweventsid AS $key => $id)
			{
				if(!isset($this->_neweventsname[$key])) continue;
 				$p_eventtype = $this->getTable('Eventtype');
				$import_event=$this->_datas['event'][$key];
				$oldID=$this->_getDataFromObject($import_event,'id');
				$alias=$this->_getDataFromObject($import_event,'alias');
				$p_eventtype->set('name',trim($this->_neweventsname[$key]));
				$p_eventtype->set('icon',$this->_getDataFromObject($import_event,'icon'));
				$p_eventtype->set('parent',$this->_getDataFromObject($import_event,'parent'));
				$p_eventtype->set('splitt',$this->_getDataFromObject($import_event,'splitt'));
				$p_eventtype->set('direction',$this->_getDataFromObject($import_event,'direction'));
				$p_eventtype->set('double',$this->_getDataFromObject($import_event,'double'));
				$p_eventtype->set('sports_type_id',$this->_sportstype_id);
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_eventtype->set('alias',$alias);
				}
				else
				{
				    $p_eventtype->set('alias',OutputFilter::stringURLSafe($this->_getDataFromObject($p_eventtype,'name')));
				}
				$query="SELECT id,name FROM #__joomleague_eventtype WHERE name='".addslashes(stripslashes($p_eventtype->name))."'";
				$db->setQuery($query);
				$db->execute();
				if ($object=$db->loadObject())
				{
					$this->_convertEventID[$oldID]=$object->id;
					$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
					$my_text .= Text::sprintf('Using existing eventtype data: %1$s','</span><strong>'.Text::_($object->name).'</strong>');
					$my_text .= '<br />';
				}
				else
				{
					if ($p_eventtype->store()===false)
					{
						$my_text .= 'error on event import: ';
						$my_text .= $oldID;
						$my_text .= "<br />Error: _importEvents<br />#$my_text#<br />#<pre>".print_r($p_eventtype,true).'</pre>#';
						$this->_success_text['Importing general event data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$p_eventtype->id; 
						$this->_convertEventID[$oldID]=$insertID;
						$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
						$my_text .= Text::sprintf('Created new eventtype data: %1$s','</span><strong>'.Text::_($p_eventtype->name).'</strong>');
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['Importing general event data:']=$my_text;
		return true;
	}

	private function _importStatistics()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['statistic']) || count($this->_datas['statistic'])==0){return true;}
		if ((!isset($this->_newstatisticsid) || count($this->_newstatisticsid)==0) &&
			(!isset($dbstatisticsid) || count($dbstatisticsid)==0)){return true;}

		if (!empty($dbstatisticsid))
		{
			foreach ($dbstatisticsid AS $key => $id)
			{
				$oldID=$this->_getDataFromObject($this->_datas['statistic'][$key],'id');
				$this->_convertStatisticID[$oldID]=$id;
				$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
				$my_text .= Text::sprintf(	'Using existing statistic data: %1$s',
											'</span><strong>'.Text::_($this->_getObjectName('statistic',$id)).'</strong>');
				$my_text .= '<br />';
			}
		}

		if (!empty($this->_newstatisticsid))
		{
			foreach ($this->_newstatisticsid AS $key => $id)
			{
				if(!isset($this->_newstatisticsname[$key])) continue;
				$p_statistic =  $this->getTable('Statistic');
				$import_statistic=$this->_datas['statistic'][$key];
				$oldID=$this->_getDataFromObject($import_statistic,'id');
				$alias=$this->_getDataFromObject($import_statistic,'alias');
				$p_statistic->set('name',trim($this->_newstatisticsname[$key]));
				$p_statistic->set('short',$this->_getDataFromObject($import_statistic,'short'));
				$p_statistic->set('icon',$this->_getDataFromObject($import_statistic,'icon'));
				$p_statistic->set('class',$this->_getDataFromObject($import_statistic,'class'));
				$p_statistic->set('calculated',$this->_getDataFromObject($import_statistic,'calculated'));
				$p_statistic->set('params',$this->_getDataFromObject($import_statistic,'params'));
				$p_statistic->set('baseparams',$this->_getDataFromObject($import_statistic,'baseparams'));
				$p_statistic->set('note',$this->_getDataFromObject($import_statistic,'note'));
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_statistic->set('alias',$alias);
				}
				else
				{
				    $p_statistic->set('alias',OutputFilter::stringURLSafe($this->_getDataFromObject($p_statistic,'name')));
				}
				$query="SELECT * FROM #__joomleague_statistic WHERE name='".addslashes(stripslashes($p_statistic->name))."' AND class='".addslashes(stripslashes($p_statistic->class))."'";
				$db->setQuery($query);
				$db->execute();
				if ($object=$db->loadObject())
				{
					$this->_convertStatisticID[$oldID]=$object->id;
					$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
					$my_text .= Text::sprintf('Using existing statistic data: %1$s','</span><strong>'.Text::_($object->name).'</strong>');
					$my_text .= '<br />';
				}
				else
				{
					if ($p_statistic->store()===false)
					{
						$my_text .= 'error on statistic import: ';
						$my_text .= $oldID;
						$my_text .= "<br />Error: _importStatistics<br />#$my_text#<br />#<pre>".print_r($p_statistic,true).'</pre>#';
						$this->_success_text['Importing general statistic data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$p_statistic->id; 
						$this->_convertStatisticID[$oldID]=$insertID;
						$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
						$my_text .= Text::sprintf('Created new statistic data: %1$s','</span><strong>'.Text::_($p_statistic->name).'</strong>');
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['Importing statistic data:']=$my_text;
		return true;
	}

	private function _importParentPositions()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['parentposition']) || count($this->_datas['parentposition'])==0){return true;}
		if ((!isset($this->_newparentpositionsid) || count($this->_newparentpositionsid)==0) &&
			(!isset($dbparentpositionsid) || count($dbparentpositionsid)==0)){return true;}
		if (!empty($dbparentpositionsid))
		{
			foreach ($dbparentpositionsid AS $key => $id)
			{
				if(empty($id)) continue;
				$oldID=$this->_getDataFromObject($this->_datas['parentposition'][$key],'id');
				$this->_convertParentPositionID[$oldID]=$id;
				$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
				$my_text .= Text::sprintf(	'Using existing parentposition data: %1$s',
											'</span><strong>'.Text::_($this->_getObjectName('position',$id)).'</strong>');
				$my_text .= '<br />';
			}
		}

		if (!empty($this->_newparentpositionsid))
		{
			foreach ($this->_newparentpositionsid AS $key => $id)
			{
				if(!isset($this->_newparentpositionsname[$key])) continue;
				$p_position =  $this->getTable('Position');
				$import_position=$this->_datas['parentposition'][$key];
				$oldID=$this->_getDataFromObject($import_position,'id');
				$alias=$this->_getDataFromObject($import_position,'alias');					
				$p_position->set('name',trim($this->_newparentpositionsname[$key]));
				$p_position->set('parent_id',0);
				$p_position->set('persontype',$this->_getDataFromObject($import_position,'persontype'));
				$p_position->set('sports_type_id',$this->_sportstype_id);
				$p_position->set('published',1);
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_position->set('alias',$alias);
				}
				else
				{
					$p_position->set('alias',OutputFilter::stringURLSafe($this->_getDataFromObject($p_position,'name')));
				}
				$query="SELECT id,name FROM #__joomleague_position WHERE name='".addslashes(stripslashes($p_position->name))."' AND parent_id=0";
				$db->setQuery($query);
				$db->execute();
				if ($db->getAffectedRows())
				{
					$p_position->load($db->loadResult());
					$this->_convertParentPositionID[$oldID]=$p_position->id;
					$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
					$my_text .= Text::sprintf('Using existing parent-position data: %1$s','</span><strong>'.Text::_($p_position->name).'</strong>');
					$my_text .= '<br />';
				}
				else
				{
					if ($p_position->store()===false)
					{
						$my_text .= 'error on parent-position import: ';
						$my_text .= $oldID;
						$my_text .= "<br />Error: _importParentPositions<br />#$my_text#<br />#<pre>".print_r($p_position,true).'</pre>#';
						$this->_success_text['Importing general parent-position data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$p_position->id; 
						$this->_convertParentPositionID[$oldID]=$insertID;
						$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
						$my_text .= Text::sprintf('Created new parent-position data: %1$s','</span><strong>'.Text::_($p_position->name).'</strong>');
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['Importing general parent-position data:']=$my_text;
		return true;
	}

	private function _importPositions()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['position']) || count($this->_datas['position'])==0){return true;}
		if ((!isset($this->_newpositionsid) || count($this->_newpositionsid)==0) &&
			(!isset($dbpositionsid) || count($dbpositionsid)==0)){return true;}

		if (!empty($dbpositionsid))
		{
			foreach ($dbpositionsid AS $key => $id)
			{
				if(empty($id)) continue;
				$oldID=$this->_getDataFromObject($this->_datas['position'][$key],'id');
				$this->_convertPositionID[$oldID]=$id;
				$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
				$my_text .= Text::sprintf(	'Using existing position data: %1$s',
											'</span><strong>'.Text::_($this->_getObjectName('position',$id)).'</strong>');
				$my_text .= '<br />';
			}
		}

		if (!empty($this->_newpositionsid))
		{
			foreach ($this->_newpositionsid AS $key => $id)
			{
				if(!isset($this->_newpositionsname[$key])) continue;
				$p_position =  $this->getTable('Position');
				$import_position=$this->_datas['position'][$key];
				$oldID=$this->_getDataFromObject($import_position,'id');
				$alias=$this->_getDataFromObject($import_position,'alias');
				$p_position->set('name',trim($this->_newpositionsname[$key]));
				$oldParentPositionID=$this->_getDataFromObject($import_position,'parent_id');
				if (isset($this->_convertPositionID[$oldParentPositionID]))
				{
					$p_position->set('parent_id',$this->_convertPositionID[$oldParentPositionID]);
				} else {
					$p_position->set('parent_id', 0);
				}
				//$p_position->set('parent_id',$this->_convertParentPositionID[(int)$this->_getDataFromObject($import_position,'parent_id')]);
				$p_position->set('persontype',$this->_getDataFromObject($import_position,'persontype'));
				$p_position->set('sports_type_id',$this->_sportstype_id);
				$p_position->set('published',1);
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_position->set('alias',$alias);
				}
				else
				{
					$p_position->set('alias',OutputFilter::stringURLSafe($this->_getDataFromObject($p_position,'name')));
				}
				$query="SELECT id,name FROM #__joomleague_position WHERE name='".addslashes(stripslashes($p_position->name))."' AND parent_id=$p_position->parent_id";
				$db->setQuery($query);
				$db->execute();
				if ($db->getAffectedRows())
				{
					$p_position->load($db->loadResult());
					// Prevent showing of using existing position twice (see the foreach $dbpositionsid loop)
					if (isset($this->_convertPositionID[$oldID]) && $this->_convertPositionID[$oldID]!=$p_position->id)
					{
						$this->_convertPositionID[$oldID]=$p_position->id;
						$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
						$my_text .= Text::sprintf('Using existing position data: %1$s','</span><strong>'.Text::_($p_position->name).'</strong>');
						$my_text .= '<br />';
					}
				}
				else
				{
					if ($p_position->store()===false)
					{
						$my_text .= 'error on position import: ';
						$my_text .= $oldID;
						$my_text .= "<br />Error: _importPositions<br />#$my_text#<br />#<pre>".print_r($p_position,true).'</pre>#';
						$this->_success_text['Importing general position data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$p_position->id; 
						$this->_convertPositionID[$oldID]=$insertID;
						$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
						$my_text .= Text::sprintf('Created new position data: %1$s','</span><strong>'.Text::_($p_position->name).'</strong>');
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['Importing general position data:']=$my_text;
		return true;
	}

	private function _importPositionEventType()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['positioneventtype']) || count($this->_datas['positioneventtype'])==0){return true;}

		if (!isset($this->_datas['event']) || count($this->_datas['event'])==0){return true;}
		if ((!isset($this->_neweventsid) || count($this->_neweventsid)==0) &&
			(!isset($dbeventsid) || count($dbeventsid)==0)){return true;}
		if (!isset($this->_datas['position']) || count($this->_datas['position'])==0){return true;}
		if ((!isset($this->_newpositionsid) || count($this->_newpositionsid)==0) &&
			(!isset($dbpositionsid) || count($dbpositionsid)==0)){return true;}

		foreach ($this->_datas['positioneventtype'] as $key => $positioneventtype)
		{
			$import_positioneventtype=$this->_datas['positioneventtype'][$key];
			$oldID=$this->_getDataFromObject($import_positioneventtype,'id');
			$p_positioneventtype = $this->getTable('PositionEventtype');
			$oldEventID=$this->_getDataFromObject($import_positioneventtype,'eventtype_id');
			$oldPositionID=$this->_getDataFromObject($import_positioneventtype,'position_id');
			if (!isset($this->_convertEventID[$oldEventID]) ||
				!isset($this->_convertPositionID[$oldPositionID]))
			{
				$my_text .= '<span style="color:red">';
				$my_text .= Text::sprintf(	'Skipping import of PositionEventtype-ID %1$s. Old-EventID: %2$s - Old-PositionID: %3$s',
								"</span><strong>$oldID</strong><span style='color:red'>",
								"</span><strong>$oldEventID</strong><span style='color:red'>",
								"</span><strong>$oldPositionID</strong>").'<br />';
				continue;
			}
			$p_positioneventtype->set('position_id',$this->_convertPositionID[$oldPositionID]);
			$p_positioneventtype->set('eventtype_id',$this->_convertEventID[$oldEventID]);
			$query ="SELECT id
							FROM #__joomleague_position_eventtype
							WHERE	position_id='$p_positioneventtype->position_id' AND
									eventtype_id='$p_positioneventtype->eventtype_id'";
			$db->setQuery($query);
			$db->execute();
			if ($object=$db->loadObject())
			{
				$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
				$my_text .= Text::sprintf(	'Using existing positioneventtype data - Position: %1$s - Event: %2$s',
								'</span><strong>'.Text::_($this->_getObjectName('position',$p_positioneventtype->position_id)).'</strong><span style="color:'.$this->existingInDbColor.'">',
								'</span><strong>'.Text::_($this->_getObjectName('eventtype',$p_positioneventtype->eventtype_id)).'</strong>');
				$my_text .= '<br />';
			}
			else
			{
				if ($p_positioneventtype->store()===false)
				{
					$my_text .= 'error on PositionEventType import: ';
					$my_text .= '#'.$oldID.'#';
					$my_text .= "<br />Error: _importPositionEventType<br />#$my_text#<br />#<pre>".print_r($p_positioneventtype,true).'</pre>#';
					$this->_success_text['Importing positioneventtype data:']=$my_text;
					return false;
				}
				else
				{
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf(	'Created new positioneventtype data. Position: %1$s - Event: %2$s',
									'</span><strong>'.Text::_($this->_getObjectName('position',$p_positioneventtype->position_id)).'</strong><span style="color:'.$this->storeSuccessColor.'">',
									'</span><strong>'.Text::_($this->_getObjectName('eventtype',$p_positioneventtype->eventtype_id)).'</strong>');
					$my_text .= '<br />';
				}
			}
		}
		$this->_success_text['Importing positioneventtype data:']=$my_text;
		return true;
	}

	private function _importPlayground()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['playground']) || count($this->_datas['playground'])==0){return true;}
		if ((!isset($this->_newplaygroundid) || count($this->_newplaygroundid)==0) &&
			(!isset($dbplaygroundsid) || count($dbplaygroundsid)==0)){return true;}

		if (!empty($dbplaygroundsid))
		{
			foreach ($dbplaygroundsid AS $key => $id)
			{
				if(empty($id)) continue;
				$oldID=$this->_getDataFromObject($this->_datas['playground'][$key],'id');
				$this->_convertPlaygroundID[$oldID]=$id;
				$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
				$my_text .= Text::sprintf(	'Using existing playground data: %1$s',
											'</span><strong>'.$this->_getObjectName('playground',$id).'</strong>');
				$my_text .= '<br />';
			}
		}
		if (!empty($this->_newplaygroundid))
		{
			foreach ($this->_newplaygroundid AS $key => $id)
			{
				if(!isset($this->_newplaygroundname[$key])) continue;
				$p_playground =  $this->getTable('Playground');
				$import_playground=$this->_datas['playground'][$key];
				$oldID=$this->_getDataFromObject($import_playground,'id');
				$alias=$this->_getDataFromObject($import_playground,'alias');
				$p_playground->set('name',trim($this->_newplaygroundname[$key]));
				$p_playground->set('short_name',$this->_newplaygroundshort[$key]);
				$p_playground->set('address',$this->_getDataFromObject($import_playground,'address'));
				$p_playground->set('zipcode',$this->_getDataFromObject($import_playground,'zipcode'));
				$p_playground->set('city',$this->_getDataFromObject($import_playground,'city'));
				$p_playground->set('country',$this->_getDataFromObject($import_playground,'country'));
				$p_playground->set('max_visitors',$this->_getDataFromObject($import_playground,'max_visitors'));
				$p_playground->set('website',$this->_getDataFromObject($import_playground,'website'));
				$p_playground->set('picture',$this->_getDataFromObject($import_playground,'picture'));
				$p_playground->set('notes',$this->_getDataFromObject($import_playground,'notes'));
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_playground->set('alias',$alias);
				}
				else
				{
					$p_playground->set('alias',OutputFilter::stringURLSafe($this->_getDataFromObject($p_playground,'name')));
				}
				if (array_key_exists((int)$this->_getDataFromObject($import_playground,'country'),$this->_convertCountryID))
				{
					$p_playground->set('country',(int)$this->_convertCountryID[(int)$this->_getDataFromObject($import_playground,'country')]);
				}
				else
				{
					$p_playground->set('country',$this->_getDataFromObject($import_playground,'country'));
				}
				if ($this->_importType!='playgrounds')	// force club_id to be set to default if only playgrounds are imported
				{
					//if (!isset($this->_getDataFromObject($import_playground,'club_id')))
					{
						$p_playground->set('club_id',$this->_getDataFromObject($import_playground,'club_id'));
					}
				}
				$query="SELECT id,name FROM #__joomleague_playground WHERE name='".addslashes(stripslashes($p_playground->name))."'";
				$db->setQuery($query);
				$db->execute();
				if ($object=$db->loadObject())
				{
					$this->_convertPlaygroundID[$oldID]=$object->id;
					$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
					$my_text .= Text::sprintf('Using existing playground data: %1$s',"</span><strong>$object->name</strong>");
					$my_text .= '<br />';
				}
				else
				{
					if ($p_playground->store()===false)
					{
						$my_text .= '<span style="color:'.$this->storeFailedColor.'"><strong>';
						$my_text .= Text::_('Error in function _importPlayground').'</strong></span><br />';
						$my_text .= Text::sprintf('Playgroundname: %1$s',$p_playground->name).'<br />';
						$my_text .= Text::sprintf('Error-Text #%1$s#',$db->getErrorMsg()).'<br />';
						$my_text .= '<pre>'.print_r($p_playground,true).'</pre>';
						$this->_success_text['Importing general playground data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$p_playground->id; 
						$this->_convertPlaygroundID[$oldID]=$insertID;
						$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
						$my_text .= Text::sprintf('Created new playground data: %1$s',"</span><strong>$p_playground->name</strong>");
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['Importing general playground data:']=$my_text;
		return true;
	}

	private function _importClubs()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		// $this->_datas['club'] : array of all clubs obtained from the xml import file
		// $this->_newclubsid    : array of club ids (xml values) for the new clubs to be created in the database
		// $dbclubsid     : array of club ids (db values) for the existing clubs to be used from the database
		//                         (value of 0 means that the club does not exist in the database)
		if (!isset($this->_datas['club']) || count($this->_datas['club'])==0){return true;}
		if ((!isset($this->_newclubsid) || count($this->_newclubsid)==0) &&
			(!isset($dbclubsid) || count($dbclubsid)==0)){return true;}



		if (!empty($dbclubsid))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query='SELECT id,name,standard_playground,country FROM #__joomleague_club GROUP BY id';
			$db->setQuery($query);
			$dbClubs = $db->loadObjectList('id');

			foreach ($dbclubsid AS $key => $id)
			{
				if (empty($this->_newclubs[$key]) && isset($this->_datas['club'][$key]))
				{
					$oldID=$this->_getDataFromObject($this->_datas['club'][$key],'id');
					$this->_convertClubID[$oldID]=$id;
					$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
					$my_text .= Text::sprintf(	'Using existing club data: %1$s - %2$s',
												'</span><strong>'.$dbClubs[$id]->name.'</strong>',
												''.$dbClubs[$id]->id.''
												);
					$my_text .= '<br />';

					// diddipoeler
					// update clubdata
					$p_club = $this->getTable('Club');
					$p_club->set('id',$id);
					$p_club->set('address', (string) $this->_datas['club'][$key]->address ); 
					$p_club->set('zipcode',(string) $this->_datas['club'][$key]->zipcode); 
					$p_club->set('location',(string) $this->_datas['club'][$key]->location); 
					$p_club->set('state',(string) $this->_datas['club'][$key]->state); 
					$p_club->set('country',(string) $this->_datas['club'][$key]->country); 
					$p_club->set('founded',(string) $this->_datas['club'][$key]->founded); 
					$p_club->set('phone',(string) $this->_datas['club'][$key]->phone); 
					$p_club->set('fax',(string) $this->_datas['club'][$key]->fax); 
					$p_club->set('email',(string) $this->_datas['club'][$key]->email); 
					$p_club->set('website',(string) $this->_datas['club'][$key]->website); 
					$p_club->set('president',(string) $this->_datas['club'][$key]->president); 
					$p_club->set('manager',(string) $this->_datas['club'][$key]->manager); 
					$p_club->set('logo_big',(string) $this->_datas['club'][$key]->logo_big); 
					$p_club->set('logo_middle',(string) $this->_datas['club'][$key]->logo_middle); 
					$p_club->set('logo_small',(string) $this->_datas['club'][$key]->logo_small);
					$p_club->set('asset_id', 0);
					if ($p_club->store()===false)
					{
						$my_text .= '<span style="color:'.$this->storeFailedColor.'"><strong>';
						$my_text .= Text::_('1.a) Error in function updateClubs').'</strong></span><br />';
						$my_text .= Text::sprintf('Clubname: %1$s',$dbClubs[$id]->name).'<br />';
						$my_text .= Text::sprintf('Error-Text #%1$s#',$db->getErrorMsg()).'<br />';
						$my_text .= '<pre>'.print_r($p_club,true).'</pre>';
						$this->_success_text['Importing general club data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$p_club->id; 
						$this->_convertClubID[$oldID]=$insertID;
						$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
						$my_text .= Text::sprintf(	'Updated club data: %1$s - %2$s',
													"</span><strong>$p_club->name</strong>",
													"".$p_club->id.""
													);
						$my_text .= '<br />';
					}


				}
			}
		}
//To Be fixed: Falls Verein neu angelegt wird, muss auch das Team neu angelegt werden.
		if (!empty($this->_newclubsid))
		{
			foreach ($this->_newclubsid AS $key => $id)
			{
				$p_club = $this->getTable('Club');
				foreach ($this->_datas['club'] AS $dClub)
				{
					if ($dClub->id == $id)
					{
						$import_club=$dClub;
						break;
					}
				}
				$oldID=$this->_getDataFromObject($import_club,'id');
				$alias=$this->_getDataFromObject($import_club,'alias');
				$p_club->set('name',$this->_newclubs[$key]);
				$p_club->set('address',$this->_getDataFromObject($import_club,'address'));
				$p_club->set('zipcode',$this->_getDataFromObject($import_club,'zipcode'));
				$p_club->set('location',$this->_getDataFromObject($import_club,'location'));
				$p_club->set('state',$this->_getDataFromObject($import_club,'state'));
				$p_club->set('country',$this->_newclubscountry[$key]);
				$p_club->set('founded',$this->_getDataFromObject($import_club,'founded'));
				$p_club->set('phone',$this->_getDataFromObject($import_club,'phone'));
				$p_club->set('fax',$this->_getDataFromObject($import_club,'fax'));
				$p_club->set('email',$this->_getDataFromObject($import_club,'email'));
				$p_club->set('website',$this->_getDataFromObject($import_club,'website'));
				$p_club->set('president',$this->_getDataFromObject($import_club,'president'));
				$p_club->set('manager',$this->_getDataFromObject($import_club,'manager'));
				$p_club->set('logo_big',$this->_getDataFromObject($import_club,'logo_big'));
				$p_club->set('logo_middle',$this->_getDataFromObject($import_club,'logo_middle'));
				$p_club->set('logo_small',$this->_getDataFromObject($import_club,'logo_small'));
                $p_club->set('dissolved',$this->_getDataFromObject($import_club,'dissolved'));
                $p_club->set('founded',$this->_getDataFromObject($import_club,'founded'));
                
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_club->set('alias',$alias);
				}
				else
				{
					$p_club->set('alias',OutputFilter::stringURLSafe($this->_getDataFromObject($p_club,'name')));
				}
				if ($this->_importType!='clubs')	// force playground_id to be set to default if only clubs are imported
				{
					if (($this->import_version=='NEW') && ($import_club->standard_playground > 0))
					{
						if (isset($this->_convertPlaygroundID[(int)$this->_getDataFromObject($import_club,'standard_playground')]))
						{
							$p_club->set('standard_playground',(int)$this->_convertPlaygroundID[(int)$this->_getDataFromObject($import_club,'standard_playground')]);
						}
					}
				}
				if (($this->import_version=='NEW') && ($import_club->extended!=''))
				{
					$p_club->set('extended',$this->_getDataFromObject($import_club,'extended'));
				}
				$query="SELECT	id,
						name,
						country
					FROM #__joomleague_club
					WHERE	name='".addslashes(stripslashes($p_club->name))."' AND
						country='$p_club->country'";
				$db->setQuery($query); 
                $db->execute();
				if ($object=$db->loadObject())
				{
					$this->_convertClubID[$oldID]=$object->id;
					$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
					$my_text .= Text::sprintf('Using existing club data: %1$s',"</span><strong>$object->name</strong>");
					$my_text .= '<br />';
				}
				else
				{
					if ($p_club->store()===false)
					{
						$my_text .= '<span style="color:'.$this->storeFailedColor.'"><strong>';
						$my_text .= Text::_('Error in function importClubs').'</strong></span><br />';
						$my_text .= Text::sprintf('Clubname: %1$s',$p_club->name).'<br />';
						$my_text .= Text::sprintf('Error-Text #%1$s#',$db->getErrorMsg()).'<br />';
						$my_text .= '<pre>'.print_r($p_club,true).'</pre>';
						$this->_success_text['Importing general club data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$p_club->id; 
						$this->_convertClubID[$oldID]=$insertID;
						$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
						$my_text .= Text::sprintf(	'Created new club data: %1$s - %2$s',
													"</span><strong>$p_club->name</strong>",
													"".$p_club->country.""
													);
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['Importing general club data:']=$my_text;
		return true;
	}

	private function _convertNewPlaygroundIDs()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		$converted=false;
		if (isset($this->_convertPlaygroundID) && !empty($this->_convertPlaygroundID))
		{
			foreach ($this->_convertPlaygroundID AS $key => $new_pg_id)
			{
				if (empty($new_pg_id)) continue;
				$p_playground=$this->_getPlaygroundRecord($new_pg_id);
				foreach ($this->_convertClubID AS $key => $new_club_id)
				{
					if (empty($new_club_id)) continue;
					if (isset($p_playground->club_id) && ($p_playground->club_id ==$key))
					{
						if ($this->_updatePlaygroundRecord($new_club_id,$new_pg_id))
						{
							$converted=true;
							$my_text .= '<span style="color:green">';
							$my_text .= Text::sprintf(	'Converted club-info %1$s in imported playground %2$s',
														'</span><strong>'.$this->_getClubName($new_club_id).'</strong><span style="color:green">',
														"</span><strong>$p_playground->name</strong>");
							$my_text .= '<br />';
						}
						break;
					}
				}
			}
			if (!$converted){$my_text .= '<span style="color:green">'.Text::_('Nothing needed to be converted').'<br />';}
			$this->_success_text['Converting new playground club-IDs of new playground data:']=$my_text;
		}
		return true;
	}

	private function _importTeams()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['team']) || count($this->_datas['team'])==0){return true;}
		if ((!isset($this->_newteams) || count($this->_newteams)==0) &&
			(!isset($dbteamsid) || count($dbteamsid)==0)){return true;}

		if (!empty($dbteamsid))
		{
			$query='SELECT id,name,club_id,short_name,middle_name,info FROM #__joomleague_team GROUP BY id';
			$db->setQuery($query);
			$dbTeams = $db->loadObjectList('id');

			foreach ($dbteamsid AS $key => $id)
			{
				if (empty($this->_newteams[$key]))
				{
					$oldID=$this->_getDataFromObject($this->_datas['team'][$key],'id');
					$this->_convertTeamID[$oldID]=$id;
					$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
					$my_text .= Text::sprintf(	'Using existing team data: %1$s - %2$s - %3$s - %4$s',
												'</span><strong>'.$dbTeams[$id]->name.'</strong>',
												'<strong>'.$dbTeams[$id]->short_name.'</strong>',
												'<strong>'.$dbTeams[$id]->middle_name.'</strong>',
												'<strong>'.$dbTeams[$id]->info.'</strong>'
												);
					$my_text .= '<br />';
				}
			}
		}
//To Be fixed: Falls Verein neu angelegt wird, muss auch das Team neu angelegt werden.
		if (!empty($this->_newteams))
		{
			foreach ($this->_newteams AS $key => $value)
			{
				$p_team = $this->getTable('Team');
                if(!isset($this->_datas['team'][$key]) || !isset($this->_newteamsmiddle[$key])) continue;
				$import_team=$this->_datas['team'][$key];
				$oldID=$this->_getDataFromObject($import_team,'id');
				$alias=$this->_getDataFromObject($import_team,'alias');
				$oldClubID=$this->_getDataFromObject($import_team,'club_id');
				if ((!empty($import_team->club_id)) && (isset($this->_convertClubID[$oldClubID])))
				{
					$p_team->set('club_id',$this->_convertClubID[$oldClubID]);
				}
				else
				{
					$p_team->set('club_id',(-1));
				}
				$p_team->set('name',$this->_newteams[$key]);
				$p_team->set('short_name',$this->_newteamsshort[$key]);
				$p_team->set('middle_name',$this->_newteamsmiddle[$key]);
				$p_team->set('website',$this->_getDataFromObject($import_team,'website'));
				$p_team->set('notes',$this->_getDataFromObject($import_team,'notes'));
				$p_team->set('picture',$this->_getDataFromObject($import_team,'picture'));
				$p_team->set('info',$this->_newteamsinfo[$key]);
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_team->set('alias',$alias);
				}
				else
				{
					$p_team->set('alias',OutputFilter::stringURLSafe($this->_getDataFromObject($p_team,'name')));
				}
				if (($this->import_version=='NEW') && ($import_team->extended!=''))
				{
					$p_team->set('extended',$this->_getDataFromObject($import_team,'extended'));
				}
				$query="SELECT	id,
								name,
								short_name,
								middle_name,
								info
						FROM #__joomleague_team
						WHERE	name='".addslashes(stripslashes($p_team->name))."' AND
								info='".addslashes(stripslashes($p_team->info))."' ";
				$db->setQuery($query);
				$db->execute();
				if ($object=$db->loadObject())
				{
					$this->_convertTeamID[$oldID]=$object->id;
					$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
					$my_text .= Text::sprintf('Using existing team data: %1$s',"</span><strong>$object->name</strong>");
					$my_text .= '<br />';
				}
				else
				{
					if ($p_team->store()===false)
					{
						$my_text .= '<span style="color:'.$this->storeFailedColor.'"><strong>';
						$my_text .= Text::_('Error in function _importTeams').'</strong></span><br />';
						$my_text .= Text::sprintf('Teamname: %1$s',$p_team->name).'<br />';
						$my_text .= Text::sprintf('Error-Text #%1$s#',$db->getErrorMsg()).'<br />';
						$my_text .= '<pre>'.print_r($p_team,true).'</pre>';
						$this->_success_text['Importing general team data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$p_team->id; 
						$this->_convertTeamID[$oldID]=$insertID;
						$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
						$my_text .= Text::sprintf(	'Created new team data: %1$s - %2$s - %3$s - %4$s - %5$s',
													"</span><strong>$p_team->name</strong>",
													"<strong>$p_team->short_name</strong>",
													"<strong>$p_team->middle_name</strong>",
													"<strong>$p_team->info</strong>",
													"<strong>$p_team->club_id</strong>"
													);
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['Importing general team data:']=$my_text;
		return true;
	}

	private function _importPersons()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($dbpersonsid) || count($dbpersonsid)==0)){return true;}

		$my_text='';
		if (!empty($dbpersonsid))
		{
			foreach ($dbpersonsid AS $key => $id)
			{
				if(empty($id)) continue;
				$oldID=$this->_getDataFromObject($this->_datas['person'][$key],'id');
				$this->_convertPersonID[$oldID]=$id;
				$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
				$my_text .= Text::sprintf(	'Using existing person data: %1$s',
											'</span><strong>'.$this->_getObjectName('person',$id,"CONCAT(id,' -> ',lastname,',',firstname,' - ',nickname,' - ',birthday) AS name").'</strong>');
				$my_text .= '<br />';

				// DFBnet Spielerimport: Update des Info Feldes, sofern vorhanden
				$info_given = $this->_getDataFromObject($this->_datas['person'][$key],'info');
				if (!empty($info_given))
				{
					$update_person =  $this->getTable('Person');
					$update_person->load($id);
					$update_person->info = $this->_getDataFromObject($this->_datas['person'][$key],'info');
					if ($update_person->store() === false)
					{
						//$my_text .= '';
					}
					else
					{
						//$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
						$my_text .= '<span style="color:green">';
						$my_text .= '...but updated info field.<br />';
						$my_text .= '</span>';
					}
				}
			}
		}
		if (!empty($this->_newpersonsid))
		{
			foreach ($this->_newpersonsid AS $key => $id)
			{
				$p_person =  $this->getTable('Person');
				$import_person=$this->_datas['person'][$key];
				$oldID=$this->_getDataFromObject($import_person,'id');
				if(isset($this->_newperson_lastname[$key])) {
					$p_person->set('lastname',trim($this->_newperson_lastname[$key]));
				} else {
					$p_person->set('lastname',$this->_getDataFromObject($import_person,'lastname'));
				}
				if(isset($this->_newperson_firstname[$key])) {
					$p_person->set('firstname',trim($this->_newperson_firstname[$key]));
				} else {
					$p_person->set('firstname',$this->_getDataFromObject($import_person,'firstname'));
				}
				if(isset($this->_newperson_nickname[$key])) {
					$p_person->set('nickname',trim($this->_newperson_nickname[$key]));
				} else {
					$p_person->set('nickname',$this->_getDataFromObject($import_person,'nickname'));
				}
				if(isset($this->_newperson_birthday[$key])) {
					$p_person->set('birthday',$this->_newperson_birthday[$key]);
				} else {
					$p_person->set('birthday',$this->_getDataFromObject($import_person,'birthday'));
				}
				$p_person->set('country',$this->_getDataFromObject($import_person,'country'));
				$p_person->set('knvbnr',$this->_getDataFromObject($import_person,'knvbnr'));
				$p_person->set('height',$this->_getDataFromObject($import_person,'height'));
				$p_person->set('weight',$this->_getDataFromObject($import_person,'weight'));
				$p_person->set('picture',$this->_getDataFromObject($import_person,'picture'));
				$p_person->set('show_pic',$this->_getDataFromObject($import_person,'show_pic'));
				$p_person->set('show_persdata',$this->_getDataFromObject($import_person,'show_persdata'));
				$p_person->set('show_teamdata',$this->_getDataFromObject($import_person,'show_teamdata'));
				$p_person->set('show_on_frontend',$this->_getDataFromObject($import_person,'show_on_frontend'));
				$p_person->set('info',$this->_getDataFromObject($import_person,'info'));
				$p_person->set('notes',$this->_getDataFromObject($import_person,'notes'));
				$p_person->set('phone',$this->_getDataFromObject($import_person,'phone'));
				$p_person->set('mobile',$this->_getDataFromObject($import_person,'mobile'));
				$p_person->set('email',$this->_getDataFromObject($import_person,'email'));
				$p_person->set('website',$this->_getDataFromObject($import_person,'website'));
				$p_person->set('address',$this->_getDataFromObject($import_person,'address'));
				$p_person->set('zipcode',$this->_getDataFromObject($import_person,'zipcode'));
				$p_person->set('location',$this->_getDataFromObject($import_person,'location'));
				$p_person->set('state',$this->_getDataFromObject($import_person,'state'));
				$p_person->set('address_country',$this->_getDataFromObject($import_person,'address_country'));
				$p_person->set('extended',$this->_getDataFromObject($import_person,'extended'));
				$p_person->set('published',1);
				if ($this->_importType!='persons')	// force position_id to be set to default if only persons are imported
				{
					if ($import_person->position_id > 0)
					{
						if (isset($this->_convertPositionID[(int)$this->_getDataFromObject($import_person,'position_id')]))
						{
							$p_person->set('position_id',(int)$this->_convertPositionID[(int)$this->_getDataFromObject($import_person,'position_id')]);
						}
					}
				}
				$alias=$this->_getDataFromObject($import_person,'alias');
				$aliasparts=array(trim($p_person->firstname),trim($p_person->lastname));
				$p_alias=OutputFilter::stringURLSafe(implode(' ',$aliasparts));
				if ((isset($alias)) && (trim($alias)!=''))
				{
					$p_person->set('alias',OutputFilter::stringURLSafe($alias));
				}
				else
				{
					$p_person->set('alias',$p_alias);
				}
				$query="	SELECT * FROM #__joomleague_person
							WHERE	firstname='".addslashes(stripslashes($p_person->firstname))."' AND
									lastname='".addslashes(stripslashes($p_person->lastname))."' AND
									birthday='$p_person->birthday'";
				$db->setQuery($query);
				$db->execute();
				if ($object=$db->loadObject())
				{
					$this->_convertPersonID[$oldID]=$object->id;
					$nameStr=!empty($object->nickname) ? '['.$object->nickname.']' : '';
					$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
					$my_text .= Text::sprintf(	'Using existing person data: %1$s %2$s [%3$s] - %4$s',
												"</span><strong>$object->lastname</strong>",
												"<strong>$object->firstname</strong>",
												"<strong>$nameStr</strong>",
												"<strong>$object->birthday</strong>");
					$my_text .= '<br />';
				}
				else
				{
					if ($p_person->store()===false)
					{
						$my_text .= 'error on person import: ';
						$my_text .= $p_person->lastname.'-';
						$my_text .= $p_person->firstname.'-';
						$my_text .= $p_person->nickname.'-';
						$my_text .= $p_person->birthday;
						$my_text .= "<br />Error: _importPersons<br />#$my_text#<br />#<pre>".print_r($p_person,true).'</pre>#';
						$this->_success_text['Importing general person data:']=$my_text;
						return false;
					}
					else
					{
						$insertID=$p_person->id; 
						$this->_convertPersonID[$oldID]=$insertID;
						$dNameStr=((!empty($p_person->lastname)) ?
									$p_person->lastname :
									'<span style="color:orange">'.Text::_('Has no lastname').'</span>');
						$dNameStr .= ','.((!empty($p_person->firstname)) ?
									$p_person->firstname.' - ' :
									'<span style="color:orange">'.Text::_('Has no firstname').' - </span>');
						$dNameStr .= ((!empty($p_person->nickname)) ? "'".$p_person->nickname."' - " : '');
						$dNameStr .= $p_person->birthday;
						$dNameStr .= ', oldid: ' . $oldID ;
						$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
						$my_text .= Text::sprintf('Created new person data: %1$s',"</span><strong>$dNameStr</strong>");
						$my_text .= '<br />';
					}
				}
			}
		}
		$this->_success_text['Importing general person data:']=$my_text;
		return true;
	}

	private function _importProject()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		$p_project = $this->getTable('Project');
		$p_project->set('name',trim($this->_name));
		$p_project->set('alias',OutputFilter::stringURLSafe(trim($this->_name)));
		$p_project->set('league_id',$this->_league_id);
		$p_project->set('season_id',$this->_season_id);
		$p_project->set('master_template',$this->_template_id);
		$p_project->set('sub_template_id',0);
		$p_project->set('extension',$this->_getDataFromObject($this->_datas['project'],'extension'));
		$p_project->set('timezone', $this->_timezone);
		$p_project->set('project_type',$this->_getDataFromObject($this->_datas['project'],'project_type'));
		$p_project->set('teams_as_referees',$this->_getDataFromObject($this->_datas['project'],'teams_as_referees'));
		$p_project->set('sports_type_id',$this->_sportstype_id);
		$p_project->set('current_round',$this->_getDataFromObject($this->_datas['project'],'current_round'));
		$p_project->set('current_round_auto',$this->_getDataFromObject($this->_datas['project'],'current_round_auto'));
		$p_project->set('auto_time',$this->_getDataFromObject($this->_datas['project'],'auto_time'));
		$p_project->set('start_date',$this->_getDataFromObject($this->_datas['project'],'start_date'));
		$p_project->set('start_time',$this->_getDataFromObject($this->_datas['project'],'start_time'));
		$p_project->set('fav_team_color',$this->_getDataFromObject($this->_datas['project'],'fav_team_color'));
		$p_project->set('fav_team_text_color',$this->_getDataFromObject($this->_datas['project'],'fav_team_text_color'));
		$p_project->set('use_legs',$this->_getDataFromObject($this->_datas['project'],'use_legs'));
		$p_project->set('game_regular_time',$this->_getDataFromObject($this->_datas['project'],'game_regular_time'));
		$p_project->set('game_parts',$this->_getDataFromObject($this->_datas['project'],'game_parts'));
		$p_project->set('halftime',$this->_getDataFromObject($this->_datas['project'],'halftime'));
		$p_project->set('allow_add_time',$this->_getDataFromObject($this->_datas['project'],'allow_add_time'));
		$p_project->set('add_time',$this->_getDataFromObject($this->_datas['project'],'add_time'));
		$p_project->set('points_after_regular_time',$this->_getDataFromObject($this->_datas['project'],'points_after_regular_time'));
		$p_project->set('points_after_add_time',$this->_getDataFromObject($this->_datas['project'],'points_after_add_time'));
		$p_project->set('points_after_penalty',$this->_getDataFromObject($this->_datas['project'],'points_after_penalty'));
		$p_project->set('template',$this->_getDataFromObject($this->_datas['project'],'template'));
		$p_project->set('enable_sb',$this->_getDataFromObject($this->_datas['project'],'enable_sb'));
		$p_project->set('sb_catid',$this->_getDataFromObject($this->_datas['project'],'sb_catid'));
		if ($this->_published){$p_project->set('published',1);}
		$p_project->set('asset_id', 0);
		if ($p_project->store()===false)
		{
			$my_text .= '<span style="color:'.$this->storeFailedColor.'"><strong>';
			$my_text .= Text::_('Error in function _importProject').'</strong></span><br />';
			$my_text .= Text::sprintf('Projectname: %1$s',$p_project->name).'<br />';
			$my_text .= Text::sprintf('Error-Text #%1$s#',$db->getErrorMsg()).'<br />';
			$my_text .= '<pre>'.print_r($p_project,true).'</pre>';
			$this->_success_text['Importing general project data:']=$my_text;
			return false;
		}
		else
		{
			$this->_project_id = $p_project->id;
			$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
			$my_text .= Text::sprintf('Created new project data: %1$s',"</span><strong>$this->_name</strong>");
			$my_text .= '<br />';
			$this->_success_text['Importing general project data:']=$my_text;
			return true;
		}
	}

	/**
	 * check that all templates in default location have a corresponding record,except if project has a master template
	 *
	 */
	private function _checklist()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$project_id=$this->_project_id;
		$defaultpath=JPATH_COMPONENT_SITE.'/settings';
		$extensiontpath=JPATH_COMPONENT_SITE.'/extensions/';
		$predictionTemplatePrefix='prediction';

		if (!$project_id){return;}

		// get info from project
		$query='SELECT master_template,extension FROM #__joomleague_project WHERE id='.(int)$project_id;

		$db->setQuery($query);
		$params=$db->loadObject();

		// if it's not a master template,do not create records.
		if ($params->master_template){return true;}

		// otherwise,compare the records with the files
		// get records
		$query='SELECT template FROM #__joomleague_template_config WHERE project_id='.(int)$project_id;

		$db->setQuery($query);
		$records=$db->loadColumn();
		if (empty($records)){$records=array();}

		// first check extension template folder if template is not default
		if ((isset($params->extension)) && ($params->extension!=''))
		{
			if (is_dir($extensiontpath.$params->extension.'/settings'))
			{
				$xmldirs[]=$extensiontpath.$params->extension.'/settings';
			}
		}

		// add default folder
		$xmldirs[]=$defaultpath.'/default';

		// now check for all xml files in these folders
		foreach ($xmldirs as $xmldir)
		{
			if ($handle=opendir($xmldir))
			{
				/* check that each xml template has a corresponding record in the
				database for this project. If not,create the rows with default values
				from the xml file */
				while ($file=readdir($handle))
				{
					if	(	$file!='.' &&
							$file!='..' &&
							$file!='do_tipsl' &&
							strtolower(substr($file,-3))=='xml' &&
							strtolower(substr($file,0,strlen($predictionTemplatePrefix)))!=$predictionTemplatePrefix
						)
					{
						$template=substr($file,0,(strlen($file)-4));

						if ((empty($records)) || (!in_array($template,$records)))
						{
							$xmlfile=$xmldir.'/'.$file;
							$arrStandardSettings=array();
							if(file_exists($xmlfile)) {
								$strXmlFile = $xmlfile;
								$form = Form::getInstance($template, $strXmlFile);
								$fieldsets = $form->getFieldsets();
								foreach ($fieldsets as $fieldset) {
									foreach($form->getFieldset($fieldset->name) as $field) {
										$arrStandardSettings[$field->name]=$field->value;
									}
								}
							}
							$defaultvalues=implode("\n",$arrStandardSettings);
							$query="	INSERT INTO #__joomleague_template_config (template,title,params,project_id)
													VALUES ('$template','".$form->getName()."','$defaultvalues','$project_id')";
							
						try
							{
								$db->setQuery($query);
								$db->execute();
							}
						catch (Exception $e)
							{
								$app->enqueueMessage(Text::_($e->getMessage()), 'error');
								return false;
							}
							array_push($records,$template);
						}
					}
				}
				closedir($handle);
			}
		}
	}

	private function _importTemplate()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if ($this->_template_id > 0) // Uses a master template
		{
			$query_template='SELECT id,master_template FROM #__joomleague_project WHERE id='.$this->_template_id;
			$db->setQuery($query_template);
			$template_row=$db->loadAssoc();
			if ($template_row['master_template']==0)
			{
				$this->_master_template=$template_row['id'];
			}
			else
			{
				$this->_master_template=$template_row['master_template'];
			}
			$query="SELECT id,template FROM #__joomleague_template_config WHERE project_id=".$this->_master_template;
			$db->setQuery($query);
			$rows=$db->loadObjectList();
			foreach ($rows AS $row)
			{
				$p_template = $this->getTable('TemplateConfig');
				$p_template->load($row->id);
				$p_template->set('project_id',$this->_project_id);
				if ($p_template->store()===false)
				{
					$my_text .= 'error on master template import: ';
					$my_text .= "<br />Error: _importTemplate<br />#$my_text#<br />#<pre>".print_r($p_template,true).'</pre>#';
					$this->_success_text['Importing template data:']=$my_text;
					return false;
				}
				else
				{
					$my_text .= $p_template->template;
					$my_text .= ' <font color="'.$this->storeSuccessColor.'">'.Text::_('...created new data').'</font><br />';
					$my_text .= '<br />';
				}
			}
		}
		else
		{
			$this->_master_template=0;
			$predictionTemplatePrefix='prediction';
			if ((isset($this->_datas['template'])) && (is_array($this->_datas['template'])))
			{
				foreach ($this->_datas['template'] as $value)
				{
					$p_template = $this->getTable('TemplateConfig');
					$template=$this->_getDataFromObject($value,'template');
					$p_template->set('template',$template);
					//actually func is unused in 1.5.0
					//$p_template->set('func',$this->_getDataFromObject($value,'func'));
					$p_template->set('title',$this->_getDataFromObject($value,'title'));
					$p_template->set('project_id',$this->_project_id);
					$p_template->set('params',$this->_getDataFromObject($value,'params'));
					if	((strtolower(substr($template,0,strlen($predictionTemplatePrefix)))!=$predictionTemplatePrefix) &&
						($template!='do_tipsl') &&
						($template!='frontpage') &&
						($template!='table') &&
						($template!='tipranking') &&
						($template!='tipresults') &&
						($template!='user'))
					{
						if ($p_template->store()===false)
						{
							$my_text .= 'error on own template import: ';
							$my_text .= "<br />Error: _importTemplate<br />#$my_text#<br />#<pre>".print_r($p_template,true).'</pre>#';
							$this->_success_text['Importing template data:']=$my_text;
							return false;
						}
						else
						{
							$dTitle=(!empty($p_template->title)) ? Text::_($p_template->title) : $p_template->template;
							$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
							$my_text .= Text::sprintf('Created new template data: %1$s',"</span><strong>$dTitle</strong>");
							$my_text .= '<br />';
						}
					}
				}
			}
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query="UPDATE #__joomleague_project SET master_template=$this->_master_template WHERE id=$this->_project_id";
		$db->setQuery($query);
		$db->execute();
		$this->_success_text['Importing template data:']=$my_text;
		if ($this->_master_template==0)
		{
			// check and create missing templates if needed
			$this->_checklist();
			$my_text='<span style="color:green">';
			$my_text .= Text::_('Checked and created missing template data if needed');
			$my_text .= '</span><br />';
			$this->_success_text['Importing template data:'] .= $my_text;
		}
		return true;
	}

	private function _importDivisions()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['division']) || count($this->_datas['division'])==0){return true;}
		if (isset($this->_datas['division']))
		{
			foreach ($this->_datas['division'] as $key => $division)
			{
				$p_division =  $this->getTable('Division');
				$oldId=(int)$division->id;
				$p_division->set('project_id',$this->_project_id);
				if ($division->id ==$this->_datas['division'][$key]->id)
				{
					$name=trim($this->_getDataFromObject($division,'name'));
					$p_division->set('name',$name);
					$p_division->set('shortname',$this->_getDataFromObject($division,'shortname'));
					$p_division->set('notes',$this->_getDataFromObject($division,'notes'));
					$p_division->set('parent_id',$this->_getDataFromObject($division,'parent_id'));
					if (trim($p_division->alias)!='')
					{
						$p_division->set('alias',$this->_getDataFromObject($division,'alias'));
					}
					else
					{
						$p_division->set('alias',OutputFilter::stringURLSafe($name));
					}
				}
				if ($p_division->store()===false)
				{
					$my_text .= 'error on division import: ';
					$my_text .= '#'.$oldID.'#';
					$my_text .= "<br />Error: _importDivisions<br />#$my_text#<br />#<pre>".print_r($p_division,true).'</pre>#';
					$this->_success_text['Importing division data:']=$my_text;
					return false;
				}
				else
				{
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf('Created new division data: %1$s',"</span><strong>$name</strong>");
					$my_text .= '<br />';
				}
				$insertID=$p_division->id; 
				$this->_convertDivisionID[$oldId] = $insertID;
			}
			$this->_success_text['Importing division data:']=$my_text;
			return true;
		}
	}

	private function _importProjectTeam()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['projectteam']) || count($this->_datas['projectteam'])==0){return true;}

		if (!isset($this->_datas['team']) || count($this->_datas['team'])==0){return true;}
		if ((!isset($this->_newteams) || count($this->_newteams)==0) &&
			(!isset($dbteamsid) || count($dbteamsid)==0)){return true;}


		foreach ($this->_datas['projectteam'] as $key => $projectteam)
		{
			$p_projectteam = $this->getTable('ProjectTeam');
			$import_projectteam=$this->_datas['projectteam'][$key];
			$oldID=$this->_getDataFromObject($import_projectteam,'id');
			$p_projectteam->set('project_id',$this->_project_id);
            $convertedTeamId = isset($this->_convertTeamID[$this->_getDataFromObject($projectteam,'team_id')]) ? $this->_convertTeamID[$this->_getDataFromObject($projectteam,'team_id')] : 0;
            if($convertedTeamId == 0) continue; //imported team doesn´t exists anymore, remove the projectteam entry inside the source project!
			$p_projectteam->set('team_id', $convertedTeamId);

			if (count($this->_convertDivisionID) > 0)
			{
				$p_projectteam->set('division_id',$this->_convertDivisionID[$this->_getDataFromObject($projectteam,'division_id')]);
			}

			$p_projectteam->set('start_points',$this->_getDataFromObject($projectteam,'start_points'));
			$p_projectteam->set('points_finally',$this->_getDataFromObject($projectteam,'points_finally'));
			$p_projectteam->set('neg_points_finally',$this->_getDataFromObject($projectteam,'neg_points_finally'));
			$p_projectteam->set('matches_finally',$this->_getDataFromObject($projectteam,'matches_finally'));
			$p_projectteam->set('won_finally',$this->_getDataFromObject($projectteam,'won_finally'));
			$p_projectteam->set('draws_finally',$this->_getDataFromObject($projectteam,'draws_finally'));
			$p_projectteam->set('lost_finally',$this->_getDataFromObject($projectteam,'lost_finally'));
			$p_projectteam->set('homegoals_finally',$this->_getDataFromObject($projectteam,'homegoals_finally'));
			$p_projectteam->set('guestgoals_finally',$this->_getDataFromObject($projectteam,'guestgoals_finally'));
			$p_projectteam->set('diffgoals_finally',$this->_getDataFromObject($projectteam,'diffgoals_finally'));
			$p_projectteam->set('is_in_score',$this->_getDataFromObject($projectteam,'is_in_score'));
			$p_projectteam->set('use_finally',$this->_getDataFromObject($projectteam,'use_finally'));

			if ($this->import_version=='NEW')
			{
				if (isset($import_projectteam->mark))
				{
					$p_projectteam->set('mark',$this->_getDataFromObject($projectteam,'mark'));
				}
				$p_projectteam->set('info',$this->_getDataFromObject($projectteam,'info'));
				$p_projectteam->set('reason',$this->_getDataFromObject($projectteam,'reason'));
				$p_projectteam->set('notes',$this->_getDataFromObject($projectteam,'notes'));
			}
			else
			{
				$p_projectteam->set('notes',$this->_getDataFromObject($projectteam,'description'));
				$p_projectteam->set('reason',$this->_getDataFromObject($projectteam,'info'));
			}
			if ((isset($projectteam->standard_playground)) && ($projectteam->standard_playground > 0))
			{
				if (isset($this->_convertPlaygroundID[$this->_getDataFromObject($projectteam,'standard_playground')]))
				{
					$p_projectteam->set('standard_playground',$this->_convertPlaygroundID[$this->_getDataFromObject($projectteam,'standard_playground')]);
				}
			}

			if ($p_projectteam->store()===false)
			{
				$my_text .= 'error on projectteam import: ';
				$my_text .= $oldID;
				$my_text .= '<br />Error: _importProjectTeam<br />~'.$my_text.'~<br />~<pre>'.print_r($p_projectteam,true).'</pre>~';
				$this->_success_text['Importing projectteam data:']=$my_text;
				//return false;
			}
			else
			{
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf(	'Created new projectteam data: %1$s',
											'</span><strong>'.$this->_getTeamName2($p_projectteam->team_id).'</strong>');
				$my_text .= '<br />';
			}
			$insertID=$p_projectteam->id;
			$this->_convertProjectTeamID[$this->_getDataFromObject($projectteam,'id')]=$insertID;
		}
		$this->_success_text['Importing projectteam data:']=$my_text;
		return true;
	}

	private function _importProjectReferees()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['projectreferee']) || count($this->_datas['projectreferee'])==0){return true;}

		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($dbpersonsid) || count($dbpersonsid)==0)){return true;}

		foreach ($this->_datas['projectreferee'] as $key => $projectreferee)
		{
			if(!isset($this->_datas['projectreferee'][$key]) ||
				!isset($this->_convertPersonID[$this->_getDataFromObject($import_projectreferee,'person_id')])) {
				continue;
			}
			$import_projectreferee=$this->_datas['projectreferee'][$key];
			$oldID=$this->_getDataFromObject($import_projectreferee,'id');
			$p_projectreferee = $this->getTable('ProjectReferee');

			$p_projectreferee->set('project_id',$this->_project_id);
			$p_projectreferee->set('person_id',$this->_convertPersonID[$this->_getDataFromObject($import_projectreferee,'person_id')]);
			$p_projectreferee->set('project_position_id',$this->_convertProjectPositionID[$this->_getDataFromObject($import_projectreferee,'project_position_id')]);

			$p_projectreferee->set('notes',$this->_getDataFromObject($import_projectreferee,'notes'));
			$p_projectreferee->set('picture',$this->_getDataFromObject($import_projectreferee,'picture'));
			$p_projectreferee->set('extended',$this->_getDataFromObject($import_projectreferee,'extended'));
			$p_projectreferee->set('published',1);

			if ($p_projectreferee->store()===false)
			{
				$my_text .= 'error on projectreferee import: ';
				$my_text .= $oldID;
				$my_text .= '<br />Error: _importProjectReferees<br />~'.$my_text.'~<br />~<pre>'.print_r($p_projectreferee,true).'</pre>~';
				$this->_success_text['Importing projectreferee data:']=$my_text;
				return false;
			}
			else
			{
				$dPerson=$this->_getPersonName($p_projectreferee->person_id);
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf(	'Created new projectreferee data: %1$s,%2$s',"</span><strong>$dPerson->lastname","$dPerson->firstname</strong>");
				$my_text .= '<br />';
			}
			$insertID=$p_projectreferee->id;
			$this->_convertProjectRefereeID[$oldID]=$insertID;
		}
		$this->_success_text['Importing projectreferee data:']=$my_text;
		return true;
	}

	private function _importProjectPositions()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['projectposition']) || count($this->_datas['projectposition'])==0){return true;}

		if (!isset($this->_datas['position']) || count($this->_datas['position'])==0){return true;}
		if ((!isset($this->_newpositionsid) || count($this->_newpositionsid)==0) &&
			(!isset($dbpositionsid) || count($dbpositionsid)==0)){return true;}

		foreach ($this->_datas['projectposition'] as $key => $projectposition)
		{
			$import_projectposition=$this->_datas['projectposition'][$key];
			$oldID=$this->_getDataFromObject($import_projectposition,'id');
			$p_projectposition =  $this->getTable('ProjectPosition');
			$p_projectposition->set('project_id',$this->_project_id);
			$oldPositionID=$this->_getDataFromObject($import_projectposition,'position_id');
			if (!isset($this->_convertPositionID[$oldPositionID]))
			{
				$my_text .= '<span style="color:red">';
				$my_text .= Text::sprintf(	'Skipping import of ProjectPosition-ID %1$s. Old-PositionID: %2$s',
								"</span><strong>$oldID</strong><span style='color:red'>",
								"</span><strong>$oldPositionID</strong>").'<br />';
				continue;
			}
			$p_projectposition->set('position_id',$this->_convertPositionID[$oldPositionID]);
			if ($p_projectposition->store()===false)
			{
				$my_text .= 'error on ProjectPosition import: ';
				$my_text .= '#'.$oldID.'#';
				$my_text .= "<br />Error: _importProjectpositions<br />#$my_text#<br />#<pre>".print_r($p_projectposition,true).'</pre>#';
				$this->_success_text['Importing projectposition data:']=$my_text;
				return false;
			}
			else
			{
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf(	'Created new projectposition data: %1$s - %2$s',
								"</span><strong>".Text::_($this->_getObjectName('position',$p_projectposition->position_id)).'</strong><span style="color:'.$this->storeSuccessColor.'">',
								"</span><strong>".$p_projectposition->position_id.'</strong>');
				$my_text .= '<br />';
			}
			$insertID=$p_projectposition->id;
			$this->_convertProjectPositionID[$oldID]=$insertID;
		}
		$this->_success_text['Importing projectposition data:']=$my_text;
		return true;
	}

	private function _importTeamPlayer()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['teamplayer']) || count($this->_datas['teamplayer'])==0){return true;}

		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($dbpersonsid) || count($dbpersonsid)==0)){return true;}

		foreach ($this->_datas['teamplayer'] as $key => $teamplayer)
		{
			$p_teamplayer = $this->getTable('TeamPlayer');
			$import_teamplayer=$this->_datas['teamplayer'][$key];
			$oldID=$this->_getDataFromObject($import_teamplayer,'id');
			$oldTeamID=$this->_getDataFromObject($import_teamplayer,'projectteam_id');
			$oldPersonID=$this->_getDataFromObject($import_teamplayer,'person_id');
			if (!isset($this->_convertProjectTeamID[$oldTeamID]) ||
				!isset($this->_convertPersonID[$oldPersonID]))
			{
				$my_text .= '<span style="color:red">';
				$my_text .= Text::sprintf(	'Skipping import of TeamPlayer-ID %1$s. Old-ProjectTeamID: %2$s - Old-PersonID: %3$s',
								"</span><strong>$oldID</strong><span style='color:red'>",
								"</span><strong>$oldTeamID</strong><span style='color:red'>",
								"</span><strong>$oldPersonID</strong>").'<br />';
				continue;
			}
			$p_teamplayer->set('projectteam_id',$this->_convertProjectTeamID[$oldTeamID]);
			$p_teamplayer->set('person_id',$this->_convertPersonID[$oldPersonID]);
			$oldPositionID=$this->_getDataFromObject($import_teamplayer,'project_position_id');
			if (isset($this->_convertProjectPositionID[$oldPositionID]))
			{
				$p_teamplayer->set('project_position_id',$this->_convertProjectPositionID[$oldPositionID]);
			}
			$p_teamplayer->set('active',$this->_getDataFromObject($import_teamplayer,'active'));
			$p_teamplayer->set('jerseynumber',$this->_getDataFromObject($import_teamplayer,'jerseynumber'));
			$p_teamplayer->set('notes',$this->_getDataFromObject($import_teamplayer,'notes'));
			$p_teamplayer->set('picture',$this->_getDataFromObject($import_teamplayer,'picture'));
			$p_teamplayer->set('extended',$this->_getDataFromObject($import_teamplayer,'extended'));
			$p_teamplayer->set('injury',$this->_getDataFromObject($import_teamplayer,'injury'));
			$p_teamplayer->set('injury_date',$this->_getDataFromObject($import_teamplayer,'injury_date'));
			$p_teamplayer->set('injury_end',$this->_getDataFromObject($import_teamplayer,'injury_end'));
			$p_teamplayer->set('injury_detail',$this->_getDataFromObject($import_teamplayer,'injury_detail'));
			$p_teamplayer->set('injury_date_start',$this->_getDataFromObject($import_teamplayer,'injury_date_start'));
			$p_teamplayer->set('injury_date_end',$this->_getDataFromObject($import_teamplayer,'injury_date_end'));
			$p_teamplayer->set('suspension',$this->_getDataFromObject($import_teamplayer,'suspension'));
			$p_teamplayer->set('suspension_date',$this->_getDataFromObject($import_teamplayer,'suspension_date'));
			$p_teamplayer->set('suspension_end',$this->_getDataFromObject($import_teamplayer,'suspension_end'));
			$p_teamplayer->set('suspension_detail',$this->_getDataFromObject($import_teamplayer,'suspension_detail'));
			$p_teamplayer->set('susp_date_start',$this->_getDataFromObject($import_teamplayer,'susp_date_start'));
			$p_teamplayer->set('susp_date_end',$this->_getDataFromObject($import_teamplayer,'susp_date_end'));
			$p_teamplayer->set('away',$this->_getDataFromObject($import_teamplayer,'away'));
			$p_teamplayer->set('away_date',$this->_getDataFromObject($import_teamplayer,'away_date'));
			$p_teamplayer->set('away_end',$this->_getDataFromObject($import_teamplayer,'away_end'));
			$p_teamplayer->set('away_detail',$this->_getDataFromObject($import_teamplayer,'away_detail'));
			$p_teamplayer->set('away_date_start',$this->_getDataFromObject($import_teamplayer,'away_date_start'));
			$p_teamplayer->set('away_date_end',$this->_getDataFromObject($import_teamplayer,'away_date_end'));
			$p_teamplayer->set('published',1);

			if ($p_teamplayer->store()===false)
			{
				$my_text .= 'error on teamplayer import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importTeamPlayer<br />#$my_text#<br />#<pre>".print_r($p_teamplayer,true).'</pre>#';
				$this->_success_text['Importing teamplayer data:']=$my_text;
				return false;
			}
			else
			{
				$dPerson=$this->_getPersonName($p_teamplayer->person_id);
				$project_position_id = $p_teamplayer->project_position_id;
				if($project_position_id>0) {
					$query ='SELECT *
								FROM #__joomleague_project_position
								WHERE	id='.$project_position_id;
					$db->setQuery($query);
					$db->execute();
					$object=$db->loadObject();
					$position_id = $object->position_id;
					$dPosName=Text::_($this->_getObjectName('position',$position_id));
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf(	'Created new teamplayer data. Team: %1$s - Person: %2$s,%3$s - Position: %4$s',
									'</span><strong>'.$this->_getTeamName($p_teamplayer->projectteam_id).'</strong><span style="color:'.$this->storeSuccessColor.'">',
									'</span><strong>'.$dPerson->lastname,$dPerson->firstname.'</strong><span style="color:'.$this->storeSuccessColor.'">',
									"</span><strong>$dPosName</strong>");
					$my_text .= '<br />';
				} else {
					$dPosName='<span style="color:orange">'.Text::_('Has no position').'</span>';
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf(	'Created new teamplayer data. Team: %1$s - Person: %2$s,%3$s - Position: %4$s',
									'</span><strong>'.$this->_getTeamName($p_teamplayer->projectteam_id).'</strong><span style="color:'.$this->storeSuccessColor.'">',
									'</span><strong>'.$dPerson->lastname,$dPerson->firstname.'</strong><span style="color:'.$this->storeSuccessColor.'">',
									"</span><strong>$dPosName</strong>");
					$my_text .= '<br />';
				}
			}
			$insertID=$p_teamplayer->id;
			$this->_convertTeamPlayerID[$oldID]=$insertID;
		}
		$this->_success_text['Importing teamplayer data:']=$my_text;
		return true;
	}

	private function _importTeamStaff()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['teamstaff']) || count($this->_datas['teamstaff'])==0){return true;}

		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($dbpersonsid) || count($dbpersonsid)==0)){return true;}

		foreach ($this->_datas['teamstaff'] as $key => $teamstaff)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$p_teamstaff =  $this->getTable('TeamStaff');
			$import_teamstaff=$this->_datas['teamstaff'][$key];
			$oldID=$this->_getDataFromObject($import_teamstaff,'id');
			$oldProjectTeamID=$this->_getDataFromObject($import_teamstaff,'projectteam_id');
			$oldPersonID=$this->_getDataFromObject($import_teamstaff,'person_id');
			if (!isset($this->_convertProjectTeamID[$oldProjectTeamID]) ||
				!isset($this->_convertPersonID[$oldPersonID]))
			{
				$my_text .= '<span style="color:red">';
				$my_text .= Text::sprintf(	'Skipping import of TeamStaff-ID %1$s. Old-ProjectTeamID: %2$s - Old-PersonID: %3$s',
								"</span><strong>$oldID</strong><span style='color:red'>",
								"</span><strong>$oldProjectTeamID</strong><span style='color:red'>",
								"</span><strong>$oldPersonID</strong>").'<br />';
				continue;
			}
			$p_teamstaff->set('projectteam_id',$this->_convertProjectTeamID[$oldProjectTeamID]);
			$p_teamstaff->set('person_id',$this->_convertPersonID[$oldPersonID]);
			$oldPositionID=$this->_getDataFromObject($import_teamstaff,'project_position_id');
			if (isset($this->_convertProjectPositionID[$oldPositionID]))
			{
				$p_teamstaff->set('project_position_id',$this->_convertProjectPositionID[$oldPositionID]);
			}
			$p_teamstaff->set('active',$this->_getDataFromObject($import_teamstaff,'active'));
			$p_teamstaff->set('notes',$this->_getDataFromObject($import_teamstaff,'notes'));
			$p_teamstaff->set('injury',$this->_getDataFromObject($import_teamstaff,'injury'));
			$p_teamstaff->set('injury_date',$this->_getDataFromObject($import_teamstaff,'injury_date'));
			$p_teamstaff->set('injury_end',$this->_getDataFromObject($import_teamstaff,'injury_end'));
			$p_teamstaff->set('injury_detail',$this->_getDataFromObject($import_teamstaff,'injury_detail'));
			$p_teamstaff->set('injury_date_start',$this->_getDataFromObject($import_teamstaff,'injury_date_start'));
			$p_teamstaff->set('injury_date_end',$this->_getDataFromObject($import_teamstaff,'injury_date_end'));
			$p_teamstaff->set('suspension',$this->_getDataFromObject($import_teamstaff,'suspension'));
			$p_teamstaff->set('suspension_date',$this->_getDataFromObject($import_teamstaff,'suspension_date'));
			$p_teamstaff->set('suspension_end',$this->_getDataFromObject($import_teamstaff,'suspension_end'));
			$p_teamstaff->set('suspension_detail',$this->_getDataFromObject($import_teamstaff,'suspension_detail'));
			$p_teamstaff->set('susp_date_start',$this->_getDataFromObject($import_teamstaff,'susp_date_start'));
			$p_teamstaff->set('susp_date_end',$this->_getDataFromObject($import_teamstaff,'susp_date_end'));
			$p_teamstaff->set('away',$this->_getDataFromObject($import_teamstaff,'away'));
			$p_teamstaff->set('away_date',$this->_getDataFromObject($import_teamstaff,'away_date'));
			$p_teamstaff->set('away_end',$this->_getDataFromObject($import_teamstaff,'away_end'));
			$p_teamstaff->set('away_detail',$this->_getDataFromObject($import_teamstaff,'away_detail'));
			$p_teamstaff->set('away_date_start',$this->_getDataFromObject($import_teamstaff,'away_date_start'));
			$p_teamstaff->set('away_date_end',$this->_getDataFromObject($import_teamstaff,'away_date_end'));
			$p_teamstaff->set('picture',$this->_getDataFromObject($import_teamstaff,'picture'));
			$p_teamstaff->set('extended',$this->_getDataFromObject($import_teamstaff,'extended'));
			$p_teamstaff->set('published',1);

			if ($p_teamstaff->store()===false)
			{
				$my_text .= 'error on teamstaff import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importTeamStaff<br />#$my_text#<br />#<pre>".print_r($p_teamstaff,true).'</pre>#';
				$this->_success_text['Importing teamstaff data:']=$my_text;
				return false;
			}
			else
			{
				$dPerson=$this->_getPersonName($p_teamstaff->person_id);
				$project_position_id = $p_teamstaff->project_position_id;
				if($project_position_id>0) {
					$query ='SELECT * FROM #__joomleague_project_position WHERE id='.$project_position_id;
					$db->setQuery($query);
					$db->execute();
					$object=$db->loadObject();
					$position_id = $object->position_id;
					$dPosName=Text::_($this->_getObjectName('position',$position_id));
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf(	'Created new teamstaff data. Team: %1$s - Person: %2$s,%3$s - Position: %4$s',
									'</span><strong>'.$this->_getTeamName($p_teamstaff->projectteam_id).'</strong><span style="color:'.$this->storeSuccessColor.'">',
									'</span><strong>'.$dPerson->lastname,
									$dPerson->firstname.'</strong><span style="color:'.$this->storeSuccessColor.'">',
									"</span><strong>$dPosName</strong>");
					$my_text .= '<br />';
				} else {
					$dPosName='<span style="color:orange">'.Text::_('Has no position').'</span>';
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf(	'Created new teamstaff data. Team: %1$s - Person: %2$s,%3$s - Position: %4$s',
									'</span><strong>'.$this->_getTeamName($p_teamstaff->projectteam_id).'</strong><span style="color:'.$this->storeSuccessColor.'">',
									'</span><strong>'.$dPerson->lastname,$dPerson->firstname.'</strong><span style="color:'.$this->storeSuccessColor.'">',
									"</span><strong>$dPosName</strong>");
					$my_text .= '<br />';
				}
			}
			$insertID=$p_teamstaff->id;
			$this->_convertTeamStaffID[$oldID]=$insertID;
		}
		$this->_success_text['Importing teamstaff data:']=$my_text;
		return true;
	}

	private function _importTeamTraining()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['teamtraining']) || count($this->_datas['teamtraining'])==0){return true;}

		foreach ($this->_datas['teamtraining'] as $key => $teamtraining)
		{
			$p_teamtraining = $this->getTable('TeamTrainingdata');
			$import_teamtraining=$this->_datas['teamtraining'][$key];
			$oldID=$this->_getDataFromObject($import_teamtraining,'id');

			//This has to be fixed after we changed the field name into projectteam_id
			$p_teamtraining->set('project_team_id',$this->_convertProjectTeamID[$this->_getDataFromObject($import_teamtraining,'project_team_id')]);
			$p_teamtraining->set('project_id',$this->_project_id);
			// This has to be fixed if we really should use this field. Normally it should be deleted in the table
			$p_teamtraining->set('team_id',$this->_getDataFromObject($import_teamtraining,'team_id'));
			$p_teamtraining->set('dayofweek',$this->_getDataFromObject($import_teamtraining,'dayofweek'));
			$p_teamtraining->set('time_start',$this->_getDataFromObject($import_teamtraining,'time_start'));
			$p_teamtraining->set('time_end',$this->_getDataFromObject($import_teamtraining,'time_end'));
			$p_teamtraining->set('place',$this->_getDataFromObject($import_teamtraining,'place'));
			$p_teamtraining->set('notes',$this->_getDataFromObject($import_teamtraining,'notes'));

			if ($p_teamtraining->store()===false)
			{
				$my_text .= 'error on teamtraining import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importTeamTraining<br />#$my_text#<br />#<pre>".print_r($p_teamtraining,true).'</pre>#';
				$this->_success_text['Importing teamtraining data:']=$my_text;
				return false;
			}
			else
			{
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf(	'Created new teamtraining data. Team: [%1$s]',
								'</span><strong>'.$this-> _getTeamName($p_teamtraining->project_team_id).'</strong>');
				$my_text .= '<br />';
			}
		}
		$this->_success_text['Importing teamtraining data:']=$my_text;
		return true;
	}

	private function _importRounds()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['round']) || count($this->_datas['round'])==0){return true;}

		foreach ($this->_datas['round'] as $key => $round)
		{
			$p_round = $this->getTable('Round');
			$oldId=(int)$round->id;
			$name=trim($this->_getDataFromObject($round,'name'));
			$alias=trim($this->_getDataFromObject($round,'alias'));
			// if the roundcode field is empty,it is an old .jlg-Import file
			$roundnumber=$this->_getDataFromObject($round,'roundcode');
			if (empty($roundnumber))
			{
				$roundnumber=$this->_getDataFromObject($round,'matchcode');
			}
			$p_round->set('roundcode',$roundnumber);
			$p_round->set('name',$name);
			if ($alias!='')
			{
				$p_round->set('alias',$alias);
			}
			else
			{
				$p_round->set('alias',OutputFilter::stringURLSafe($name));
			}
			$p_round->set('round_date_first',$this->_getDataFromObject($round,'round_date_first'));
			$round_date_last=trim($this->_getDataFromObject($round,'round_date_last'));
			if (($round_date_last=='') || ($round_date_last=='0000-00-00'))
			{
				$round_date_last=$this->_getDataFromObject($round,'round_date_first');
			}
			$p_round->set('round_date_last',$round_date_last);
			$p_round->set('project_id',$this->_project_id);
			if ($p_round->store()===false)
			{
				$my_text .= 'error on round import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importRounds<br />#$my_text#<br />#<pre>".print_r($p_round,true).'</pre>#';
				$this->_success_text['Importing round data:']=$my_text;
				return false;
			}
			else
			{
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf('Created new round: %1$s',"</span><strong>$name</strong>");
				$my_text .= '<br />';
			}
			$insertID=$p_round->id; 
			$this->_convertRoundID[$oldId]=$insertID;
		}
		$this->_success_text['Importing round data:']=$my_text;
		return true;
	}

	private function _importMatches()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['match']) || count($this->_datas['match'])==0){return true;}

		if (!isset($this->_datas['team']) || count($this->_datas['team'])==0){return true;}
		if ((!isset($this->_newteams) || count($this->_newteams)==0) &&
			(!isset($dbteamsid) || count($dbteamsid)==0)){return true;}

		foreach ($this->_datas['match'] as $key => $match)
		{
			$p_match = $this->getTable('Match');
			$oldId=(int)$match->id;
			if ($this->import_version=='NEW')
			{
                if(!isset($this->_convertRoundID[$this->_getDataFromObject($match,'round_id')]) ||
                   !isset($this->_convertProjectTeamID[intval($this->_getDataFromObject($match,'projectteam1_id'))])) {
                    continue;
                }
				$p_match->set('round_id',$this->_convertRoundID[$this->_getDataFromObject($match,'round_id')]);
				$p_match->set('match_number',$this->_getDataFromObject($match,'match_number'));

				if ($match->projectteam1_id > 0)
				{
					$team1=$this->_convertProjectTeamID[intval($this->_getDataFromObject($match,'projectteam1_id'))];
				}
				else
				{
					$team1=0;
				}
				$p_match->set('projectteam1_id',$team1);

				if ($match->projectteam2_id > 0)
				{
                    if(isset($this->_convertProjectTeamID[intval($this->_getDataFromObject($match,'projectteam2_id'))])) {
                        $team2=$this->_convertProjectTeamID[intval($this->_getDataFromObject($match,'projectteam2_id'))];
                    } else {
                        continue;
                    }
				}
				else
				{
					$team2=0;
				}
				$p_match->set('projectteam2_id',$team2);

				if (!empty($this->_convertPlaygroundID))
				{
					if (array_key_exists((int)$this->_getDataFromObject($match,'playground_id'),$this->_convertPlaygroundID))
					{
						$p_match->set('playground_id',$this->_convertPlaygroundID[$this->_getDataFromObject($match,'playground_id')]);
					}
					else
					{
						$p_match->set('playground_id',0);
					}
				}
				if ($p_match->playground_id ==0)
				{
					$p_match->set('playground_id',NULL);
				}

				$p_match->set('match_date',$this->_getDataFromObject($match,'match_date'));

				$p_match->set('time_present',$this->_getDataFromObject($match,'time_present'));

				$team1_result=$this->_getDataFromObject($match,'team1_result');
				if (isset($team1_result) && ($team1_result !=NULL)) { $p_match->set('team1_result',$team1_result); }

				$team2_result=$this->_getDataFromObject($match,'team2_result');
				if (isset($team2_result) && ($team2_result !=NULL)) { $p_match->set('team2_result',$team2_result); }

				$team1_bonus=$this->_getDataFromObject($match,'team1_bonus');
				if (isset($team1_bonus) && ($team1_bonus !=NULL)) { $p_match->set('team1_bonus',$team1_bonus); }

				$team2_bonus=$this->_getDataFromObject($match,'team2_bonus');
				if (isset($team2_bonus) && ($team2_bonus !=NULL)) { $p_match->set('team2_bonus',$team2_bonus); }

				$team1_legs=$this->_getDataFromObject($match,'team1_legs');
				if (isset($team1_legs) && ($team1_legs !=NULL)) { $p_match->set('team1_legs',$team1_legs); }

				$team2_legs=$this->_getDataFromObject($match,'team2_legs');
				if (isset($team2_legs) && ($team2_legs !=NULL)) { $p_match->set('team2_legs',$team2_legs); }

				$p_match->set('team1_result_split',$this->_getDataFromObject($match,'team1_result_split'));
				$p_match->set('team2_result_split',$this->_getDataFromObject($match,'team2_result_split'));
				$p_match->set('match_result_type',$this->_getDataFromObject($match,'match_result_type'));

				$team1_result_ot=$this->_getDataFromObject($match,'team1_result_ot');
				if (isset($team1_result_ot) && ($team1_result_ot !=NULL)) { $p_match->set('team1_result_ot',$team1_result_ot); }

				$team2_result_ot=$this->_getDataFromObject($match,'team2_result_ot');
				if (isset($team2_result_ot) && ($team2_result_ot !=NULL)) { $p_match->set('team2_result_ot',$team2_result_ot); }

				$team1_result_so=$this->_getDataFromObject($match,'team1_result_so');
				if (isset($team1_result_so) && ($team1_result_so !=NULL)) { $p_match->set('team1_result_so',$team1_result_so); }

				$team2_result_so=$this->_getDataFromObject($match,'team2_result_so');
				if (isset($team2_result_so) && ($team2_result_so !=NULL)) { $p_match->set('team2_result_so',$team2_result_so); }

				$p_match->set('alt_decision',$this->_getDataFromObject($match,'alt_decision'));

				$team1_result_decision=$this->_getDataFromObject($match,'team1_result_decision');
				if (isset($team1_result_decision) && ($team1_result_decision !=NULL)) { $p_match->set('team1_result_decision',$team1_result_decision); }

				$team2_result_decision=$this->_getDataFromObject($match,'team2_result_decision');
				if (isset($team2_result_decision) && ($team2_result_decision !=NULL)) { $p_match->set('team2_result_decision',$team2_result_decision); }

				$p_match->set('decision_info',$this->_getDataFromObject($match,'decision_info'));
				$p_match->set('cancel',$this->_getDataFromObject($match,'cancel'));
				$p_match->set('cancel_reason',$this->_getDataFromObject($match,'cancel_reason'));
				$p_match->set('count_result',$this->_getDataFromObject($match,'count_result'));
				$p_match->set('crowd',$this->_getDataFromObject($match,'crowd'));
				$p_match->set('summary',$this->_getDataFromObject($match,'summary'));
				$p_match->set('show_report',$this->_getDataFromObject($match,'show_report'));
				$p_match->set('preview',$this->_getDataFromObject($match,'preview'));
				$p_match->set('match_result_detail',$this->_getDataFromObject($match,'match_result_detail'));
				$p_match->set('new_match_id',$this->_getDataFromObject($match,'new_match_id'));
				$p_match->set('old_match_id',$this->_getDataFromObject($match,'old_match_id'));
				$p_match->set('extended',$this->_getDataFromObject($match,'extended'));
				$p_match->set('published',$this->_getDataFromObject($match,'published'));
			}
			else // ($this->import_version=='OLD')
			{
				$p_match->set('round_id',$this->_convertRoundID[intval($match->round_id)]);
				$p_match->set('match_number',$this->_getDataFromObject($match,'match_number'));

				if ($match->matchpart1 > 0)
				{
					$team1=$this->_convertTeamID[intval($match->matchpart1)];
					$p_match->set('projectteam1_id',$this->_convertProjectTeamID[$team1]);
				}
				else
				{
					$p_match->set('projectteam1_id',0);
				}

				if ($match->matchpart2 > 0)
				{
					$team2=$this->_convertTeamID[intval($match->matchpart2)];
					$p_match->set('projectteam2_id',$this->_convertProjectTeamID[$team2]);
				}
				else
				{
					$p_match->set('projectteam2_id',0);
				}

				$matchdate=(string)$match->match_date;
				$p_match->set('match_date',$matchdate);

				$team1_result=$this->_getDataFromObject($match,'matchpart1_result');
				if (isset($team1_result) && ($team1_result !=NULL)) { $p_match->set('team1_result',$team1_result); }

				$team2_result=$this->_getDataFromObject($match,'matchpart2_result');
				if (isset($team2_result) && ($team2_result !=NULL)) { $p_match->set('team2_result',$team2_result); }

				$team1_bonus=$this->_getDataFromObject($match,'matchpart1_bonus');
				if (isset($team1_bonus) && ($team1_bonus !=NULL)) { $p_match->set('team1_bonus',$team1_bonus); }

				$team2_bonus=$this->_getDataFromObject($match,'matchpart2_bonus');
				if (isset($team2_bonus) && ($team2_bonus !=NULL)) { $p_match->set('team2_bonus',$team2_bonus); }

				$team1_legs=$this->_getDataFromObject($match,'matchpart1_legs');
				if (isset($team1_legs) && ($team1_legs !=NULL)) { $p_match->set('team1_legs',$team1_legs); }

				$team2_legs=$this->_getDataFromObject($match,'matchpart2_legs');
				if (isset($team2_legs) && ($team2_legs !=NULL)) { $p_match->set('team2_legs',$team2_legs); }

				$p_match->set('team1_result_split',$this->_getDataFromObject($match,'matchpart1_result_split'));//NULL
				$p_match->set('team2_result_split',$this->_getDataFromObject($match,'matchpart2_result_split'));//NULL
				$p_match->set('match_result_type',$this->_getDataFromObject($match,'match_result_type'));

				$team1_result_ot=$this->_getDataFromObject($match,'matchpart1_result_ot');
				if (isset($team1_result_ot) && ($team1_result_ot !=NULL)) { $p_match->set('team1_result_ot',$team1_result_ot); }

				$team2_result_ot=$this->_getDataFromObject($match,'matchpart2_result_ot');
				if (isset($team2_result_ot) && ($team2_result_ot !=NULL)) { $p_match->set('team2_result_ot',$team2_result_ot); }

				$p_match->set('alt_decision',$this->_getDataFromObject($match,'alt_decision'));

				$team1_result_decision=$this->_getDataFromObject($match,'matchpart1_result_decision');
				if (isset($team1_result_decision) && ($team1_result_decision !=NULL)) { $p_match->set('team1_result_decision',$team1_result_decision); }

				$team2_result_decision=$this->_getDataFromObject($match,'matchpart2_result_decision');
				if (isset($team2_result_decision) && ($team2_result_decision !=NULL)) { $p_match->set('team2_result_decision',$team2_result_decision); }

				$p_match->set('decision_info',$this->_getDataFromObject($match,'decision_info'));
				$p_match->set('count_result',$this->_getDataFromObject($match,'count_result'));
				$p_match->set('crowd',$this->_getDataFromObject($match,'crowd'));
				$p_match->set('summary',$this->_getDataFromObject($match,'summary'));
				$p_match->set('show_report',$this->_getDataFromObject($match,'show_report'));
				$p_match->set('match_result_detail',$this->_getDataFromObject($match,'match_result_detail'));
				$p_match->set('published',$this->_getDataFromObject($match,'published'));
			}

			if ($p_match->store()===false)
			{
				$my_text .= 'error on match import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importMatches<br />#$my_text#<br />#<pre>".print_r($p_match,true).'</pre>#';
				$this->_success_text['Importing match data:']=$my_text;
				return false;
			}
			else
			{
				if ($this->import_version=='NEW')
				{
					if ($match->projectteam1_id > 0)
					{
						$teamname1=$this->_getTeamName($p_match->projectteam1_id);
					}
					else
					{
						$teamname1='<span style="color:orange">'.Text::_('Home-Team not asigned').'</span>';
					}
					if ($match->projectteam2_id > 0)
					{
						$teamname2=$this->_getTeamName($p_match->projectteam2_id);
					}
					else
					{
						$teamname2='<span style="color:orange">'.Text::_('Guest-Team not asigned').'</span>';
					}

					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf(	'Added to round: %1$s / Match: %2$s - %3$s',
									'</span><strong>'.$this->_getRoundName($this->_convertRoundID[$this->_getDataFromObject($match,'round_id')]).'</strong><span style="color:'.$this->storeSuccessColor.'">',
									"</span><strong>$teamname1</strong>",
									"<strong>$teamname2</strong>");
					$my_text .= '<br />';
				}

				if ($this->import_version=='OLD')
				{
					if ($match->matchpart1 > 0)
					{
						$teamname1=$this->_getTeamName2($this->_convertTeamID[intval($match->matchpart1)]);
					}
					else
					{
						$teamname1='<span style="color:orange">'.Text::_('Home-Team not asigned').'</span>';
					}
					if ($match->matchpart2 > 0)
					{
						$teamname2=$this->_getTeamName2($this->_convertTeamID[intval($match->matchpart2)]);
					}
					else
					{
						$teamname2='<span style="color:orange">'.Text::_('Guest-Team not asigned').'</span>';
					}

					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf(	'Added to round: %1$s / Match: %2$s - %3$s',
									'</span><strong>'.$this->_getRoundName($this->_convertRoundID[$this->_getDataFromObject($match,'round_id')]).'</strong><span style="color:'.$this->storeSuccessColor.'">',
									"</span><strong>$teamname1</strong>",
									"<strong>$teamname2</strong>");
					$my_text .= '<br />';
				}
			}

			$insertID=$p_match->id;
			$this->_convertMatchID[$oldId]=$insertID;
		}
		$this->_success_text['Importing match data:']=$my_text;
		return true;
	}

	private function _importMatchPlayer()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['matchplayer']) || count($this->_datas['matchplayer'])==0){return true;}

		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($dbpersonsid) || count($dbpersonsid)==0)){return true;}

		foreach ($this->_datas['matchplayer'] as $key => $matchplayer)
		{
			$import_matchplayer=$this->_datas['matchplayer'][$key];
			$oldID=$this->_getDataFromObject($import_matchplayer,'id');
			$p_matchplayer = $this->getTable('MatchPlayer');
			$oldMatchID=$this->_getDataFromObject($import_matchplayer,'match_id');
			$oldTeamPlayerID=$this->_getDataFromObject($import_matchplayer,'teamplayer_id');
			if (!isset($this->_convertMatchID[$oldMatchID]) ||
				!isset($this->_convertTeamPlayerID[$oldTeamPlayerID]))
			{
				$my_text .= '<span style="color:red">';
				$my_text .= Text::sprintf(	'Skipping import of MatchPlayer-ID [%1$s]. Old-MatchID: [%2$s] - Old-TeamPlayerID: [%3$s]',
								"</span><strong>$oldID</strong><span style='color:red'>",
								"</span><strong>$oldMatchID</strong><span style='color:red'>",
								"</span><strong>$oldTeamPlayerID</strong>").'<br />';
				continue;
			}
			$p_matchplayer->set('match_id',$this->_convertMatchID[$oldMatchID]);
			$p_matchplayer->set('teamplayer_id',$this->_convertTeamPlayerID[$oldTeamPlayerID]);
			$oldPositionID=$this->_getDataFromObject($import_matchplayer,'project_position_id');
			if (isset($this->_convertProjectPositionID[$oldPositionID]))
			{
				$p_matchplayer->set('project_position_id',$this->_convertProjectPositionID[$oldPositionID]);
			}
			$p_matchplayer->set('came_in',$this->_getDataFromObject($import_matchplayer,'came_in'));
			if ($import_matchplayer->in_for > 0)
			{
				$oldTeamPlayerID=$this->_getDataFromObject($import_matchplayer,'in_for');
				if (isset($this->_convertTeamPlayerID[$oldTeamPlayerID]))
				{
					$p_matchplayer->set('in_for',$this->_convertTeamPlayerID[$oldTeamPlayerID]);
				}
			}
			$p_matchplayer->set('out',$this->_getDataFromObject($import_matchplayer,'out'));
			$p_matchplayer->set('in_out_time',$this->_getDataFromObject($import_matchplayer,'in_out_time'));
			$p_matchplayer->set('ordering',$this->_getDataFromObject($import_matchplayer,'ordering'));

			if ($p_matchplayer->store()===false)
			{
				$my_text .= 'error on matchplayer import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importMatchPlayer<br />#$my_text#<br />#<pre>".print_r($p_matchplayer,true).'</pre>#';
				$this->_success_text['Importing matchplayer data:']=$my_text;
				return false;
			}
			else
			{
				$dPerson=$this->_getPersonFromTeamPlayer($p_matchplayer->teamplayer_id);
				$dPosName=(($p_matchplayer->project_position_id==0) ?
							'<span style="color:orange">'.Text::_('Has no position').'</span>' :
							$this->_getProjectPositionName($p_matchplayer->project_position_id));
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf(	'Created new matchplayer data. MatchID: %1$s - Player: %2$s,%3$s - Position: %4$s',
								'</span><strong>'.$p_matchplayer->match_id.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$dPerson->lastname,
								$dPerson->firstname.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								"</span><strong>$dPosName</strong>");
				$my_text .= '<br />';
			}
		}
		$this->_success_text['Importing matchplayer data:']=$my_text;
		return true;
	}

	private function _importMatchStaff()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['matchstaff']) || count($this->_datas['matchstaff'])==0){return true;}

		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($dbpersonsid) || count($dbpersonsid)==0)){return true;}

		foreach ($this->_datas['matchstaff'] as $key => $matchstaff)
		{
			$import_matchstaff=$this->_datas['matchstaff'][$key];
			$oldID=$this->_getDataFromObject($import_matchstaff,'id');
			$p_matchstaff = $this->getTable('MatchStaff');
			$oldMatchID=$this->_getDataFromObject($import_matchstaff,'match_id');
			$oldTeamStaffID=$this->_getDataFromObject($import_matchstaff,'team_staff_id');
			if (!isset($this->_convertMatchID[$oldMatchID]) ||
				!isset($this->_convertTeamStaffID[$oldTeamStaffID]))
			{
				$my_text .= '<span style="color:red">';
				$my_text .= Text::sprintf(	'Skipping import of MatchStaff-ID [%1$s]. Old-MatchID: [%2$s] - Old-StaffID: [%3$s]',
								"</span><strong>$oldID</strong><span style='color:red'>",
								"</span><strong>$oldMatchID</strong><span style='color:red'>",
								"</span><strong>$oldTeamStaffID</strong>").'<br />';
				continue;
			}
			$p_matchstaff->set('match_id',$this->_convertMatchID[$oldMatchID]);
			$p_matchstaff->set('team_staff_id',$this->_convertTeamStaffID[$oldTeamStaffID]);
			$oldPositionID=$this->_getDataFromObject($import_matchstaff,'project_position_id');
			if (isset($this->_convertProjectPositionID[$oldPositionID]))
			{
				$p_matchstaff->set('project_position_id',$this->_convertProjectPositionID[$oldPositionID]);
			}
			$p_matchstaff->set('ordering',$this->_getDataFromObject($import_matchstaff,'ordering'));
			if ($p_matchstaff->store()===false)
			{
				$my_text .= 'error on matchstaff import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importMatchStaff<br />#$my_text#<br />#<pre>".print_r($p_matchstaff,true).'</pre>#';
				$this->_success_text['Importing matchstaff data:']=$my_text;
				return false;
			}
			else
			{
				$dPerson=$this->_getPersonFromTeamStaff($p_matchstaff->team_staff_id);
				$dPosName=(($p_matchstaff->project_position_id==0) ?
							'<span style="color:orange">'.Text::_('Has no position').'</span>' :
							$this->_getProjectPositionName($p_matchstaff->project_position_id));
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf(	'Created new matchstaff data. MatchID: %1$s - Staff: %2$s,%3$s - Position: %4$s',
								'</span><strong>'.$p_matchstaff->match_id.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$dPerson->lastname,
								$dPerson->firstname.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								"</span><strong>$dPosName</strong>");
				$my_text .= '<br />';
			}
		}
		$this->_success_text['Importing matchstaff data:']=$my_text;
		return true;
	}

	private function _importMatchReferee()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['matchreferee']) || count($this->_datas['matchreferee'])==0){return true;}

		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($dbpersonsid) || count($dbpersonsid)==0)){return true;}

		foreach ($this->_datas['matchreferee'] as $key => $matchreferee)
		{
			$import_matchreferee=$this->_datas['matchreferee'][$key];
			$oldID=$this->_getDataFromObject($import_matchreferee,'id');
			$p_matchreferee = $this->getTable('MatchReferee');
			$oldMatchID=$this->_getDataFromObject($import_matchreferee,'match_id');
			$oldProjectRefereeID=$this->_getDataFromObject($import_matchreferee,'project_referee_id');
			if (!isset($this->_convertMatchID[$oldMatchID]) ||
				!isset($this->_convertProjectRefereeID[$oldProjectRefereeID]))
			{
				$my_text .= '<span style="color:red">';
				$my_text .= Text::sprintf(	'Skipping import of MatchReferee-ID [%1$s]. Old-MatchID: [%2$s] - Old-RefereeID: [%3$s]',
								"</span><strong>$oldID</strong><span style='color:red'>",
								"</span><strong>$oldMatchID</strong><span style='color:red'>",
								"</span><strong>$oldProjectRefereeID</strong>").'<br />';
				continue;
			}
			$p_matchreferee->set('match_id',$this->_convertMatchID[$oldMatchID]);
			$p_matchreferee->set('project_referee_id',$this->_convertProjectRefereeID[$oldProjectRefereeID]);
			$oldPositionID=$this->_getDataFromObject($import_matchreferee,'project_position_id');
			if (isset($this->_convertProjectPositionID[$oldPositionID]))
			{
				$p_matchreferee->set('project_position_id',$this->_convertProjectPositionID[$oldPositionID]);
			}
			$p_matchreferee->set('ordering',$this->_getDataFromObject($import_matchreferee,'ordering'));
			if ($p_matchreferee->store()===false)
			{
				$my_text .= 'error on matchreferee import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importMatchReferee<br />#$my_text#<br />#<pre>".print_r($p_matchreferee,true).'</pre>#';
				$this->_success_text['Importing matchreferee data:']=$my_text;
				return false;
			}
			else
			{
				$dPerson=$this->_getPersonFromProjectReferee($p_matchreferee->project_referee_id);
				$dPosName=(($p_matchreferee->project_position_id==0) ?
							'<span style="color:orange">'.Text::_('Has no position').'</span>' :
							$this->_getProjectPositionName($p_matchreferee->project_position_id));
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf(	'Created new matchreferee data. MatchID: %1$s - Referee: %2$s,%3$s - Position: %4$s',
								'</span><strong>'.$p_matchreferee->match_id.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$dPerson->lastname,
								$dPerson->firstname.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								"</span><strong>$dPosName</strong>");
				$my_text .= '<br />';
			}
		}
		$this->_success_text['Importing matchreferee data:']=$my_text;
		return true;
	}

	private function _importMatchEvent()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['matchevent']) || count($this->_datas['matchevent'])==0){return true;}

		if (!isset($this->_datas['person']) || count($this->_datas['person'])==0){return true;}
		if ((!isset($this->_newpersonsid) || count($this->_newpersonsid)==0) &&
			(!isset($dbpersonsid) || count($dbpersonsid)==0)){return true;}
		if (!isset($this->_datas['event']) || count($this->_datas['event'])==0){return true;}
		if ((!isset($this->_neweventsid) || count($this->_neweventsid)==0) &&
			(!isset($dbeventsid) || count($dbeventsid)==0)){return true;}

		foreach ($this->_datas['matchevent'] as $key => $matchevent)
		{
			$import_matchevent=$this->_datas['matchevent'][$key];
			$oldID=$this->_getDataFromObject($import_matchevent,'id');

			$p_matchevent = $this->getTable('MatchEvent');
            if(!isset($this->_convertMatchID[$this->_getDataFromObject($import_matchevent,'match_id')]) ||
               !isset($this->_convertProjectTeamID[$this->_getDataFromObject($import_matchevent,'projectteam_id')]) ||
               !isset($this->_convertTeamPlayerID[$this->_getDataFromObject($import_matchevent,'teamplayer_id')]))
            {
                continue;
            }
			$p_matchevent->set('match_id',$this->_convertMatchID[$this->_getDataFromObject($import_matchevent,'match_id')]);
			$p_matchevent->set('projectteam_id',$this->_convertProjectTeamID[$this->_getDataFromObject($import_matchevent,'projectteam_id')]);
			if ($import_matchevent->teamplayer_id > 0)
			{
				$p_matchevent->set('teamplayer_id',$this->_convertTeamPlayerID[$this->_getDataFromObject($import_matchevent,'teamplayer_id')]);
			}
			else
			{
				$p_matchevent->set('teamplayer_id',0);
			}
			if ($import_matchevent->teamplayer_id2 > 0)
			{
				$p_matchevent->set('teamplayer_id2',$this->_convertTeamPlayerID[$this->_getDataFromObject($import_matchevent,'teamplayer_id2')]);
			}
			else
			{
				$p_matchevent->set('teamplayer_id2',0);
			}
			$p_matchevent->set('event_time',$this->_getDataFromObject($import_matchevent,'event_time'));
			$p_matchevent->set('event_type_id',$this->_convertEventID[$this->_getDataFromObject($import_matchevent,'event_type_id')]);
			$p_matchevent->set('event_sum',$this->_getDataFromObject($import_matchevent,'event_sum'));
			$p_matchevent->set('notice',$this->_getDataFromObject($import_matchevent,'notice'));
			$p_matchevent->set('notes',$this->_getDataFromObject($import_matchevent,'notes'));

			if ($p_matchevent->store()===false)
			{
				$my_text .= 'error on matchevent import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importMatchEvent<br />#$my_text#<br />#<pre>".print_r($p_matchevent,true).'</pre>#';
				$this->_success_text['Importing matchevent data:']=$my_text;
				return false;
			}
			else
			{
				$dPerson=$this->_getPersonFromTeamPlayer($p_matchevent->teamplayer_id);
				$dEventName=(($p_matchevent->event_type_id==0) ?
							'<span style="color:orange">'.Text::_('Has no event').'</span>' :
							Text::_($this->_getObjectName('eventtype',$p_matchevent->event_type_id)));
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf(	'Created new matchevent data. MatchID: %1$s - Player: %2$s,%3$s - Eventtime: %4$s - Event: %5$s',
								'</span><strong>'.$p_matchevent->match_id.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$dPerson->lastname,
								$dPerson->firstname.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$p_matchevent->event_time.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								"</span><strong>$dEventName</strong>");
				$my_text .= '<br />';
			}
		}
		$this->_success_text['Importing matchevent data:']=$my_text;
		return true;
	}

	private function _importPositionStatistic()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['positionstatistic']) || count($this->_datas['positionstatistic'])==0){return true;}

		if ((!isset($this->_newpositionsid) || count($this->_newpositionsid)==0) &&
			(!isset($dbpositionsid) || count($dbpositionsid)==0)){return true;}
		if ((!isset($this->_newstatisticsid) || count($this->_newstatisticsid)==0) &&
			(!isset($dbstatisticsid) || count($dbstatisticsid)==0)){return true;}

		if (!isset($this->_datas['statistic']) || count($this->_datas['statistic'])==0)
		{
			$my_text .= '<span style="color:red">';
			$my_text .= Text::sprintf('Warning: Skipped %1$s records for position statistic data because there is no statistic data included!',count($this->_datas['positionstatistic']));
			$my_text .= '</span>';
			$this->_success_text['Importing position statistic data:']=$my_text;
			return true;
		}

		if (!isset($this->_datas['position']) || count($this->_datas['position'])==0)
		{
			$my_text .= '<span style="color:red">';
			$my_text .= Text::sprintf('Warning: Skipped %1$s records for position statistic data because there is no position data included!',count($this->_datas['positionstatistic']));
			$my_text .= '</span>';
			$this->_success_text['Importing position statistic data:']=$my_text;
			return true;
		}

		foreach ($this->_datas['positionstatistic'] as $key => $positionstatistic)
		{
			$import_positionstatistic=$this->_datas['positionstatistic'][$key];
			$oldID=$this->_getDataFromObject($import_positionstatistic,'id');

			$p_positionstatistic = $this->getTable('PositionStatistic');

			$p_positionstatistic->set('position_id',$this->_convertPositionID[$this->_getDataFromObject($import_positionstatistic,'position_id')]);
			$p_positionstatistic->set('statistic_id',$this->_convertStatisticID[$this->_getDataFromObject($import_positionstatistic,'statistic_id')]);
			//$p_positionstatistic->set('ordering',$this->_getDataFromObject($import_positionstatistic,'ordering'));

			$query ="SELECT id
				FROM #__joomleague_position_statistic
				WHERE	position_id='$p_positionstatistic->position_id' AND
					statistic_id='$p_positionstatistic->statistic_id'";
			$db->setQuery($query);
			$db->execute();
			if ($object=$db->loadObject())
			{
				$my_text .= '<span style="color:'.$this->existingInDbColor.'">';
				$my_text .= Text::sprintf(	'Using existing positionstatistic data. Position: %1$s - Statistic: %2$s',
								'</span><strong>'.$this->_getObjectName('position',$p_positionstatistic->position_id).'</strong><span style="color:'.$this->existingInDbColor.'">',
								'</span><strong>'.$this->_getObjectName('statistic',$p_positionstatistic->statistic_id).'</strong>');
				$my_text .= '<br />';
			}
			else
			{
				if ($p_positionstatistic->store()===false)
				{
					$my_text .= 'error on positionstatistic import: ';
					$my_text .= $oldID;
					$my_text .= "<br />Error: _importPositionStatistic<br />#$my_text#<br />#<pre>".print_r($p_positionstatistic,true).'</pre>#';
					$this->_success_text['Importing position statistic data:']=$my_text;
					return false;
				}
				else
				{
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf(	'Created new position statistic data. Position: %1$s - Statistic: %2$s',
									'</span><strong>'.$this->_getObjectName('position',$p_positionstatistic->position_id).'</strong><span style="color:'.$this->storeSuccessColor.'">',
									'</span><strong>'.$this->_getObjectName('statistic',$p_positionstatistic->statistic_id).'</strong>');
					$my_text .= '<br />';
				}
			}
		}
		$this->_success_text['Importing position statistic data:']=$my_text;
		return true;
	}

	private function _importMatchStaffStatistic()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['matchstaffstatistic']) || count($this->_datas['matchstaffstatistic'])==0){return true;}

		if ((!isset($this->_newstatisticsid) || count($this->_newstatisticsid)==0) &&
			(!isset($dbstatisticsid) || count($dbstatisticsid)==0)){return true;}

		if (!isset($this->_datas['statistic']) || count($this->_datas['statistic'])==0)
		{
			$my_text .= '<span style="color:red">';
			$my_text .= Text::sprintf('Warning: Skipped %1$s records for match staff statistic data because there is no statistic data included!',count($this->_datas['matchstaffstatistic']));
			$my_text .= '</span>';
			$this->_success_text['Importing match staff statistic data:']=$my_text;
			return true;
		}
		if (!isset($this->_datas['match']) || count($this->_datas['match'])==0)
		{
			$my_text .= '<span style="color:red">';
			$my_text .= Text::sprintf('Warning: Skipped %1$s records for match statistic data because there is no match data included!',count($this->_datas['matchstaffstatistic']));
			$my_text .= '</span>';
			$this->_success_text['Importing match staff statistic data:']=$my_text;
			return true;
		}
		if (!isset($this->_datas['projectteam']) || count($this->_datas['projectteam'])==0)
		{
			$my_text .= '<span style="color:red">';
			$my_text .= Text::sprintf('Warning: Skipped %1$s records for match statistic data because there is no projectteam data included!',count($this->_datas['matchstaffstatistic']));
			$my_text .= '</span>';
			$this->_success_text['Importing match staff statistic data:']=$my_text;
			return true;
		}
		if (!isset($this->_datas['teamstaff']) || count($this->_datas['teamstaff'])==0)
		{
			$my_text .= '<span style="color:red">';
			$my_text .= Text::sprintf('Warning: Skipped %1$s records for match statistic data because there is no teamstaff data included!',count($this->_datas['matchstaffstatistic']));
			$my_text .= '</span>';
			$this->_success_text['Importing match staff statistic data:']=$my_text;
			return true;
		}
		foreach ($this->_datas['matchstaffstatistic'] as $key => $matchstaffstatistic)
		{
			$import_matchstaffstatistic=$this->_datas['matchstaffstatistic'][$key];
			$oldID=$this->_getDataFromObject($import_matchstaffstatistic,'id');

			$p_matchstaffstatistic = $this->getTable('MatchStaffStatistic');

			$p_matchstaffstatistic->set('match_id',$this->_convertMatchID[$this->_getDataFromObject($import_matchstaffstatistic,'match_id')]);
			$p_matchstaffstatistic->set('projectteam_id',$this->_convertProjectTeamID[$this->_getDataFromObject($import_matchstaffstatistic,'projectteam_id')]);
			$p_matchstaffstatistic->set('team_staff_id',$this->_convertTeamStaffID[$this->_getDataFromObject($import_matchstaffstatistic,'team_staff_id')]);
			$p_matchstaffstatistic->set('statistic_id',$this->_convertStatisticID[$this->_getDataFromObject($import_matchstaffstatistic,'statistic_id')]);
			$p_matchstaffstatistic->set('value',$this->_getDataFromObject($import_matchstaffstatistic,'value'));

			if ($p_matchstaffstatistic->store()===false)
			{
				$my_text .= 'error on matchstaffstatistic import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importMatchStaffStatistic<br />#$my_text#<br />#<pre>".print_r($p_matchstaffstatistic,true).'</pre>#';
				$this->_success_text['Importing match staff statistic data:']=$my_text;
				return false;
			}
			else
			{
				$dPerson=$this->_getPersonFromTeamStaff($p_matchstaffstatistic->team_staff_id);
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf(	'Created new match staff statistic data. StatisticID: %1$s - MatchID: %2$s - Player: %3$s,%4$s - Team: %5$s - Value: %6$s',
								'</span><strong>'.$p_matchstaffstatistic->statistic_id.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$p_matchstaffstatistic->match_id.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$dPerson->lastname,
								$dPerson->firstname.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$this->_getTeamName($p_matchstaffstatistic->projectteam_id).'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$p_matchstaffstatistic->value.'</strong>');
				$my_text .= '<br />';
			}
		}
		$this->_success_text['Importing match staff statistic data:']=$my_text;
		return true;
	}

	private function _importMatchStatistic()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['matchstatistic']) || count($this->_datas['matchstatistic'])==0){return true;}

		if ((!isset($this->_newstatisticsid) || count($this->_newstatisticsid)==0) &&
			(!isset($dbstatisticsid) || count($dbstatisticsid)==0)){return true;}

		if (!isset($this->_datas['statistic']) || count($this->_datas['statistic'])==0)
		{
			$my_text .= '<span style="color:red">';
			$my_text .= Text::sprintf('Warning: Skipped %1$s records for match statistic data because there is no statistic data included!',count($this->_datas['matchstatistic']));
			$my_text .= '</span>';
			$this->_success_text['Importing match statistic data:']=$my_text;
			return true;
		}
		if (!isset($this->_datas['match']) || count($this->_datas['match'])==0)
		{
			$my_text .= '<span style="color:red">';
			$my_text .= Text::sprintf('Warning: Skipped %1$s records for match statistic data because there is no match data included!',count($this->_datas['matchstatistic']));
			$my_text .= '</span>';
			$this->_success_text['Importing match statistic data:']=$my_text;
			return true;
		}
		if (!isset($this->_datas['projectteam']) || count($this->_datas['projectteam'])==0)
		{
			$my_text .= '<span style="color:red">';
			$my_text .= Text::sprintf('Warning: Skipped %1$s records for match statistic data because there is no projectteam data included!',count($this->_datas['matchstatistic']));
			$my_text .= '</span>';
			$this->_success_text['Importing match statistic data:']=$my_text;
			return true;
		}
		if (!isset($this->_datas['teamplayer']) || count($this->_datas['teamplayer'])==0)
		{
			$my_text .= '<span style="color:red">';
			$my_text .= Text::sprintf('Warning: Skipped %1$s records for match statistic data because there is no teamplayer data included!',count($this->_datas['matchstatistic']));
			$my_text .= '</span>';
			$this->_success_text['Importing match statistic data:']=$my_text;
			return true;
		}
		foreach ($this->_datas['matchstatistic'] as $key => $matchstatistic)
		{
			$import_matchstatistic=$this->_datas['matchstatistic'][$key];
			$oldID=$this->_getDataFromObject($import_matchstatistic,'id');

			$p_matchstatistic = $this->getTable('MatchStatistic');

			$p_matchstatistic->set('match_id',$this->_convertMatchID[$this->_getDataFromObject($import_matchstatistic,'match_id')]);
			$p_matchstatistic->set('projectteam_id',$this->_convertProjectTeamID[$this->_getDataFromObject($import_matchstatistic,'projectteam_id')]);
			$p_matchstatistic->set('teamplayer_id',$this->_convertTeamPlayerID[$this->_getDataFromObject($import_matchstatistic,'teamplayer_id')]);
			$p_matchstatistic->set('statistic_id',$this->_convertStatisticID[$this->_getDataFromObject($import_matchstatistic,'statistic_id')]);
			$p_matchstatistic->set('value',$this->_getDataFromObject($import_matchstatistic,'value'));

			if ($p_matchstatistic->store()===false)
			{
				$my_text .= 'error on matchstatistic import: ';
				$my_text .= $oldID;
				$my_text .= "<br />Error: _importMatchStatistic<br />#$my_text#<br />#<pre>".print_r($p_matchstatistic,true).'</pre>#';
				$this->_success_text['Importing match statistic data:']=$my_text;
				return false;
			}
			else
			{
				$dPerson=$this->_getPersonFromTeamPlayer($p_matchstatistic->teamplayer_id);
				$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
				$my_text .= Text::sprintf(	'Created new match statistic data. StatisticID: %1$s - MatchID: %2$s - Player: %3$s,%4$s - Team: %5$s - Value: %6$s',
								'</span><strong>'.$p_matchstatistic->statistic_id.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$p_matchstatistic->match_id.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$dPerson->lastname,
								$dPerson->firstname.'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$this->_getTeamName($p_matchstatistic->projectteam_id).'</strong><span style="color:'.$this->storeSuccessColor.'">',
								'</span><strong>'.$p_matchstatistic->value.'</strong>');
				$my_text .= '<br />';
			}
		}
		$this->_success_text['Importing match statistic data:']=$my_text;
		return true;
	}

	private function _importTreetos()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['treeto']) || count($this->_datas['treeto'])==0){return true;}
		if (isset($this->_datas['treeto']))
		{
			foreach ($this->_datas['treeto'] as $key => $treeto)
			{
				$p_treeto =  $this->getTable('Treeto');
				$oldId=(int)$treeto->id;
				$p_treeto->set('project_id',$this->_project_id);
				if ($treeto->id ==$this->_datas['treeto'][$key]->id)
				{
					if (trim($p_treeto->name)!='')
					{
						$p_treeto->set('name',$this->_getDataFromObject($treeto,'name'));
					}
					else
					{
						$p_treeto->set('name',$this->_getDataFromObject($treeto,'id'));
					}
					if (count($this->_convertDivisionID) > 0)
					{
						$p_treeto->set('division_id',$this->_convertDivisionID[$this->_getDataFromObject($treeto,'division_id')]);
					}
					$p_treeto->set('tree_i',$this->_getDataFromObject($treeto,'tree_i'));
					$p_treeto->set('global_bestof',$this->_getDataFromObject($treeto,'global_bestof'));
					$p_treeto->set('global_matchday',$this->_getDataFromObject($treeto,'global_matchday'));
					$p_treeto->set('global_known',$this->_getDataFromObject($treeto,'global_known'));
					$p_treeto->set('global_fake',$this->_getDataFromObject($treeto,'global_fake'));
					$p_treeto->set('leafed',$this->_getDataFromObject($treeto,'leafed'));
					$p_treeto->set('mirror',$this->_getDataFromObject($treeto,'mirror'));
					$p_treeto->set('hide',$this->_getDataFromObject($treeto,'hide'));
					$p_treeto->set('trophypic',$this->_getDataFromObject($treeto,'trophypic'));
				}
				if ($p_treeto->store()===false)
				{
					$my_text .= 'error on treeto import: ';
					$my_text .= '#'.$oldID.'#';
					$my_text .= "<br />Error: _importTreetos<br />#$my_text#<br />#<pre>".print_r($p_treeto,true).'</pre>#';
					$this->_success_text['Importing treeto data:']=$my_text;
					return false;
				}
				else
				{
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf('Created new treeto data: %1$s','</span><strong>'.$p_treeto->name.'</strong>');
					$my_text .= '<br />';
				}
				$insertID=$p_treeto->id; 
				$this->_convertTreetoID[$oldId]=$insertID;
			}
			$this->_success_text['Importing treeto data:']=$my_text;
			return true;
		}
	}

	private function _importTreetonode()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['treetonode']) || count($this->_datas['treetonode'])==0){return true;}
		if (isset($this->_datas['treetonode']))
		{
			foreach ($this->_datas['treetonode'] as $key => $treetonode)
			{
				$p_treetonode =  $this->getTable('TreetoNode');
				$oldId=(int)$treetonode->id;
				if ($treetonode->id ==$this->_datas['treetonode'][$key]->id)
				{
					$p_treetonode->set('treeto_id',$this->_convertTreetoID[$this->_getDataFromObject($treetonode,'treeto_id')]);
					$p_treetonode->set('node',$this->_getDataFromObject($treetonode,'node'));
					$p_treetonode->set('row',$this->_getDataFromObject($treetonode,'row'));
					$p_treetonode->set('bestof',$this->_getDataFromObject($treetonode,'bestof'));
					$p_treetonode->set('title',$this->_getDataFromObject($treetonode,'title'));
					$p_treetonode->set('content',$this->_getDataFromObject($treetonode,'content'));
					$p_treetonode->set('team_id',$this->_convertProjectTeamID[$this->_getDataFromObject($treetonode,'team_id')]);
					$p_treetonode->set('published',$this->_getDataFromObject($treetonode,'published'));
					$p_treetonode->set('is_leaf',$this->_getDataFromObject($treetonode,'is_leaf'));
					$p_treetonode->set('is_lock',$this->_getDataFromObject($treetonode,'is_lock'));
					$p_treetonode->set('is_ready',$this->_getDataFromObject($treetonode,'is_ready'));
					$p_treetonode->set('got_lc',$this->_getDataFromObject($treetonode,'got_lc'));
					$p_treetonode->set('got_rc',$this->_getDataFromObject($treetonode,'got_rc'));
				}
				if ($p_treetonode->store()===false)
				{
					$my_text .= 'error on treetonode import: ';
					$my_text .= '#'.$oldID.'#';
					$my_text .= "<br />Error: _importTreetonode<br />#$my_text#<br />#<pre>".print_r($p_treetonode,true).'</pre>#';
					$this->_success_text['Importing treetonode data:']=$my_text;
					return false;
				}
				else
				{
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf('Created new treetonode data: %1$s','</span><strong>'.$p_treetonode->id.'</strong>');
					$my_text .= '<br />';
				}
				$insertID=$p_treetonode->id; 
				$this->_convertTreetonodeID[$oldId]=$insertID;
			}
			$this->_success_text['Importing treetonode data:']=$my_text;
			return true;
		}
	}

	private function _importTreetomatch()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$my_text='';
		if (!isset($this->_datas['treetomatch']) || count($this->_datas['treetomatch'])==0){return true;}
		if (isset($this->_datas['treetomatch']))
		{
			foreach ($this->_datas['treetomatch'] as $key => $treetomatch)
			{
				$p_treetomatch =  $this->getTable('TreetoMatch');
				$oldId=(int)$treetomatch->id;
				if ($treetomatch->id ==$this->_datas['treetomatch'][$key]->id)
				{
					$p_treetomatch->set('node_id',$this->_convertTreetonodeID[$this->_getDataFromObject($treetomatch,'node_id')]);
					$p_treetomatch->set('match_id',$this->_convertMatchID[$this->_getDataFromObject($treetomatch,'match_id')]);
				}
				if ($p_treetomatch->store()===false)
				{
					$my_text .= 'error on treetomatch import: ';
					$my_text .= '#'.$oldID.'#';
					$my_text .= "<br />Error: _importTreetomatch<br />#$my_text#<br />#<pre>".print_r($p_treetomatch,true).'</pre>#';
					$this->_success_text['Importing treetomatch data:']=$my_text;
					return false;
				}
				else
				{
					$my_text .= '<span style="color:'.$this->storeSuccessColor.'">';
					$my_text .= Text::sprintf('Created new treetomatch data: %1$s','</span><strong>'.$p_treetomatch->id.'</strong>');
					$my_text .= '<br />';
				}
				$insertID=$p_treetomatch->id; 
				$this->_convertTreetomatchID[$oldId]=$insertID;
			}
			$this->_success_text['Importing treetomatch data:']=$my_text;
			return true;
		}
	}

	private function _beforeFinish()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// convert favorite teams
		$checked_fav_teams=trim($this->_getDataFromObject($this->_datas['project'],'fav_team'));
		$t_fav_team='';
		if ($checked_fav_teams!='')
		{
			$t_fav_teams=explode(",",$checked_fav_teams);
			foreach ($t_fav_teams as $value)
			{
				if (isset($this->_convertTeamID[$value])){$t_fav_team .= $this->_convertTeamID[$value].',';}
			}
			$t_fav_team=trim($t_fav_team,',');
		}
		$query="UPDATE #__joomleague_project SET fav_team='$t_fav_team' WHERE id=$this->_project_id";
		$db->setQuery($query);
		$db->execute();
	}

	public function importData($post)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$this->_datas=$this->getData();

		$this->_newteams=array();
		$this->_newteamsshort=array();
		$dbteamsid=array();
		$this->_newteamsmiddle=array();
		$this->_newteamsinfo=array();

		$this->_newclubs=array();
		$this->_newclubsid=array();
		$this->_newclubscountry=array();
		$dbclubsid=array();
		$this->_createclubsid=array();

		$this->_newplaygroundid=array();
		$this->_newplaygroundname=array();
		$this->_newplaygroundshort=array();
		$dbplaygroundsid=array();

		$this->_newpersonsid=array();
		$this->_newperson_lastname=array();
		$this->_newperson_firstname=array();
		$this->_newperson_nickname=array();
		$this->_newperson_birthday=array();
		$dbpersonsid=array();

		$this->_neweventsname=array();
		$this->_neweventsid=array();
		$dbeventsid=array();

		$this->_newpositionsname=array();
		$this->_newpositionsid=array();
		$dbpositionsid=array();

		$this->_newparentpositionsname=array();
		$this->_newparentpositionsid=array();
		$dbparentpositionsid=array();

		$this->_newstatisticsname=array();
		$this->_newstatisticsid=array();
		$dbstatisticsid=array();

		//tracking of old -> new ids
		// The 0 entry is needed to translate an input with ID 0 to an output with ID 0;
		// this can happen when the exported file contains a field with ID equal to 0
		$standard_translation = array(0 => 0);
		$this->_convertProjectTeamID=$standard_translation;
		$this->_convertProjectRefereeID=$standard_translation;
		$this->_convertTeamPlayerID=$standard_translation;
		$this->_convertTeamStaffID=$standard_translation;
		$this->_convertProjectPositionID=$standard_translation;
		$this->_convertClubID=$standard_translation;
		$this->_convertPersonID=$standard_translation;
		$this->_convertTeamID=$standard_translation;
		$this->_convertRoundID=$standard_translation;
		$this->_convertDivisionID=$standard_translation;
		$this->_convertCountryID=$standard_translation;
		$this->_convertPlaygroundID=$standard_translation;
		$this->_convertEventID=$standard_translation;
		$this->_convertPositionID=$standard_translation;
		$this->_convertParentPositionID=$standard_translation;
		$this->_convertMatchID=$standard_translation;
		$this->_convertStatisticID=$standard_translation;
		$this->_convertTreetoID=$standard_translation;
		$this->_convertTreetonodeID=$standard_translation;
		$this->_convertTreetomatchID=$standard_translation;

		if (is_array($post) && count($post) > 0)
		{
			foreach($post as $key => $element)
			{
				if (substr($key,0,8)=='teamName')
				{
					$tempteams=explode("_",$key);
					$this->_newteams[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,13)=='teamShortname')
				{
					$tempteams=explode("_",$key);
					$this->_newteamsshort[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,8)=='teamInfo')
				{
					$tempteams=explode("_",$key);
					$this->_newteamsinfo[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,14)=='teamMiddleName')
				{
					$tempteams=explode("_",$key);
					$this->_newteamsmiddle[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,6)=='teamID')
				{
					$tempteams=explode("_",$key);
					$this->_newteamsid[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,8)=='dbTeamID')
				{
					$tempteams=explode("_",$key);
					$dbteamsid[$tempteams[1]]=$element;
				}
				elseif (substr($key,0,8)=='clubName')
				{
					$tempclubs=explode("_",$key);
					$this->_newclubs[$tempclubs[1]]=$element;
				}
				elseif (substr($key,0,11)=='clubCountry')
				{
					$tempclubs=explode("_",$key);
					$this->_newclubscountry[$tempclubs[1]]=$element;
				}
				/**/
				elseif (substr($key,0,6)=='clubID')
				{
					$tempclubs=explode("_",$key);
					$this->_newclubsid[$tempclubs[1]]=$element;
				}
				/**/
				elseif (substr($key,0,10)=='createClub')
				{
					$tempclubs=explode("_",$key);
					$this->_createclubsid[$tempclubs[1]]=$element;
				}
				elseif (substr($key,0,8)=='dbClubID')
				{
					$tempclubs=explode("_",$key);
					$dbclubsid[$tempclubs[1]]=$element;
				}
				elseif (substr($key,0,9)=='eventName')
				{
					$tempevent=explode("_",$key);
					$this->_neweventsname[$tempevent[1]]=$element;
				}
				elseif (substr($key,0,7)=='eventID')
				{
					$tempevent=explode("_",$key);
					$this->_neweventsid[$tempevent[1]]=$element;
				}
				elseif (substr($key,0,9)=='dbEventID')
				{
					$tempevent=explode("_",$key);
					$dbeventsid[$tempevent[1]]=$element;
				}
				elseif (substr($key,0,12)=='positionName')
				{
					$tempposition=explode("_",$key);
					$this->_newpositionsname[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,10)=='positionID')
				{
					$tempposition=explode("_",$key);
					$this->_newpositionsid[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,12)=='dbPositionID')
				{
					$tempposition=explode("_",$key);
					$dbpositionsid[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,18)=='parentPositionName')
				{
					$tempposition=explode("_",$key);
					$this->_newparentpositionsname[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,16) =="parentPositionID")
				{
					$tempposition=explode("_",$key);
					$this->_newparentpositionsid[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,18)=='dbParentPositionID')
				{
					$tempposition=explode("_",$key);
					$dbparentpositionsid[$tempposition[1]]=$element;
				}
				elseif (substr($key,0,14)=='playgroundName')
				{
					$tempplayground=explode("_",$key);
					$this->_newplaygroundname[$tempplayground[1]]=$element;
				}
				elseif (substr($key,0,19)=='playgroundShortname')
				{
					$tempplayground=explode("_",$key);
					$this->_newplaygroundshort[$tempplayground[1]]=$element;
				}
				elseif (substr($key,0,12)=='playgroundID')
				{
					$tempplayground=explode("_",$key);
					$this->_newplaygroundid[$tempplayground[1]]=$element;
				}
				elseif (substr($key,0,14)=='dbPlaygroundID')
				{
					$tempplayground=explode("_",$key);
					$dbplaygroundsid[$tempplayground[1]]=$element;
				}
				elseif (substr($key,0,13)=='statisticName')
				{
					$tempstatistic=explode("_",$key);
					$this->_newstatisticsname[$tempstatistic[1]]=$element;
				}
				elseif (substr($key,0,11)=='statisticID')
				{
					$tempstatistic=explode("_",$key);
					$this->_newstatisticsid[$tempstatistic[1]]=$element;
				}
				elseif (substr($key,0,13)=='dbStatisticID')
				{
					$tempstatistic=explode("_",$key);
					$dbstatisticsid[$tempstatistic[1]]=$element;
				}
				elseif (substr($key,0,14)=='personLastname')
				{
					$temppersons=explode("_",$key);
					$this->_newperson_lastname[$temppersons[1]]=$element;
				}
				elseif (substr($key,0,15)=='personFirstname')
				{
					$temppersons=explode("_",$key);
					$this->_newperson_firstname[$temppersons[1]]=$element;
				}
				elseif (substr($key,0,14)=='personNickname')
				{
					$temppersons=explode("_",$key);
					$this->_newperson_nickname[$temppersons[1]]=$element;
				}
				elseif (substr($key,0,14)=='personBirthday')
				{
					$temppersons=explode("_",$key);
					$this->_newperson_birthday[$temppersons[1]]=$element;
				}
				elseif (substr($key,0,8)=='personID')
				{
					$temppersons=explode("_",$key);
					$this->_newpersonsid[$temppersons[1]]=$element;
				}
				elseif (substr($key,0,10)=='dbPersonID')
				{
					$temppersons=explode("_",$key);
					$dbpersonsid[$temppersons[1]]=$element;
				}
			}

			$this->_success_text='';

			//set $this->_importType
			$this->_importType=$post['importType'];

			//check project name
			if ($post['importProject'])
			{
				if (isset($post['name'])) // Project Name
				{
					$this->_name=substr($post['name'],0,100);
				}
				else
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Missing projectname'));
				    echo "<script> alert('".Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Missing projectname')."'); window.history.go(-1); </script>\n";
				}

				if (empty($this->_datas['project']))
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Missing projectname'));
				    echo "<script> alert('".Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Project object is missing inside import file!!!')."'); window.history.go(-1); </script>\n";
				    return false;				    
				}

				if ($this->_checkProject()===false)
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Projectname already exists'));
				    echo "<script> alert('".Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Projectname already exists')."'); window.history.go(-1); </script>\n";
					return false;
				}
			}

			//check sportstype
			if ($post['importProject'] || $post['importType']=='events' || $post['importType']=='positions')
			{
				if ((isset($post['sportstype'])) && ($post['sportstype'] > 0))
				{
					$this->_sportstype_id=(int)$post['sportstype'];
				}
				elseif (isset($post['sportstypeNew']))
					{
						$this->_sportstype_new=substr($post['sportstypeNew'],0,25);
					}
					else
					{
					    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Missing sportstype'));
					    echo "<script> alert('".Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Missing sportstype')."'); window.history.go(-1); </script>\n";
						return false;
					}
			}

			//check league/season/admin/editor/publish/template
			if ($post['importProject'])
			{
				if (isset($post['league']))
				{
					$this->_league_id=(int)$post['league'];
				}
				elseif (isset($post['leagueNew']))
				{
					$this->_league_new=substr($post['leagueNew'],0,75);
				}
				else
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Missing league'));
				    echo "<script> alert('".Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Missing league')."'); window.history.go(-1); </script>\n";
					return false;
				}

				if (isset($post['season']))
				{
					$this->_season_id=(int)$post['season'];
				}
				elseif (isset($post['seasonNew']))
				{
						$this->_season_new=substr($post['seasonNew'],0,75);
				}
				else
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Missing season'));
				    echo "<script> alert('".Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Missing season')."'); window.history.go(-1); </script>\n";
					return false;
				}

				if (isset($post['published']))
				{
					$this->_published=(int)$post['published'];
				}
				else
				{
					$this->_published=0;
				}
				if (isset($post['timezone']))
				{
					$this->_timezone=$post['timezone'];
				}
				
				if (isset($post['copyTemplate'])) // if new template set this value is 0
				{
					$this->_template_id=(int)$post['copyTemplate'];
				}
				else
				{
					$this->_template_id=0;
				}
			}

			/**
			 *
			 * Real Import Work starts here
			 *
			 */
			if ($post['importProject'] || $post['importType']=='events' || $post['importType']=='positions')
			{
				// import sportstype
				if ($this->_importSportsType()===false)
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','sports-type'));
				    return $this->_success_text;
				}
			}

			if ($post['importProject'])
			{
				// import league
				if ($this->_importLeague()===false)
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','league'));
				    return $this->_success_text;
				}

				// import season
				if ($this->_importSeason()===false)
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','season'));
				    return $this->_success_text;
				}
			}

			// import events / should also work with exported events-XML without problems
			if ($this->_importEvents()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','event'));
			    return $this->_success_text;
			}

			// import Statistic
			if ($this->_importStatistics()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','statistic'));
			    return $this->_success_text;
			}

			// import parent positions
			if ($this->_importParentPositions()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','parent-position'));
			    return $this->_success_text;
			}

			// import positions
			if ($this->_importPositions()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','position'));
			    return $this->_success_text;
			}

			// import PositionEventType
			if ($this->_importPositionEventType()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','position-eventtype'));
			    return $this->_success_text;
			}

			// import playgrounds
			if ($this->_importPlayground()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','playground'));
			    return $this->_success_text;
			}

			// import clubs
			if ($this->_importClubs()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','club'));
			    return $this->_success_text;
			}

			if ($this->_importType!='playgrounds')	// don't convert club_id if only playgrounds are imported
			{
				// convert playground Club-IDs
				if ($this->_convertNewPlaygroundIDs()===false)
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','conversion of playground club-id'));
				    return $this->_success_text;
				}
			}

			// import teams
			if ($this->_importTeams()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','team'));
			    return $this->_success_text;
			}

			// import persons
			if ($this->_importPersons()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','person'));
			    return $this->_success_text;
			}

			if ($post['importProject'])
			{
				// import project
				if ($this->_importProject()===false)
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','project'));
				    return $this->_success_text;
				}

				// import template
				if ($this->_importTemplate()===false)
				{
				    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','template'));
				    return $this->_success_text;
				}
			}

			// import divisions
			if ($this->_importDivisions()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','division'));
			    return $this->_success_text;
			}

			// import project positions
			if ($this->_importProjectPositions()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','projectpositions'));
			    return $this->_success_text;
			}

			// import project referees
			if ($this->_importProjectReferees()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','projectreferees'));
			    return $this->_success_text;
			}


			// import projectteam
			if ($this->_importProjectTeam()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','projectteam'));
			    return $this->_success_text;
			}

			// import teamplayers
			if ($this->_importTeamPlayer()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','teamplayer'));
			    return $this->_success_text;
			}

			// import teamstaffs
			if ($this->_importTeamStaff()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','teamstaff'));
			    return $this->_success_text;
			}

			// import teamtrainingdata
			if ($this->_importTeamTraining()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','teamtraining'));
			    return $this->_success_text;
			}

			// import rounds
			if ($this->_importRounds()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','round'));
			    return $this->_success_text;
			}

			// import matches
			// last to import cause needs a lot of imports and conversions inside the database before match-conversion may be done
			// after this import only the matchplayers,-staffs,-referees and -events can be imported cause they need existing
			//
			// imported matches
			if ($this->_importMatches()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','match'));
			    return $this->_success_text;
			}

			// import MatchPlayer
			if ($this->_importMatchPlayer()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','matchplayer'));
			    return $this->_success_text;
			}

			// import MatchStaff
			if ($this->_importMatchStaff()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','matchstaff'));
			    return $this->_success_text;
			}

			// import MatchReferee
			if ($this->_importMatchReferee()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','matchreferee'));
			    return $this->_success_text;
			}

			// import MatchEvent
			if ($this->_importMatchEvent()===false)
			{
			    Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','matchevent'));
			    return $this->_success_text;
			}

			// import PositionStatistic
			if ($this->_importPositionStatistic()===false)
			{
				Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','positionstatistic'));
				return $this->_success_text;
			}

			// import MatchStaffStatistic
			if ($this->_importMatchStaffStatistic()===false)
			{
				Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','matchstaffstatistic'));
				return $this->_success_text;
			}

			// import MatchStatistic
			if ($this->_importMatchStatistic()===false)
			{
				Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','matchstatistic'));
				return $this->_success_text;
			}
			// import Treeto
			if ($this->_importTreetos()===false)
			{
				Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','treeto'));
				return $this->_success_text;
			}

			// import Treetonode
			if ($this->_importTreetonode()===false)
			{
				Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','treetonode'));
				return $this->_success_text;
			}

			// import Treetomatch
			if ($this->_importTreetomatch()===false)
			{
				Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_ERROR_DURING','treetomatch'));
				return $this->_success_text;
			}

			if ($post['importProject'])
			{
				$this->_beforeFinish();
			}

			$this->_deleteImportFile();

			return $this->_success_text;
		}
		else
		{
			$this->_deleteImportFile();
			Factory::getApplication()->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ERROR','Missing import data'));
			return false;
		}
	}
}
