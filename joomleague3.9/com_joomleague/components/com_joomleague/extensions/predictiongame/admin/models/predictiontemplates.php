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


// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once ( JPATH_COMPONENT . DS . 'models' . DS . 'list.php' );

/**
 * Joomleague Component prediction templates Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100625
 */

class JoomleagueModelPredictionTemplates extends JoomleagueModelList
{

	var $_identifier = "predtemplates";
	
	var $_prediction_id	= null;

	function __construct()
	{
		$app = JFactory::getApplication();

		parent::__construct();
		$prediction_id = $app->getUserState( 'com_joomleague' . 'prediction_id', 0 );
		$this->set( '_prediction_id', $prediction_id );
	}

	function getData()
	{
		$this->checklist();
		return parent::getData();
	}

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = 	'	SELECT	tmpl.* ,
								u.name AS editor

						FROM #__joomleague_prediction_template AS tmpl
						LEFT JOIN #__users AS u ON u.id = tmpl.checked_out ' . $where . $orderby;
		return $query;
	}

	function _buildContentWhere()
	{
		$app				= JFactory::getApplication();
		$option				= 'com_joomleague';

		$filter_order		= $app->getUserStateFromRequest( $option . 'tmpl_filter_order',		'filter_order',		'tmpl.title',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'tmpl_filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		$where = array();
		$prediction_id = (int) $app->getUserState( 'com_joomleague' . 'prediction_id' );
		if ( $prediction_id > 0 )
		{
			$where[] = 'tmpl.prediction_id = ' . $prediction_id;
		}
		$where 	= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildContentOrderBy()
	{
		$app			= JFactory::getApplication();
		$option				= 'com_joomleague';

		$filter_order		= $app->getUserStateFromRequest( $option . 'tmpl_filter_order',		'filter_order',		'tmpl.title',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'tmpl_filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		if ( $filter_order == 'tmpl.title' )
		{
			$orderby 	= ' ORDER BY tmpl.title ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , tmpl.title ';
		}

		return $orderby;
	}

	/**
	* Method to return a prediction games array
	*
	* @access  public
	* @return  array
	*/
	function getPredictionGames()
	{
		$query = "	SELECT	id AS value,
							name AS text
					FROM #__joomleague_prediction_game
					ORDER by name";

		$this->_db->setQuery( $query );

		if ( !$result = $this->_db->loadObjectList() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		else
		{
			return $result;
		}
	}

	/**
	* Method to return a prediction game item array
	*
	* @access  public
	* @return  object
	*/
	function getPredictionGame($id)
	{
		$query = '	SELECT	*
					FROM #__joomleague_prediction_game
					WHERE id = ' . (int) $id;

		$this->_db->setQuery( $query );
		if ( !$result = $this->_db->loadObject() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		else
		{
			return $result;
		}
	}

	/**
	 * check that all prediction templates in default location have a corresponding record, except if game has a master template
	 *
	 */
	function checklist()
	{
		$prediction_id	= $this->_prediction_id;
		$defaultpath	= JLG_PATH_EXTENSION_PREDICTIONGAME.DS.'settings';
		$extensionspath	= JPATH_COMPONENT_SITE . DS . 'extensions' . DS;
		$templatePrefix	= 'prediction';

		if (!$prediction_id){return;}

		// get info from prediction game
		$query = 'SELECT master_template 
					FROM #__joomleague_prediction_game 
					WHERE id = ' . (int) $prediction_id;

		$this->_db->setQuery($query);
		$params = $this->_db->loadObject();

		// if it's not a master template, do not create records.
		if ($params->master_template){return true;}

		// otherwise, compare the records with the files // get records
		$query = 'SELECT template 
					FROM #__joomleague_prediction_template 
					WHERE prediction_id = ' . (int) $prediction_id;

		$this->_db->setQuery($query);
		//$records = $this->_db->loadResultArray();
		$records = $this->_db->loadcolumn();

		if (empty($records)){$records=array();}

		// first check extension template folder if template is not default
		if ((isset($params->extension)) && ($params->extension!=''))
		{
			if (is_dir($extensionspath . $params->extension . DS . 'settings'))
			{
				$xmldirs[] = $extensionspath . $params->extension . DS . 'settings';
			}
		}

		// add default folder
		$xmldirs[] = $defaultpath . DS . 'default';

		// now check for all xml files in these folders
		foreach ($xmldirs as $xmldir)
		{
			if ($handle = opendir($xmldir))
			{
				/* check that each xml template has a corresponding record in the
				database for this project. If not, create the rows with default values
				from the xml file */
				while ($file = readdir($handle))
				{
					if ($file!='.'&&$file!='..'&&strtolower(substr($file,(-3)))=='xml'&&
						strtolower(substr($file,0,strlen($templatePrefix)))==$templatePrefix)
					{
						$template = substr($file,0,(strlen($file)-4));

						if ((empty($records)) || (!in_array($template,$records)))
						{
							//template not present, create a row with default values
							$params = new JLParameter(null, $xmldir . DS . $file);

							//get the values
							$defaultvalues = array();
							foreach ($params->getGroups() as $key => $group)
							{
								foreach ($params->getParams('params',$key) as $param)
								{
									$defaultvalues[] = $param[5] . '=' . $param[4];
								}
							}
							$defaultvalues = implode('\n', $defaultvalues);

							$title = JText::_($params->name);
							$query =	"	INSERT INTO #__joomleague_prediction_template (title, prediction_id, template, params)
											VALUES ( '$title', '$prediction_id', '$template', '$defaultvalues' )";

							$this->_db->setQuery($query);
							//echo error, allows to check if there is a mistake in the template file
							if (!$this->_db->execute())
							{
								$this->setError($this->_db->getErrorMsg());
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

}
?>