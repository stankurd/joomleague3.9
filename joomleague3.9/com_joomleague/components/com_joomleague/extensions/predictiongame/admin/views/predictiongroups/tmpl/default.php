<?php use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');

//Ordering allowed ?
$ordering=($this->lists['order'] == 's.ordering');

HTMLHelper::_('behavior.tooltip');HTMLHelper::_('behavior.modal');
?>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php
				echo Text::_('COM_JOOMLEAGUE_GLOBAL_FILTER');
				?>&nbsp;<input	type="text" name="search" id="search"
								value="<?php echo $this->lists['search']; ?>"
								class="text_area" onchange="$('adminForm').submit(); " />
				<button onclick="this.form.submit(); "><?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_GO'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit(); ">
					<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_RESET'); ?>
				</button>
			</td>
		</tr>
	</table>
	<div id="editcell">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="5"><?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_NUM'); ?></th>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th width="20">&nbsp;</th>
					<th>
						<?php echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_ADMIN_PREDICTIONGROUPS_NAME','s.name',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
					<th width="10%">
						<?php
						echo HTMLHelper::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ORDER','s.ordering',$this->lists['order_Dir'],$this->lists['order']);
						echo HTMLHelper::_('grid.order',$this->items, 'filesave.png', 'predictiongroup.saveorder');
						?>
					</th>
					<th width="20">
						<?php echo HTMLHelper::_('grid.sort','JGRID_HEADING_ID','s.id',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
				</tr>
			</thead>
			<tfoot><tr><td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
			<tbody>
				<?php
				$k=0;
				for ($i=0,$n=count($this->items); $i < $n; $i++)
				{
					$row = $this->items[$i];
					$link=Route::_('index.php?option=com_joomleague&task=predictiongroup.edit&cid[]='.$row->id);
					$checked=HTMLHelper::_('grid.checkedout',$row,$i);
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td class="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
						<td class="center"><?php echo $checked; ?></td>
						<?php
						if (JLTable::isCheckedOut($this->user->get('id'),$row->checked_out))
						{
							$inputappend=' disabled="disabled"';
							?><td class="center">&nbsp;</td><?php
						}
						else
						{
							$inputappend='';
							?>
							<td class="center">
								<a href="<?php echo $link; ?>">
									<?php
									$imageTitle=Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTIONGROUPS_EDIT_DETAILS');
									echo HTMLHelper::_(	'image','administrator/components/com_joomleague/assets/images/edit.png',
													$imageTitle,'title= "'.$imageTitle.'"');
									?>
								</a>
							</td>
							<?php
						}
						?>
						<td><?php echo $row->name; ?></td>
						<td class="order">
							<span>
								<?php echo $this->pagination->orderUpIcon($i,$i > 0,'predictiongroup.orderup','COM_JOOMLEAGUE_GLOBAL_ORDER_UP',$ordering); ?>
							</span>
							<span>
								<?php echo $this->pagination->orderDownIcon($i,$n,$i < $n,'predictiongroup.orderdown','COM_JOOMLEAGUE_GLOBAL_ORDER_DOWN',$ordering); ?>
								<?php $disabled=true ? '' : 'disabled="disabled"'; ?>
							</span>
							<input	type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?>
									class="text_area" style="text-align: center" />
						</td>
						<td class="center"><?php echo $row->id; ?></td>
					</tr>
					<?php
					$k=1 - $k;
				}
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo HTMLHelper::_('form.token')."\n"; ?>
</form>