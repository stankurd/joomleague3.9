<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Event;

defined('_JEXEC') or die;
HTMLHelper::_('behavior.tooltip');
//HTMLHelper::_('bootstrap.tooltip');

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
<?php

	if (($this->config['show_sectionheader'])==1)
	{
		echo $this->loadTemplate('sectionheader');
	}

	echo $this->loadTemplate('projectheading');

	if (($this->config['show_result'])==1)
	{
	    echo $this->loadTemplate('details');
		echo $this->loadTemplate('result');
		echo $this->loadTemplate('timeline');
		echo $this->loadTemplate('events');
	}
	$p = 1;
	$selector = 'matchreport';
	echo HTMLHelper::_('bootstrap.startTabSet',$selector,array('active' => 'result'));
	
	if (($this->config['show_summary'])==1)
	{
	    echo HTMLHelper::_('bootstrap.addTab',$selector,'summary'.$p ++,Text::_('Komentarz',true));
	    echo $this->loadTemplate('summary');
	    echo HTMLHelper::_('bootstrap.endTab');
	}
	/*if (($this->config['show_details'])==1)
	{ 
	    echo HTMLHelper::_('bootstrap.addTab',$selector,'details'.$p ++,Text::_('Szczegoly',true));
		echo $this->loadTemplate('details');
		echo HTMLHelper::_('bootstrap.endTab');
	}
	*/
	if (($this->config['show_roster'])==1)
	{
	    echo HTMLHelper::_('bootstrap.addTab',$selector,'roster'.$p ++,Text::_('Sklady',true));
	    echo $this->loadTemplate('roster');
	    echo $this->loadTemplate('staff');
	    echo $this->loadTemplate('subst');
	    echo HTMLHelper::_('bootstrap.endTab');
		if (($this->config['use_tabs_events'])!=2)
		{
		    echo HTMLHelper::_('bootstrap.addTab',$selector,'subst'.$p ++,Text::_('Zmiany',true));
		    echo $this->loadTemplate('subst');
		    echo HTMLHelper::_('bootstrap.endTab');
		}
	}
	
	if (($this->config['show_stats'])==1)
	{
	    echo HTMLHelper::_('bootstrap.addTab',$selector,'stats'.$p ++,Text::_('statystyki',true));
	    echo $this->loadTemplate('stats');
	    echo HTMLHelper::_('bootstrap.endTab');
	}
	

	if ( !empty( $this->matchevents ) || !empty( $this->substitutes ) )
	{
		/*if (($this->config['show_timeline'])==1)
		{
		    echo HTMLHelper::_('bootstrap.addTab',$selector,'timeline'.$p ++,Text::_('timeline',true));
		   // echo $this->loadTemplate('timeline');
		    echo HTMLHelper::_('bootstrap.endTab');
		}
*/
		if (($this->config['show_events'])==1)
		{
			switch ($this->config['use_tabs_events'])
			{
				case 0:
					/** No tabs */
					if ( !empty( $this->eventtypes ) ) {
					    echo HTMLHelper::_('bootstrap.addTab',$selector,'events'.$p ++,Text::_('wydarzenia',true));
					    echo $this->loadTemplate('events');
					    echo HTMLHelper::_('bootstrap.endTab');
					}
					break;
				case 1:
					/** Tabs */
					if ( !empty( $this->eventtypes ) ) {
					    echo HTMLHelper::_('bootstrap.addTab',$selector,'events_tabs'.$p ++,Text::_('Zdarzenia',true));
					    echo $this->loadTemplate('events_tabs');
					    echo HTMLHelper::_('bootstrap.endTab');
					}
					break;
				case 2:
					/** Table/Ticker layout */
				    echo HTMLHelper::_('bootstrap.addTab',$selector,'events_ticker'.$p ++,Text::_('Event tiker',true));
				    echo $this->loadTemplate('events_ticker');
				    echo HTMLHelper::_('bootstrap.endTab');
					break;
			}
		}
	}
	if (($this->config['show_summary'])==1)
	{
	    echo HTMLHelper::_('bootstrap.addTab',$selector,'summary'.$p ++,Text::_('Podsumowanie',true));
	    echo $this->loadTemplate('summary');
	    echo HTMLHelper::_('bootstrap.endTab');
	}

	if (($this->config['show_extended'])==1)
	{
	    echo HTMLHelper::_('bootstrap.addTab',$selector,'extended'.$p ++,Text::_('Rozszerzone',true));
	    echo $this->loadTemplate('extended');
	    echo HTMLHelper::_('bootstrap.endTab');
	}
	echo HTMLHelper::_('bootstrap.endTabSet');
	
	
	// Comments integration
	PluginHelper::importPlugin( 'joomleague' );
	$dispatcher = JEventDispatcher::getInstance();
	$comments = '';
	if ($dispatcher->trigger( 'onNextMatchComments', array( &$this->match, $this->teams[0]->name .' - '. $this->teams[1]->name, &$comments ) )) {
	    echo $comments;
	}

	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	
	?>
</div>
