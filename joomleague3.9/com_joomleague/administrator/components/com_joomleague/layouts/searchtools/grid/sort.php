<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$data = $displayData;

$metatitle = HTMLHelper::tooltipText(Text::_($data->tip ? $data->tip : $data->title), Text::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN'), 0);
HTMLHelper::_('bootstrap.tooltip');
?>
<a href="#" onclick="return false;" class="js-stools-column-order hasTooltip" data-order="<?php echo $data->order; ?>" data-direction="<?php echo strtoupper($data->direction); ?>" data-name="<?php echo Text::_($data->title); ?>" title="<?php echo $metatitle; ?>">
	<?php if (!empty($data->icon)) : ?>
		<span class="<?php echo $data->icon; ?>"></span>
	<?php endif; ?>
	<?php if (!empty($data->title)) : ?>
		<?php echo Text::_($data->title); ?>
	<?php endif; ?>
	<?php if ($data->order == $data->selected) : ?>
		<span class="<?php echo $data->orderIcon; ?>"></span>
	<?php endif; ?>
</a>