<?php

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
 defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.html.pane');
$document = Factory::getDocument();
$css = 'components/com_joomleague/assets/css/tabs.css';
$document->addStyleSheet($css);
$this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'projectheading' . DS . 'tmpl' );
$this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'backbutton' . DS . 'tmpl' );
$this->_addPath( 'template', JPATH_COMPONENT . DS . 'views' . DS . 'footer' . DS . 'tmpl' );
?>
<div class="joomleague">
<!-- General part of person view START -->
<?php

echo $this->loadTemplate( 'projectheading' );

if ($this->match)
{
	if (($this->config['show_sectionheader'])==1)
	{ 
		echo $this->loadTemplate('sectionheader');
	}
	
	if (($this->config['show_nextmatch'])==1)
	{ 
	    echo $this->loadTemplate('details');
		echo $this->loadTemplate('nextmatch');
	}
	    $p = 1;
	    $selector = 'nextmatch';
		echo HTMLHelper::_('bootstrap.startTabSet',$selector,array('active' => 'panel1'));
		
	if (($this->config['show_preview'])==1)
	{ 

		echo HTMLHelper::_('bootstrap.addTab',$selector,'panel'.$p ++,Text::_('Zapowiedz meczu',true));
		echo $this->loadTemplate('preview');
		echo HTMLHelper::_('bootstrap.endTab');
	}
	if (($this->config['show_details'])==1)
	{
		echo HTMLHelper::_('bootstrap.addTab',$selector,'panel'.$p ++,Text::_('Szczegoly meczu',true));
		echo $this->loadTemplate('details');
		echo HTMLHelper::_('bootstrap.endTab');		
		
	}	
	if (($this->config['show_stats'])==1)
	{ 
		echo HTMLHelper::_('bootstrap.addTab',$selector,'panel'.$p ++,Text::_('Porownanie bezposrednie',true));
		echo $this->loadTemplate('stats');
		echo HTMLHelper::_('bootstrap.endTab');		
		
	}
	
	if (($this->config['show_history'])==1)
	{ 
		echo HTMLHelper::_('bootstrap.addTab',$selector,'panel'.$p ++,Text::_('Historia spotkan',true));
		echo $this->loadTemplate('history');
		echo HTMLHelper::_('bootstrap.endTab');
		
	}
		echo HTMLHelper::_('bootstrap.endTabSet');
?>

<?php

	// Comments integration
	PluginHelper::importPlugin( 'joomleague' );
	$dispatcher = JEventDispatcher::getInstance();
	$comments = '';
	if ($dispatcher->trigger( 'onNextMatchComments', array( &$this->match, $this->teams[0]->name .' - '. $this->teams[1]->name, &$comments ) )) {
		echo $comments;
	}
	
	//backbutton
	echo "<div>";
	echo $this->loadTemplate('backbutton');
	// footer 
	echo $this->loadTemplate('footer');
	echo "</div>";
}
else
{
	echo "<p>" . JText::_('JL_NEXTMATCH_NO_MORE_MATCHES') . "</p>";
}
?>
</div>