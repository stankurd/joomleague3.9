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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');

//Ordering allowed ?
$ordering = ( $this->lists['order'] == 'pre.ordering' );

HTMLHelper::_( 'behavior.tooltip' );
?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=predictionmembers'); ?>" method="post" id="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php
				echo Text::_( 'COM_JOOMLEAGUE_GLOBAL_FILTER' ); ?>: <input   type="text" name="search" id="search"
														value="<?php echo $this->lists['search'];?>" class="text_area"
														onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();">
					<?php
					echo Text::_( 'COM_JOOMLEAGUE_GLOBAL_GO' );
					?>
				</button>
				<button onclick="document.getElementById('search').value='';this.form.submit();">
					<?php
					echo Text::_( 'COM_JOOMLEAGUE_GLOBAL_RESET' );
					?>
				</button>
			</td>
			<td nowrap='nowrap' align='right'>
				<?php
				echo $this->lists['predictions'] . '&nbsp;&nbsp;';
				?>
			</td>
			<td nowrap='nowrap'>
				<?php
				echo $this->lists['state'];
				?>
			</td>
		</tr>
	</table>
	<div id="editcell">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="5">
						<?php
						echo Text::_( 'COM_JOOMLEAGUE_GLOBAL_NUM' );
						?>
					</th>
					<th width="20">
					<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="title" nowrap="nowrap">
						<?php
						echo HTMLHelper::_( 'grid.sort',  Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_USERNAME' ), 'u.username', $this->lists['order_Dir'], $this->lists['order'] );
						?>
					</th>
					<th class="title" nowrap="nowrap">
						<?php
						echo HTMLHelper::_( 'grid.sort',  Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_REAL_NAME' ), 'u.name', $this->lists['order_Dir'], $this->lists['order'] );
						?>
					</th>
					<th class="title" nowrap="nowrap">
						<?php
						echo HTMLHelper::_( 'grid.sort', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_PRED_NAME' ), 'p.name', $this->lists['order_Dir'], $this->lists['order'] );
						?>
					</th>
					<th class="title" nowrap="nowrap">
						<?php
						echo HTMLHelper::_( 'grid.sort', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_DATE_LAST_TIP' ), 'tmb.last_tipp', $this->lists['order_Dir'], $this->lists['order'] );
						?>
					</th>
					<th class="title">
						<?php
						echo HTMLHelper::_( 'grid.sort', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_SEND_REMINDER' ), 'tmb.reminder', $this->lists['order_Dir'], $this->lists['order'] );
						?>
					</th>
					<th class="title">
						<?php
						echo HTMLHelper::_( 'grid.sort', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_RECEIPT' ), 'tmb.receipt', $this->lists['order_Dir'], $this->lists['order'] );
						?>
					</th>
					<th class="title">
						<?php
						echo HTMLHelper::_( 'grid.sort', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_PROFILE' ), 'tmb.show_profile', $this->lists['order_Dir'], $this->lists['order'] );
						?>
					</th>
					<th class="title">
						<?php
						echo HTMLHelper::_( 'grid.sort', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_ADMIN_TIP' ), 'tmb.admintipp', $this->lists['order_Dir'], $this->lists['order'] );
						?>
					</th>
					<th width="1%">
						<?php
						echo HTMLHelper::_( 'grid.sort', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_APPROVED' ), 'tmb.approved', $this->lists['order_Dir'], $this->lists['order'] );
						?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan='11'>
						<?php
						echo $this->pagination->getListFooter();
						?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
      if ( isset($this->items) )
      {
			$k = 0;
			for ( $i = 0, $n = count( $this->items ); $i < $n; $i++ )
			{
				$row = $this->items[$i];

				$link	= Route::_( 'index.php?option=com_joomleague&task=prediction.edit&cid[]=' . $row->id );
				//$link2	= Route::_( 'index.php?option=com_users&view=user&layout=edit&cid[]=' . $row->user_id );
                $link2	= Route::_( 'index.php?option=com_joomleague&task=predictionmember.edit&cid[]=' . $row->id );

				$checked = HTMLHelper::_( 'grid.checkedout', $row, $i );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php
						echo $this->pagination->getRowOffset( $i );
						?>
					</td>
					<td>
						<?php
						echo $checked;
						?>
					</td>
					<td>
						<?php
						#if ( JTable::isCheckedOut($this->user->get( 'id' ), $row->checked_out ) )
						#{
						#	echo $row->username;
						#}
						#else
						{
						?>
							<a  href="<?php echo $link2; ?>"
								title="<?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_EDIT_USER' ); ?>" >
								<?php
								echo $row->username;
								?>
							</a>
							<?php
						}
						?>
					</td>
					<td>
						<?php
						#if ( JTable::isCheckedOut($this->user->get( 'id' ), $row->checked_out ) )
						#{
						#	echo $row->realname;
						#}
						#else
						{
						?>
							<?php /* ?>
							<a  href="<?php echo $link; ?>"
								title="<?php echo Text::_( 'Edit JoomLeague-Prediction User' ); ?>">
							<?php */ ?>
								<?php
								echo $row->realname;
								?>
							<?php /* ?>
							</a>
							<?php */ ?>
							<?php
						}
						?>
					</td>
					<td nowrap='nowrap'>
						<?php
						echo $row->predictionname;
						?>
					</td>
					<td style='text-align: center; '>
						<?php
						if ( isset( $row->last_tipp ) )
						{
							list( $date, $time ) = explode( " ", $row->last_tipp );
							$time = strftime( "H:M", strtotime( $time ) );
							echo JoomleagueHelper::convertDate( $date );
							echo ' / ';
							echo $time;
						}
						else
						{
							echo Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_NEVER_TIPPED' );
						}
						?>
					</td>
					<td style='text-align: center; '>
						<?php
						if ($row->reminder){$imgfile='ok.png';$imgtitle=Text::_('Active');}else{$imgfile='delete.png';$imgtitle=Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_INACTIVE');}
						echo HTMLHelper::_(	'image', 'administrator/components/com_joomleague/assets/images/' . $imgfile,
										$imgtitle, 'title= "' . $imgtitle . '"' );
						?>
					</td>
					<td style='text-align: center; '>
						<?php
						if ($row->receipt){$imgfile='ok.png';$imgtitle=Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_ACTIVE');}else{$imgfile='delete.png';$imgtitle=Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_INACTIVE');}
						echo HTMLHelper::_(	'image', 'administrator/components/com_joomleague/assets/images/' . $imgfile,
										$imgtitle, 'title= "' . $imgtitle . '"' );
						?>
					</td>
					<td style='text-align: center; '>
						<?php
						if ($row->show_profile){$imgfile='ok.png';$imgtitle=Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_ALLOWED');}else{$imgfile='delete.png';$imgtitle=Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_NOT_ALLOWED');}
						echo HTMLHelper::image(	'administrator/components/com_joomleague/assets/images/' . $imgfile,
											$imgtitle, 'title= "' . $imgtitle . '"' );
						?>
					</td>
					<td style='text-align: center; '>
						<?php
						if ($row->admintipp){$imgfile='ok.png';$imgtitle=Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_ACTIVE');}else{$imgfile='delete.png';$imgtitle=Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_INACTIVE');}
						echo HTMLHelper::_(	'image', 'administrator/components/com_joomleague/assets/images/' . $imgfile,
										$imgtitle, 'title= "' . $imgtitle . '"' );
						?>
					</td>
					<td style='text-align: center; '>
						<?php
						if ($row->approved){$imgfile='ok.png';$imgtitle=Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_APPROVED');}else{$imgfile='delete.png';$imgtitle=Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_NOT_APPROVED');}
						echo HTMLHelper::_(	'image', 'administrator/components/com_joomleague/assets/images/' . $imgfile,
										$imgtitle, 'title= "' . $imgtitle . '"' );
						?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
      }
			?>
			</tbody>
		</table>
	</div>

	
	<input type="hidden" name="view"				value="predictionmembers" />
	<input type='hidden' name='task'				value='predictionmember.display' />
  
	<input type="hidden" name="boxchecked"			value="0" />
	<input type="hidden" name="filter_order"		value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir"	value="" />
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>
