<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at

 * @author		Kurt Norgaz
 * 
 * script file to CREATE standard soccer events,positions and position-eventtypes
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

$version			= '3.0.22.57ae969-b';
$updateFileDate		= '2012-09-13';
$updateFileTime		= '00:05';
$updateDescription	= '<span style="color:green">Create standard soccer events,positions and position-events for use with JoomLeague</span>';
$excludeFile		= 'false';
$app 				= Factory::getApplication();

function PrintStepResult($result)
{
	if ($result)
	{
		$output=' - <span style="color:green">'.Text::_('SUCCESS').'</span>';
	}
	else
	{
		$output=' - <span style="color:red">'.Text::_('FAILED').'</span>';
	}

	return $output;
}

function addSportsType()
{
	$result	= false;
	$db	   = Factory::getDbo();
	$app   = Factory::getApplication();

	echo Text::sprintf	(	'Adding the sports-type [%1$s] to table [%2$s] if it does not exist yet!',
							'<strong>'.'COM_JOOMLEAGUE_ST_SOCCER'.'</strong>',
							'<strong>'.'#__joomleague_sports_type'.'</strong>'
						);

	$query="SELECT id FROM #__joomleague_sports_type WHERE name='COM_JOOMLEAGUE_ST_SOCCER'";
	$db->setQuery($query);
	if (!$dbresult=$db->loadObject())
	{
		// Add new sportstype Soccer to #__joomleague_sports_type
		$queryAdd="INSERT INTO #__joomleague_sports_type (`name`) VALUES ('COM_JOOMLEAGUE_ST_SOCCER')";
		$db->setQuery($queryAdd);
		try
		{
			$result = $db->execute();
		}
		catch(Exception $e)
		{
			$app->enqueueMessage($e->getMessage(),'warning');
		}
	}

	echo PrintStepResult($result).'<br />';
	if (!$result) { echo Text::_ ('DO NOT WORRY... Surely the sports-type soccer was already existing in your database!!!').'<br />'; }

	return '';
}

function build_SelectQuery($tablename,$param1)
{
	$query="SELECT * FROM #__joomleague_".$tablename." WHERE name='".$param1."'";
	return $query;
}

function build_InsertQuery_Position($tablename,$param1,$param2,$param3,$param4,$sports_type_id,$order_count)
{
	$alias=OutputFilter::stringURLSafe($param1);
	$query="INSERT INTO #__joomleague_".$tablename." (`name`,`alias`,`".$param2."`,`parent_id`,`sports_type_id`,`published`,`ordering`) VALUES ('".$param1."','".$alias."','".$param4."','".$param3."','".$sports_type_id."','1','".$order_count."')";
	return $query;
}

function build_InsertQuery_Event($tablename,$param1,$param2,$suspension,$sports_type_id,$order_count)
{
    $alias=OutputFilter::stringURLSafe($param1);
	$query="INSERT INTO #__joomleague_".$tablename." (`name`,`alias`,`icon`,`suspension`,`sports_type_id`,`published`,`ordering`) VALUES ('".$param1."','".$alias."','".$param2."','".$suspension."','".$sports_type_id."','1','".$order_count."')";
	return $query;
}

function build_InsertQuery_PositionEventType($param1,$param2)
{
	$query="	INSERT INTO	#__joomleague_position_eventtype
				(`position_id`,`eventtype_id`)
				VALUES
				('".$param1."','".$param2."')";
	return $query;
}

