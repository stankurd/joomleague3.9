<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<table width='96%' align='center' border='0' cellpadding='0' cellspacing='0' class='venueinfo'>
	<tr class='sectiontableheader'>
		<th colspan='2'><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_DATA'); ?></th>
	</tr>
	<?php if (($this->config['show_shortname'])==1): ?>
	<tr>
		<th class='td_r' width='30%'><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_SHORT'); ?></th>
		<td width='70%'><?php echo $this->playground->short_name; ?></td>
	</tr>
	<?php endif; ?>

	<?php if ($this->playground->address || $this->playground->zipcode): ?>
	<tr>
		<th class='td_r_t' width='30%'><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_ADDRESS'); ?></th>
		<td width='70%'>
			<?php
			echo Countries::convertAddressString(	'',
													$this->playground->address,
													'',
													$this->playground->zipcode,
													$this->playground->city,
													$this->playground->country,
													'COM_JOOMLEAGUE_PLAYGROUND_ADDRESS_FORM');
			?>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($this->playground->website): ?>
	<tr>
		<th class='td_r' width='30%'><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_WEBSITE'); ?></th>
		<td><?php echo HTMLHelper::link($this->playground->website, $this->playground->website, array('target' => '_blank')); ?></td>
	</tr>
	<?php endif; ?>

	<?php if ($this->playground->max_visitors): ?>
	<tr>
		<th class='td_r' width='30%'><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_MAX_VISITORS'); ?></th>
		<td><?php echo $this->playground->max_visitors; ?></td>
	</tr>
	<?php endif; ?>
</table>
<br />
