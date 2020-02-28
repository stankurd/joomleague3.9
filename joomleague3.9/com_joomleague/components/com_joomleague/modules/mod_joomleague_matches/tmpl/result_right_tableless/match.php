<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
?>
<div id="modJLML<?php echo $module->id.'_row'.$cnt;?>" class="<?php echo $styleclass;?> jlmlmatchholder">
<!--jlml-mod<?php echo $module->id.'nr'.$cnt;?> start-->
  <?php
if ($heading != $lastheading) {
?><div class="contentheading">
      <?php echo $heading;?>
    </div>
  <?php
}
  if ($show_pheading) {
?><div class="<?php echo $params->get('heading_style');?>">
      <?php echo $pheading;?>
    </div>
<?php
}
?>
    <div class="jlmlDateHolder">
      <?php
      if (!empty($match['location'])) echo '<span style="white-space:nowrap;">'.$match['location'].'</span> ';
      echo ' <span style="white-space:nowrap;">'.$match['date'].'</span> '
      .' <span style="white-space:nowrap;">'.$match['time'].'</span> ';
      if (isset($match['meeting'])) echo' <span style="white-space:nowrap;">'.$match['meeting'].'</span> ';
      ?>

  </div>
 <div class="jlmlmatchholder">

    <span class="jlmlteamcol jlmlleft">
      <?php
    if (!empty($match['hometeam']['logo'])) {
      echo '<img src="'.$match['hometeam']['logo']['src'].'" alt="'.$match['hometeam']['logo']['alt'].'" title="'.$match['hometeam']['logo']['alt'].'" '.$match['hometeam']['logo']['append'].' />';
      if($params->get('new_line_after_logo') == 1) { echo '<br />'; }
    }
      if($params->get('show_names') == 1) { echo $match['hometeam']['name']; }
      if (!empty($match['homeover'])) echo $match['homeover'];
      ?>
    </span>

      <span class="jlmlMatchLinks">
    <?php
  
    if (!empty($match['awayteam']['logo']) && $params->get('new_line_after_logo') == 1) { echo '<br />'; }
    if ($match['reportlink'] OR $match['statisticlink'] OR $match['nextmatchlink']) {  
      if ($match['reportlink']) { 
      	echo $match['reportlink']; 
      }
      if ($match['statisticlink']) { 
      	echo $match['statisticlink']; 
      }
      if ($match['nextmatchlink']) {
      	echo $match['nextmatchlink'];
      }
    } 
    else { echo ' - '; }?>
      </span>
    
    <span class="jlmlteamcol jlmlright">
      <?php
    if (!empty($match['awayteam']['logo'])) {
      echo '<img src="'.$match['awayteam']['logo']['src'].'" alt="'.$match['awayteam']['logo']['alt'].'" title="'.$match['awayteam']['logo']['alt'].'" '.$match['awayteam']['logo']['append'].' />';
      if($params->get('new_line_after_logo') == 1) { echo '<br />'; }
    }
      if($params->get('show_names') == 1) { echo $match['awayteam']['name']; }
      if (!empty($match['awayover'])) echo $match['awayover'];
      ?>
    </span>
  
   
    <span class="jlmlResults">
    <?php
    if (!empty($match['awayteam']['logo']) && $params->get('new_line_after_logo') == 1) { echo '<br />'; }
      echo $match['result']; 
    ?>
    <?php
      if (!empty($match['partresults'])) { ?>
       <span class="jlmlPartResults"><?php echo $match['partresults'];?></span>
      <?php } ?>
      </span>
      
 </div>
  <?php
    if (($params->get('show_referee') == 1 && !empty($match['referee'])) OR ($params->get('show_spectators') == 1 && !empty($match['spectators']))) { ?>
    <div style="width:100%;display:block;clear:both;">
      <?php 
      echo $match['referee'] . ' '. $match['spectators'];
      ?>
    </div>
<?php
}
  if (!empty($match['notice'])) { ?>
    <div style="width:100%;display:block;clear:both;">
      <?php 
      echo $match['notice'];
      ?>
    </div>
<?php
}
if ($match['ajax']) echo $match['ajax'];
$limit = (int) $params->get("limit"); 
if($limit>1) {
?>
<hr style="width:100%;display:block;clear:both;margin-top:10px;" />
<?php } ?>
<!--jlml-mod<?php echo $module->id.'nr'.$cnt;?> end-->
</div>
<?php
 if($ajax && $ajaxmod==$module->id){ exit(); } ?>