function addStandardsForSoccer()
{
	$events_player		= array();
	$events_staff		= array();
	$events_referees	= array();
	$events_clubstaff	= array();
	$PlayersPositions	= array();
	$StaffPositions		= array();
	$RefereePositions	= array();
	$ClubStaffPositions	= array();

	$result				= false;
	$db					= Factory::getDbo();
	$app 				= Factory::getApplication();

	if ('AddEvents' == 'AddEvents')
	{
		echo Text::sprintf('Adding standard soccer events to table table [%s]','<b>'.'#__joomleague_eventtype'.'</b>');

		$squery				= 'SELECT * FROM #__joomleague_eventtype WHERE name=`%s`';
		$isquery			= 'INSERT INTO #__joomleague_eventtype (`name`,`icon`) VALUES (`%1$s`,`%2$s`)';
		$query				= "SELECT * FROM #__joomleague_sports_type WHERE name='COM_JOOMLEAGUE_ST_SOCCER'"; $db->setQuery($query); $sports_type=$db->loadObject();

		$newEventName='COM_JOOMLEAGUE_E_GOAL';
		$newEventIcon='images/com_joomleague/database/events/soccer/goal.png';
		$suspension=0;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query				= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,1); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			
			$events_player['0']	= $db->insertid();
		}
		else
		{
			$events_player['0']	= $object->id;
		}

		$newEventName='COM_JOOMLEAGUE_E_ASSISTS';
		$newEventIcon='images/com_joomleague/database/events/soccer/assists.png';
		$suspension=0;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query				= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,2); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['1']	= $db->insertid();
		}
		else
		{
			$events_player['1']	= $object->id;
		}

		$newEventName='COM_JOOMLEAGUE_E_YELLOW_CARD';
		$newEventIcon='images/com_joomleague/database/events/soccer/yellow_card.png';
		$suspension=0;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query					= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,3); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['2']		= $db->insertid();
			$events_staff['2']		= $db->insertid();
			$events_clubstaff['2']	= $db->insertid();
			$events_referees['2']	= $db->insertid();
		}
		else
		{
			$events_player['2']		= $object->id;
			$events_staff['2']		= $object->id;
			$events_clubstaff['2']	= $object->id;
			$events_referees['2']	= $object->id;
		}

		$newEventName='COM_JOOMLEAGUE_E_YELLOW-RED_CARD';
		$newEventIcon='images/com_joomleague/database/events/soccer/yellow_red_card.png';
		$suspension=1;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query					= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,4); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['3']		= $db->insertid();
			$events_staff['3']		= $db->insertid();
			$events_clubstaff['3']	= $db->insertid();
			$events_referees['3']	= $db->insertid();
		}
		else
		{
			$events_player['3']		= $object->id;
			$events_staff['3']		= $object->id;
			$events_clubstaff['3']	= $object->id;
			$events_referees['3']	= $object->id;
		}

		$newEventName='COM_JOOMLEAGUE_E_RED_CARD';
		$newEventIcon='images/com_joomleague/database/events/soccer/red_card.png';
		$suspension=1;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query					= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,5); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['4']		= $db->insertid();
			$events_staff['4']		= $db->insertid();
			$events_clubstaff['4']	= $db->insertid();
			$events_referees['4']	= $db->insertid();
		}
		else
		{
			$events_player['4']		= $object->id;
			$events_staff['4']		= $object->id;
			$events_clubstaff['4']	= $object->id;
			$events_referees['4']	= $object->id;
		}

		$newEventName='COM_JOOMLEAGUE_E_FOUL';
		$newEventIcon='images/com_joomleague/database/events/soccer/foul.png';
		$suspension=0;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query					= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,6); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['5']		= $db->insertid();
			$events_referees['5']	= $db->insertid();
		}
		else
		{
			$events_player['5']		= $object->id;
			$events_referees['5']	= $object->id;
		}

		$newEventName='COM_JOOMLEAGUE_E_FOUL_TIME';
		$newEventIcon='images/com_joomleague/database/events/soccer/foul_time.png';
		$suspension=0;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query					= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,7); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['6']		= $db->insertid();
			$events_referees['6']	= $db->insertid();
		}
		else
		{
			$events_player['6']		= $object->id;
			$events_referees['6']	= $object->id;
		}

		$newEventName='COM_JOOMLEAGUE_E_OWN_GOAL';
		$newEventIcon='images/com_joomleague/database/events/soccer/own_goal.png';
		$suspension=0;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query				= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,8); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['7']	= $db->insertid();
		}
		else
		{
			$events_player['7']	= $object->id;
		}

		$newEventName='COM_JOOMLEAGUE_E_PENALTY_GOAL';
		$newEventIcon='images/com_joomleague/database/events/soccer/penalty_goal.png';
		$suspension=0;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query					= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,9); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['8']		= $db->insertid();
			$events_referees['8']	= $db->insertid();
		}
		else
		{
			$events_player['8']		= $object->id;
			$events_referees['8']	= $object->id;
		}

		$newEventName='COM_JOOMLEAGUE_E_INJURY';
		$newEventIcon='images/com_joomleague/database/events/soccer/injured.png';
		$suspension=0;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query					= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,10); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['9']		= $db->insertid();
			$events_staff['9']		= $db->insertid();
			$events_clubstaff['9']	= $db->insertid();
			$events_referees['9']	= $db->insertid();
		}
		else
		{
			$events_player['9']		= $object->id;
			$events_staff['9']		= $object->id;
			$events_clubstaff['9']	= $object->id;
			$events_referees['9']	= $object->id;
		}

			$newEventName='COM_JOOMLEAGUE_E_BLUE_CARD';
		$newEventIcon='images/com_joomleague/database/events/soccer/blue_card.png';
		$suspension=0;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query					= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,11); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['10']	= $db->insertid();
			$events_staff['10']		= $db->insertid();
			$events_clubstaff['10']	= $db->insertid();
			$events_referees['10']	= $db->insertid();
		}
		else
		{
			$events_player['10']	= $object->id;
			$events_staff['10']		= $object->id;
			$events_clubstaff['10']	= $object->id;
			$events_referees['10']	= $object->id;
		}
		
		$newEventName='COM_JOOMLEAGUE_E_BLUE_RED_CARD';
		$newEventIcon='images/com_joomleague/database/events/soccer/blue_red_card.png';
		$suspension=1;
		$query=build_SelectQuery('eventtype',$newEventName); $db->setQuery($query);
		if (!$object=$db->loadObject())
		{
			$query					= build_InsertQuery_Event('eventtype',$newEventName,$newEventIcon,$suspension,$sports_type->id,12); $db->setQuery($query);
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
			$events_player['11']	= $db->insertid();
			$events_staff['11']		= $db->insertid();
			$events_clubstaff['11']	= $db->insertid();
			$events_referees['11']	= $db->insertid();
		}
		else
		{
			$events_player['11']	= $object->id;
			$events_staff['11']		= $object->id;
			$events_clubstaff['11']	= $object->id;
			$events_referees['11']	= $object->id;
		}
		
		echo PrintStepResult($result).'<br />';
		if (!$result) { echo Text::_ ('DO NOT WORRY... Surely at least one of the events was already existing in your database!!!').'<br />'; }
		echo '<br />';
	}

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	$result=false;

	if ('AddPositions' == 'AddPositions')
	{
		echo Text::sprintf('Adding standard soccer positions to table table [%s]','<b>'.'#__joomleague_position'.'</b>');

		if ('AddGeneralPlayersPositions' == 'AddGeneralPlayersPositions')
		{
			// Add new Parent position to PlayersPositions
			$newPosName='COM_JOOMLEAGUE_F_PLAYERS'; $newPosSwitch='persontype'; $newPosParent='0'; $newPosContent='1';
			$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
			if (!$dbresult=$db->loadObject())
			{
				$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,1); $db->setQuery($query);
				try
				{
					$result = $db->execute();
				}
				catch(Exception $e)
				{
					$app->enqueueMessage($e->getMessage(),'warning');
				}
				$ParentID				= $db->insertid();
				$PlayersPositions['0']	= $db->insertid();
			}
			else
			{
				$ParentID				= $dbresult->id;
				$PlayersPositions['0']	= $dbresult->id;
			}

			if ('AddGeneralPlayersChildPositions' == 'AddGeneralPlayersChildPositions')
			{
				// Add new Child positions to PlayersPositions

				// New Child position for PlayersPositions
				$newPosName='COM_JOOMLEAGUE_P_GOALKEEPER'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='1';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$object=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,2); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$PlayersPositions['1']	= $db->insertid();
				}
				else
				{
					$PlayersPositions['1']	= $object->id;
				}

				// New Child position for PlayersPositions
				$newPosName='COM_JOOMLEAGUE_P_DEFENDER'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='1';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$object=$db->loadObject())
				{
					$query	= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,3); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$PlayersPositions['2']	= $db->insertid();
				}
				else
				{
					$PlayersPositions['2']	= $object->id;
				}

				// New Child position for PlayersPositions
				$newPosName='COM_JOOMLEAGUE_P_MIDFIELDER'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='1';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$object=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,4); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$PlayersPositions['3']	= $db->insertid();
				}
				else
				{
					$PlayersPositions['3']	= $object->id;
				}

				// New Child position for PlayersPositions
				$newPosName='COM_JOOMLEAGUE_P_FORWARD'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='1';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$object=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,5); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$PlayersPositions['4']	= $db->insertid();
				}
				else
				{
					$PlayersPositions['4']	= $object->id;
				}
			}
		}

		//----------------------------------------------------------------------

		if ('AddGeneralStaffPositions' == 'AddGeneralStaffPositions')
		{
			if ('AddGeneralStaffTeamStaffPositions' == 'AddGeneralStaffTeamStaffPositions')
			{
				// Add new Parent position to StaffPositions
				$newPosName='COM_JOOMLEAGUE_F_TEAM_STAFF'; $newPosSwitch='persontype'; $newPosParent='0'; $newPosContent='2';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$dbresult=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,6); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$ParentID				= $db->insertid();
					$StaffPositions['0']	= $db->insertid();
				}
				else
				{
					$ParentID				= $dbresult->id;
					$StaffPositions['0']	= $dbresult->id;
				}
			}

			//----------------------------------------------------------------------

			if ('AddGeneralStaffCoachesPositions' == 'AddGeneralStaffCoachesPositions')
			{
				// Add new Parent position to StaffPositions
				$newPosName='COM_JOOMLEAGUE_F_COACHES'; $newPosSwitch='persontype'; $newPosParent='0'; $newPosContent='2';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$dbresult=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,7); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$ParentID				= $db->insertid();
					$StaffPositions['1']	= $db->insertid();
				}
				else
				{
					$ParentID				= $dbresult->id;
					$StaffPositions['1']	= $dbresult->id;
				}

				if ('AddGeneralStaffCoachesChildPositions' == 'AddGeneralStaffCoachesChildPositions')
				{
					// New Child position for StaffPositions
					$newPosName='COM_JOOMLEAGUE_F_COACH'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='2';
					$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
					if (!$object=$db->loadObject())
					{
						$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,8); $db->setQuery($query);
						try
						{
							$result = $db->execute();
						}
						catch(Exception $e)
						{
							$app->enqueueMessage($e->getMessage(),'warning');
						}
						$StaffPositions['2']	= $db->insertid();
					}
					else
					{
						$StaffPositions['2']	= $object->id;
					}

					// New Child position for StaffPositions
					$newPosName='COM_JOOMLEAGUE_F_HEAD_COACH'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='2';
					$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
					if (!$object=$db->loadObject())
					{
						$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,9); $db->setQuery($query);
						try
						{
							$result = $db->execute();
						}
						catch(Exception $e)
						{
							$app->enqueueMessage($e->getMessage(),'warning');
						}
						$StaffPositions['3']	= $db->insertid();
					}
					else
					{
						$StaffPositions['3']	= $object->id;
					}
				}
			}

			//----------------------------------------------------------------------

			if ('AddGeneralStaffMaintainerteamPositions' == 'AddGeneralStaffMaintainerteamPositions')
			{
				// Add new Parent position to StaffPositions
				$newPosName='COM_JOOMLEAGUE_F_MAINTAINER_TEAM'; $newPosSwitch='persontype'; $newPosParent='0'; $newPosContent='2';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$dbresult=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,10); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$ParentID				= $db->insertid();
					$StaffPositions['4']	= $db->insertid();
				}
				else
				{
					$ParentID				= $dbresult->id;
					$StaffPositions['4']	= $dbresult->id;
				}

				if ('AddGeneralStaffMaintainerChildPositions' == 'AddGeneralStaffMaintainerChildPositions')
				{
					// New Child position for StaffPositions
					$newPosName='COM_JOOMLEAGUE_F_MAINTAINER'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='2';
					$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
					if (!$object=$db->loadObject())
					{
						$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,11); $db->setQuery($query);
						try
						{
							$result = $db->execute();
						}
						catch(Exception $e)
						{
							$app->enqueueMessage($e->getMessage(),'warning');
						}
						$StaffPositions['5']	= $db->insertid();
					}
					else
					{
						$StaffPositions['5']	= $object->id;
					}
				}
			}

			//----------------------------------------------------------------------

			if ('AddGeneralStaffMedicalStaffPositions' == 'AddGeneralStaffMedicalStaffPositions')
			{
				// Add new Parent position to StaffPositions
				$newPosName='COM_JOOMLEAGUE_F_MEDICAL_STAFF'; $newPosSwitch='persontype'; $newPosParent='0'; $newPosContent='2';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$dbresult=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,12); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$ParentID				= $db->insertid();
					$StaffPositions['6']	= $db->insertid();
				}
				else
				{
					$ParentID				= $dbresult->id;
					$StaffPositions['6']	= $dbresult->id;
				}
			}
		}

		//----------------------------------------------------------------------

		if ('AddGeneralRefereesPositions' == 'AddGeneralRefereesPositions')
		{
			// Add new Parent position to RefereesPositions
			$newPosName='COM_JOOMLEAGUE_F_REFEREES'; $newPosSwitch='persontype'; $newPosParent='0'; $newPosContent='3';
			$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
			if (!$dbresult=$db->loadObject())
			{
				$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,13); $db->setQuery($query);
				try
				{
					$result = $db->execute();
				}
				catch(Exception $e)
				{
					$app->enqueueMessage($e->getMessage(),'warning');
				}
				$ParentID				= $db->insertid();
				$RefereePositions['0']	= $db->insertid();
			}
			else
			{
				$ParentID				= $dbresult->id;
				$RefereePositions['0']	= $dbresult->id;
			}

			if ('AddGeneralRefereesChildPositions' == 'AddGeneralRefereesChildPositions')
			{
				// New Child position for RefereePositions
				$newPosName='COM_JOOMLEAGUE_F_CENTER_REFEREE'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='3';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$object=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,14); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$RefereePositions['1']	= $db->insertid();
				}
				else
				{
					$RefereePositions['1']	= $object->id;
				}

				// New Child position for RefereePositions
				$newPosName='COM_JOOMLEAGUE_F_LINESMAN'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='3';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$object=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,15); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$RefereePositions['2']	= $db->insertid();
				}
				else
				{
					$RefereePositions['2']	= $object->id;
				}

				// New Child position for RefereePositions
				$newPosName='COM_JOOMLEAGUE_F_FOURTH_OFFICIAL'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='3';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$object=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,16); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$RefereePositions['3']	= $db->insertid();
				}
				else
				{
					$RefereePositions['3']	= $object->id;
				}

				// New Child position for RefereePositions
				$newPosName	= 'COM_JOOMLEAGUE_F_FIFTH_OFFICIAL'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='3';
				$query		= build_SelectQuery('position',$newPosName); $db->setQuery($query);
				if (!$object=$db->loadObject())
				{
					$query					= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,17); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$RefereePositions['4']	= $db->insertid();
				}
				else
				{
					$RefereePositions['4']	= $object->id;
				}
			}
		}

		//----------------------------------------------------------------------

		if ('AddGeneralClubstaffPositions' == 'AddGeneralClubstaffPositions')
		{
			// Add new Parent position to ClubStaffPositions
			$newPosName	= 'COM_JOOMLEAGUE_F_CLUB_STAFF'; $newPosSwitch='persontype'; $newPosParent='0'; $newPosContent='4';
			$query=build_SelectQuery('position',$newPosName); $db->setQuery($query);
			if (!$dbresult=$db->loadObject())
			{
				$query						= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,18); $db->setQuery($query);
				try
				{
					$result = $db->execute();
				}
				catch(Exception $e)
				{
					$app->enqueueMessage($e->getMessage(),'warning');
				}
				$ParentID					= $db->insertid();
				$ClubStaffPositions['0']	= $db->insertid();
			}
			else
			{
				$ParentID					= $dbresult->id;
				$ClubStaffPositions['0']	= $dbresult->id;
			}


			if ('AddGeneralClubstaffChildPositions' == 'AddGeneralClubstaffChildPositions')
			{
				// New Child position for ClubStaffPositions
				$newPosName='COM_JOOMLEAGUE_F_CLUB_MANAGER'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='4';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query); // hier die 4 als newposcontent einf�gen
				if (!$object=$db->loadObject())
				{
					$query						= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,19); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$ClubStaffPositions['1']	= $db->insertid();
				}
				else
				{
					$ClubStaffPositions['1']	= $object->id;
				}

				$newPosName='COM_JOOMLEAGUE_F_CLUB_YOUTH_MANAGER'; $newPosSwitch='persontype'; $newPosParent=$ParentID; $newPosContent='4';
				$query=build_SelectQuery('position',$newPosName); $db->setQuery($query); // hier die 4 als newposcontent einf�gen
				if (!$object=$db->loadObject())
				{
					$query						= build_InsertQuery_Position('position',$newPosName,$newPosSwitch,$newPosParent,$newPosContent,$sports_type->id,20); $db->setQuery($query);
					try
					{
						$result = $db->execute();
					}
					catch(Exception $e)
					{
						$app->enqueueMessage($e->getMessage(),'warning');
					}
					$ClubStaffPositions['2']	= $db->insertid();
				}
				else
				{
					$ClubStaffPositions['2']	= $object->id;
				}
			}
		}

		echo PrintStepResult($result).'<br />';
		if (!$result) { echo Text::_ ('DO NOT WORRY... Surely at least one of the positions was already existing in your database!!!').'<br />'; }
		echo '<br />';
	}

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	$result		= false;

	echo Text::sprintf('Adding standard position-related-events for soccer to table table [%s]','<b>'.'#__joomleague_position_eventtype'.'</b>');

	foreach ($PlayersPositions AS $ppkey => $ppid)
	{
		foreach ($events_player AS $epkey => $epid)
		{
			$query = build_InsertQuery_PositionEventType($ppid,$epid); 
			$db->setQuery($query); 
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
		}
	}

	foreach ($StaffPositions AS $spkey => $spid)
	{
		foreach ($events_staff AS $eskey => $esid)
		{
			$query=build_InsertQuery_PositionEventType($spid,$esid); 
			$db->setQuery($query); 
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
		}
	}

	foreach ($RefereePositions AS $rkey => $rid)
	{
		foreach ($events_referees AS $erkey => $erid)
		{
			$query=build_InsertQuery_PositionEventType($rid,$erid); 
			$db->setQuery($query); 
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
		}
	}

	foreach ($ClubStaffPositions AS $cskey => $csid)
	{
		foreach ($events_clubstaff AS $ecskey => $escid)
		{
			$query=build_InsertQuery_PositionEventType($csid,$escid); 
			$db->setQuery($query); 
			try
			{
				$result = $db->execute();
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(),'warning');
			}
		}
	}

	echo PrintStepResult($result).'<br />';
	if (!$result) { echo Text::_ ('DO NOT WORRY... Surely at least one of the position related events was already existing in your database!!!').'<br />'; }

	return '';
}

?>
<hr>
<?php
	$output=Text::sprintf(	'JoomLeague v%1$s - Update filedate/time: %2$s / %3$s %4$s',
								$version,$updateFileDate,$updateFileTime,'<br />'.$updateDescription.'<br />');
	JLToolBarHelper::title($output);

	echo addSportsType().'<br />';
	echo addStandardsForSoccer().'<br />';
?>