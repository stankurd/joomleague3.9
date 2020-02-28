<?php
/**
* @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
* @license	GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterBase;

require_once 'joomleague.core.php';

class JoomleagueRouter extends RouterBase
{
    public function __construct($app = null, $menu = null)
    {
        $params = ComponentHelper::getParams('com_joomleague');
        
        if ($app)
        {
            $this->app = $app;
        }
        else
        {
            $this->app = Factory::getApplication();
        }
        
        if ($menu)
        {
            $this->menu = $menu;
        }
        else
        {
            $this->menu = $this->app->getMenu();
        }
        parent::__construct($app, $menu);
    }
    
   
    
    
/**
 * this array will be used to build and parse the segments
 *
 * @author And_One
 * 
 * @return object
 */
public function getRouteParametersObject() {
	$params = null;
	$params ['clubs'] = array (
			'p' => 0,
			'division' => 0 
	);
	$params ['clubinfo'] = array (
			'p' => 0,
			'cid' => 0,
			'task'=>'' 
	);
	$params ['clubplan'] = array (
	    'p' => 0,
	    'cid' => 0,
	    'task'=>''
	);
	$params ['curve'] = array (
			'p' => 0,
			'tid1' => 0,
			'tid2' => 0,
			'division' => 0 
	);
	$params ['division'] = array (
			'p' => 0,
			'division' => 0 
	);
	$params ['eventsranking'] = array (
			'p' => 0,
			'division' => 0,
			'tid' => 0,
			'evid' => 0,
			'mid' => 0 
	);
	$params ['matrix'] = array (
			'p' => 0,
			'division' => 0,
			'r' => 0 
	);
	$params ['matchreport'] = array (
			'p' => 0,
			'mid' => 0 
	);
	$params ['nextmatch'] = array (
			'p' => 0,
			'tid' => 0,
			'mid' => 0,
			'division' => 0 
	);
	$params ['player'] = array (
			'p' => 0,
			'tid' => 0,
			'pid' => 0,
			'task'=>''
	);
	$params ['playground'] = array (
			'p' => 0,
			'pgid' => 0 
	);
	$params ['ranking'] = array (
			'p' => 0,
			'type' => 0,
			'r' => 0,
			'from' => 0,
			'to' => 0,
			'division' => 0 
	);
	$params ['referees'] = array (
	    'p' => 0,
	);
	$params ['referee'] = array (
			'p' => 0,
			'pid' => 0 
	);
	$params ['results'] = array (
			'p' => 0,
			'r' => 0,
			'division' => 0,
			'mode' => 0,
			'order' => 0,
			'from' => 0 
	);
	$params ['resultsmatrix'] = array (
			'p' => 0,
			'r' => 0,
			'division' => 0,
			'mode' => 0,
			'order' => 0,
			'from' => 0 
	);
	$params ['resultsranking'] = array (
			'p' => 0,
			'r' => 0,
			'division' => 0,
			'mode' => 0,
			'order' => 0,
			'from' => 0 
	);
	$params ['rivals'] = array (
			'p' => 0,
			'tid' => 0 
	);
	$params ['roster'] = array (
			'p' => 0,
			'tid' => 0,
			'task'=>'',
			'division'=>0 
	);
	$params ['rosteralltime'] = array (
	    'p' => 0,
	    'tid' => 0,
	    'task'=>'',
	);
	$params ['teams'] = array (
			'p' => 0,
			'division' => 0 
	);
	$params ['teaminfo'] = array (
			'p' => 0,
			'tid' => 0 
	);
	$params ['teamstats'] = array (
			'p' => 0,
			'tid' => 0 
	);
	$params ['teamplan'] = array (
			'p' => 0,
			'tid' => 0,
			'division' => 0,
			'mode' => 0 
	);
	$params ['tree'] = array (
			'p' => 0,
			'division' => 0 
	);
	$params ['treeone'] = array (
			'p' => 0,
			'division' => 0 
	);
	$params ['treetonode'] = array (
			'p' => 0 
	);
	$params ['staff'] = array (
			'p' => 0,
			'tid' => 0,
			'pid' => 0,
			'task'=>''
	);
	$params ['stats'] = array (
			'p' => 0,
			'division' => 0 
	);
	$params ['statsranking'] = array (
			'p' => 0,
			'division' => 0,
			'tid' => 0 
	);
	$params ['rankingalltime'] = array (
	    'p' => 0,
	    'l' => 0,
	    'points' => 0,
	    
	);
	
	
	return $params;
}
function build(&$query) {
    $app = Factory::getApplication();
	$segments = array ();
	
	// include extensions routers for custom views - if extension does have a route file, use it
	$extensions = JoomleagueHelper::getExtensions ( 0 );
	foreach ( $extensions as $type ) {
		$file = JLG_PATH_SITE.'/extensions/'.$type.'/route.php';
		if (file_exists ( $file )) {
			require_once ($file);
			$obj = new $classname ();
			$func = 'build' . ucfirst ( $type );
			if ($segs = $func ( $query )) {
				return $segs;
			}
		}
	}
	
	$view = (isset ( $query ['view'] ) ? $query ['view'] : null);
	if ($view) {
		$segments [] = $view;
		unset ( $query ['view'] );
	} else {
		return $segments;
	}
	
	$params = $this->getRouteParametersObject ();
	if (isset ( $params [$view] )) {
		foreach ( $params [$view] as $key => $value ) {
			if (isset ( $query [$key] )) {
				$segments [] = $query [$key] ;
				unset ( $query [$key] );
			} else {
				$segments [] = $value;
			}
		}
	}
	return $segments;
	
}
public function parse(&$segments) {
    $app = Factory::getApplication();
    
    // include extensions routers for custom views - if extension route file exists, use it
	$extensions = JoomleagueHelper::getExtensions ( 0 );
	foreach ( $extensions as $type ) {
		$file = JLG_PATH_SITE.'/extensions/'.$type.'/route.php';
		if (file_exists ( $file )) {
			require_once ($file);
			$obj = new $classname ();
			$func = 'parse' . ucfirst ( $type );
			if ($vars = $func ( $segments )) {
				return $vars;
			}
		}
	}
	
	$vars = array ();
	$vars ['view'] = $segments [0];
	
	$params = $this->getRouteParametersObject ();
	if (isset ( $params [$vars ['view']] )) {
		$i = 1;
		foreach ( $params [$vars ['view']] as $key => $value ) {
			if (isset ( $segments [$i] )) {
				$vars [$key] = $segments [$i ++];
			}
		}
	}
	return $vars;
	
}
function joomleagueBuildRoute(&$query) {
    $app = Factory::getApplication();
    //$router = new JoomleagueRouter($app, $app->getMenu());
    $router = new JoomleagueRouter;
    return $router->build($query);
}
function joomleagueParseRoute($segments) {
    $app = Factory::getApplication();
    //$router = new JoomleagueRouter($app, $app->getMenu());
    $router = new JoomleagueRouter;   
    return $router->parse($segments);
}

}
