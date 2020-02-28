<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<div style='width: 100%; float: left'>
	<div class='contentpaneopen'>
		<div class='contentheading'>
			<?php echo Text::_('COM_JOOMLEAGUE_GMAP_DIRECTIONS'); ?>
		</div>
	</div>
	<?php
		$arrPluginParams = array();
		
		$param = 'zoom_wheel';
		if ($this->mapconfig[$param])
		{
			$arrPluginParams[] = "zoomWheel='".$this->mapconfig[$param]."'";
		}
		
		$param = 'default_map_type';
		if ($this->mapconfig[$param])
		{
			$arrPluginParams[] = "mapType='".$this->mapconfig[$param]."'";
		}
		$param = 'map_control';
		if ($this->mapconfig[$param])
		{
			$arrPluginParams[] = "zoomType='".$this->mapconfig[$param]."'";
		}
		$param = 'width';
		if ($this->mapconfig[$param])
		{
			$arrPluginParams[] = "$param='".$this->mapconfig[$param]."'";
		}
		$param = 'height';
		if ($this->mapconfig[$param])
		{
			$arrPluginParams[] = "$param='".$this->mapconfig[$param]."'";
		}
		$param = 'zoom';
		if ($this->mapconfig[$param])
		{
			$arrPluginParams[] = "$param='".$this->mapconfig[$param]."'";
		}	
			
		if ($this->address_string != '')
		{
			$arrPluginParams[] = "address='" .$this->address_string. "'";
			$arrPluginParams[] = "text='<div style=width:250px;height=30px;>".$this->address_string."</div>'";
		}
		$icon = '';
		if ($this->playground->picture != '')
		{
			$arrPluginParams[] = "tooltip='". $this->playground->name . "'";
			$icon = $this->playground->picture;
		}
		if ($icon != '')
		{
			$arrPluginParams[] = "icon='".$icon."'";
		}
		$params  = '{mosmap ';
		$params .= implode('|', $arrPluginParams);
		$params .= "}";
		echo HTMLHelper::_('content.prepare', $params);
	?>
</div>