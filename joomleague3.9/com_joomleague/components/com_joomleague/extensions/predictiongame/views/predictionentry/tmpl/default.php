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

defined('_JEXEC') or die('Restricted access');

//echo 'project_id<pre>'.print_r($this->model->predictionProject->project_id, true).'</pre><br>';

/*
$this->_addPath( 'template', JLG_PATH_EXTENSION_PREDICTIONGAME . '/views/predictionheading/tmpl' );
$this->_addPath( 'template', JPATH_COMPONENT . '/views/backbutton/tmpl' );
$this->_addPath( 'template', JPATH_COMPONENT . '/views/footer/tmpl' );
*/

$this->_addPath( 'template', JLG_PATH_EXTENSION_PREDICTIONGAME . DS . 'views' . DS . 'predictionheading' . DS . 'tmpl' );
$this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'backbutton' . DS . 'tmpl' );
$this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'footer' . DS . 'tmpl' );

?><div class='joomleague'><?php

	echo $this->loadTemplate('predictionheading');
	echo $this->loadTemplate('sectionheader');

	if ((!isset($this->actJoomlaUser)) || ($this->actJoomlaUser->id==0))
	{
		echo $this->loadTemplate('view_deny');
	}
	else
	{
		if ((!$this->isPredictionMember) && (!$this->allowedAdmin))
		{
			echo $this->loadTemplate('view_not_member');
		}
		else
		{
			if ($this->isNewMember){echo $this->loadTemplate('view_welcome');}

			if (!$this->tippEntryDone)
			{
				if (($this->config['show_help']==0)||($this->config['show_help']==2))
                {
                    echo $this->model->createHelptText($predictionProject->mode);
                }
                echo $this->loadTemplate('view_tippentry_do');
                echo $this->loadTemplate('matchday_nav');
            if (($this->config['show_help']==1)||($this->config['show_help']==2))
			{
				echo $this->model->createHelptText($predictionProject->mode);
			}
            
			}
			else
			{
				echo $this->loadTemplate('view_tippentry_done');
			}
		}
	}
  
  echo $this->loadTemplate('matchday_nav');
	
    echo '<div>';
		//backbutton
		echo $this->loadTemplate('backbutton');
		// footer
		echo $this->loadTemplate('footer');
	echo '</div>';

?></div>