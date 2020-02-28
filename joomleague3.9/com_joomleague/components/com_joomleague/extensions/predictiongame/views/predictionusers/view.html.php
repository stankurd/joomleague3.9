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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\Field\MediaField;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
//require_once(JLG_PATH_EXTENSION_PREDICTIONGAME . DS . 'helpers' . DS . 'imageselect.php');

/**
 * Joomleague Component prediction View
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
class JoomleagueViewPredictionUsers extends JLGView
{

	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document	= Factory::getDocument();
		
		$document->addScript(Uri::root().'components/com_joomleague/assets/js/json2.js');
		$document->addScript(Uri::root().'components/com_joomleague/assets/js/swfobject.js');
		
		$model		= $this->getModel();

		$this->predictionGame = $model->getPredictionGame();

		if (isset($this->predictionGame))
		{
			$config				= $model->getPredictionTemplateConfig($this->getName());
			$overallConfig		= $model->getPredictionOverallConfig();
			$tipprankingconfig	= $model->getPredictionTemplateConfig('predictionranking');
			$flashconfig 		= $model->getPredictionTemplateConfig( "predictionflash" );
			//$rankingconfig	= $model->getPredictionTemplateConfig('ranking');

			$this->model = $model;
			$this->roundID = $this->model->roundID;
			$this->config =	array_merge($overallConfig,$tipprankingconfig,$config);

			$this->predictionMember = $model->getPredictionMember();
			if (!isset($this->predictionMember->id))
			{
				$this->predictionMember->id=0;
				$this->predictionMember->pmID=0;
			}
			$this->predictionProjectS = $model->getPredictionProjectS();

			$this->actJoomlaUser = Factory::getUser();
			$this->isPredictionMember = $model->checkPredictionMembership();
			$this->memberData = $model->memberPredictionData();
			$this->allowedAdmin = $model->getAllowed();
			
			if (!empty($this->predictionMember->user_id)) {
				$this->showediticon = $model->getAllowed($this->predictionMember->user_id);
			}
			
			//$this->_setPointsChartdata(array_merge($flashconfig, $config));
			//$this->_setRankingChartdata(array_merge($flashconfig, $config));
			//echo '<br /><pre>~' . print_r($this->predictionMember,true) . '~</pre><br />';

			$lists = array();

			if ($this->predictionMember->pmID > 0){$dMemberID=$this->predictionMember->pmID;}else{$dMemberID=0;}
			if (!$this->allowedAdmin){$userID=$this->actJoomlaUser->id;}else{$userID=null;}
			$predictionMembers[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_PRED_SELECT_MEMBER'),'value','text');

			if ($res=$model->getPredictionMemberList($this->config,$userID)){$predictionMembers=array_merge($predictionMembers,$res);}
			$lists['predictionMembers']=HTMLHelper::_('select.genericList',$predictionMembers,'uid','class="inputbox" onchange="this.form.submit(); "','value','text',$dMemberID);
			unset($res);
			unset($predictionMembers);

			if (empty($this->predictionMember->fav_team)){$this->predictionMember->fav_team='0,0';}
			$sFavTeamsList=explode(';',$this->predictionMember->fav_team);
			foreach ($sFavTeamsList AS $key => $value){$dFavTeamsList[]=explode(',',$value);}
			foreach ($dFavTeamsList AS $key => $value){$favTeamsList[$value[0]]=$value[1];}

			//echo '<br /><pre>~' . print_r($this->predictionMember->champ_tipp,true) . '~</pre><br />';
			if (empty($this->predictionMember->champ_tipp)){$this->predictionMember->champ_tipp='0,0';}
			$sChampTeamsList=explode(';',$this->predictionMember->champ_tipp);
			foreach ($sChampTeamsList AS $key => $value){$dChampTeamsList[]=explode(',',$value);}
			foreach ($dChampTeamsList AS $key => $value){$champTeamsList[$value[0]]=$value[1];}

			if ($this->getLayout()=='edit')
			{
				$dArray[] = HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_GLOBAL_NO'));
				$dArray[] = HTMLHelper::_('select.option',1,Text::_('COM_JOOMLEAGUE_GLOBAL_YES'));

				$lists['show_profile']		= HTMLHelper::_('select.radiolist',$dArray,'show_profile',	'class="inputbox" size="1"','value','text',$this->predictionMember->show_profile);
				$lists['reminder']			= HTMLHelper::_('select.radiolist',$dArray,'reminder',		'class="inputbox" size="1"','value','text',$this->predictionMember->reminder);
				$lists['receipt']			= HTMLHelper::_('select.radiolist',$dArray,'receipt',		'class="inputbox" size="1"','value','text',$this->predictionMember->receipt);
				$lists['admintipp']			= HTMLHelper::_('select.radiolist',$dArray,'admintipp',		'class="inputbox" size="1"','value','text',$this->predictionMember->admintipp);
				$lists['approvedForGame']	= HTMLHelper::_('select.radiolist',$dArray,'approved',		'class="inputbox" size="1" disabled="disabled"','value','text',$this->predictionMember->approved);
				unset($dArray);

				foreach ($this->predictionProjectS AS $predictionProject)
				{
					//echo '<br /><pre>~' . print_r($predictionProject,true) . '~</pre><br />';
					$projectteams[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_PRED_USERS_SELECT_TEAM'),'value','text');
					if ($res=$model->getPredictionProjectTeams($predictionProject->project_id))
					{
						$projectteams = array_merge($projectteams,$res);
					}
					if (!isset($favTeamsList[$predictionProject->project_id])){$favTeamsList[$predictionProject->project_id]=0;}
					$lists['fav_team'][$predictionProject->project_id] = HTMLHelper::_('select.genericList',$projectteams,'fav_team['.$predictionProject->project_id.']','class="inputbox"','value','text',$favTeamsList[$predictionProject->project_id]);

					if (!$model->getChampTippAllowed()){$disabled=' disabled="disabled" ';}else{$disabled='';}
					if (!isset($champTeamsList[$predictionProject->project_id])){$champTeamsList[$predictionProject->project_id]=0;}
					$lists['champ_tipp_disabled'][$predictionProject->project_id] = HTMLHelper::_('select.genericList',$projectteams,'champ_tipp['.$predictionProject->project_id.']','class="inputbox'.$disabled.'"','value','text',$champTeamsList[$predictionProject->project_id]);
					$lists['champ_tipp_enabled'][$predictionProject->project_id] = HTMLHelper::_('select.genericList',$projectteams,'champ_tipp['.$predictionProject->project_id.']','class="inputbox"','value','text',$champTeamsList[$predictionProject->project_id]);
					unset($projectteams);
				}

				// image selector
				$default = 'media/com_joomleague/placeholders/placeholder_150_2_png';
				if (empty($this->predictionMember->picture)){$this->predictionMember->picture=$default;}
				$imageselect = ImageSelect::getSelector('picture','picture_preview','predictionusers',$this->predictionMember->picture,$default,'','');
		        //$imageselect = MediaHelper::canUpload($file, $component = 'com_media');
				
				$this->imageselect = $imageselect;
			}
			else
			{
				$this->favTeams = $favTeamsList;
				$this->champTeams =	$champTeamsList;
			}

			$this->lists = $lists;

			// Set page title
			$pageTitle = Text::_('COM_JOOMLEAGUE_PRED_USERS_TITLE');

			$document->setTitle($pageTitle);

			parent::display($tpl);
		}
		else
		{
			Factory::getApplication()->enqueueMessage(500,Text::_('COM_JOOMLEAGUE_PRED_PREDICTION_NOT_EXISTING'),'notice');
		}



	}
/*
	function _setPointsChartdata($config)
	{
		require_once( JLG_PATH_SITE.DS."assets".DS."classes".DS."open-flash-chart".DS."open-flash-chart.php" );

		$data = $this->get('PointsChartData');

		// Calculate Values for Chart Object
		$userpoints= array();		
		$round_labels = array();

		foreach( $data as $rw )
		{
			if (!$rw->points) $rw->points = 0;
			$userpoints[] = (int)$rw->points;
			$round_labels[] = $rw->roundcode;		
		}

		
		$chart = new open_flash_chart();
		$chart->set_bg_colour($config['bg_colour']);
		
	if(!empty($userpoints))
	{
		$bar = new $config['bartype_1']();
		$bar->set_values( $userpoints);	
		$bar->set_tooltip( Text::_('COM_JOOMLEAGUE_PRED_USER_POINTS'). ": #val#" );
		$bar->set_colour( $config['bar1'] );
		$bar->set_on_show(new bar_on_show($config['animation_1'], $config['cascade_1'], $config['delay_1']));

		$chart->add_element( $bar );
	}
		//X-axis
		$x = new x_axis();
		$x->set_colours($config['x_axis_colour'], $config['x_axis_colour_inner']);
		$x->set_labels_from_array($round_labels);
		$chart->set_x_axis( $x );
		$x_legend = new x_legend( Text::_('COM_JOOMLEAGUE_PRED_USER_ROUNDS') );
		$x_legend->set_style( '{font-size: 15px; color: #778877}' );
		$chart->set_x_legend( $x_legend );

		//Y-axis
		$y = new y_axis();
		$y->set_range( 0, @max($userpoints)+2, 1);
		$y->set_steps(round(@max($userpoints)/8));
		$y->set_colours($config['y_axis_colour'], $config['y_axis_colour_inner']);
		$chart->set_y_axis( $y );
		$y_legend = new y_legend( Text::_('COM_JOOMLEAGUE_PRED_USER_POINTS') );
		$y_legend->set_style( '{font-size: 15px; color: #778877}' );
		$chart->set_y_legend( $y_legend );
		
		$this->pointschartdata = $chart;
	}

	function _setRankingChartdata($config)
	{
		require_once( JLG_PATH_SITE.DS."assets".DS."classes".DS."open-flash-chart".DS."open-flash-chart.php" );

		//$data = $this->get('RankChartData');		
		//some example data....fixme!!!
		$data_1 = array();
		$data_2 = array();

		for( $i=0; $i<6.2; $i+=0.2 )
		{
			$data_1[] = (sin($i) * 1.9) + 10;
		}

		for( $i=0; $i<6.2; $i+=0.2 )
		{
			$data_2[] = (sin($i) * 1.3) + 10;
		}
		
		$chart = new open_flash_chart();
		//***********
		
		//line 1
		$d = new $config['dotstyle_1']();
		$d->size((int) $config['line1_dot_strength']);
		$d->halo_size(1);
		$d->colour($config['line1']);
		$d->tooltip('Rank: #val#');

		$line = new line();
		$line->set_default_dot_style($d);
		$line->set_values( $data_1 );
		$line->set_width( (int) $config['line1_strength'] );
		///$line->set_key($team->name, 12);
		$line->set_colour( $config['line1'] );
		$line->on_show(new line_on_show($config['l_animation_1'], $config['l_cascade_1'], $config['l_delay_1']));
		$chart->add_element($line);
		
		//Line 2
		$d = new $config['dotstyle_2']();
		$d->size((int) $config['line2_dot_strength']);
		$d->halo_size(1);
		$d->colour($config['line2']);
		$d->tooltip('Rank: #val#');

		$line = new line();
		$line->set_default_dot_style($d);
		$line->set_values( $data_2);
		$line->set_width( (int) $config['line2_strength'] );
		//$line->set_key($team->name, 12);
		$line->set_colour( $config['line2'] );
		$line->on_show(new line_on_show($config['l_animation_2'], $config['l_cascade_2'], $config['l_delay_2']));
		$chart->add_element($line);
		
		//X-axis
		$x = new x_axis();
		$x->set_colours($config['x_axis_colour'], $config['x_axis_colour_inner']);
		//$x->set_labels_from_array($round_labels);
		$chart->set_x_axis( $x );
		$x_legend = new x_legend( Text::_('COM_JOOMLEAGUE_PRED_USER_ROUNDS') );
		$x_legend->set_style( '{font-size: 15px; color: #778877}' );
		$chart->set_x_legend( $x_legend );

		//Y-axis
		$y = new y_axis();
		$y->set_range( 0, @max($data_1)+2, 1);
		$y->set_steps(round(@max($data_1)/8));
		$y->set_colours($config['y_axis_colour'], $config['y_axis_colour_inner']);
		$chart->set_y_axis( $y );
		$y_legend = new y_legend( Text::_('COM_JOOMLEAGUE_PRED_USER_POINTS') );
		$y_legend->set_style( '{font-size: 15px; color: #778877}' );
		$chart->set_y_legend( $y_legend );
		
		$this->rankingchartdata = $chart;
	}*/
}
?>