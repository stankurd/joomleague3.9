<?php 

defined('_JEXEC') or die;

$style  = 'style="background-color: '.$this->config['tree_bg_colour'].';';
$style .= 'border: 1px solid  '.$this->config['tree_border_colour'].';';
//$style .= 'font-weight: bold; white-space:nowrap;';
$style .= 'font-size: '.$this->config['tree_fontsize'].'pt;';
$style .= 'width: '.$this->config['tree_width'].'px;';
$style .= 'font-family: verdana; ';
$style .= 'text-align: left; padding-left: 10px;"';

	if($this->config['tree_bracket_type']==0){
		$path = 'media/com_joomleague/treebracket/onwhite/';
	}
	elseif($this->config['tree_bracket_type']==1){
		$path = 'media/com_joomleague/treebracket/onblack/';
	}

$treedl='treedl.gif';
$treeul='treeul.gif';
$treecl='treecl.gif';
$treedr='treedr.gif';
$treeur='treeur.gif';
$treecr='treecr.gif';
$treep='treep.gif';
$treeh='treeh.gif';

$dl='<img src="'.$path.$treedl.'" alt="|" width="16" height="18">';
$ul='<img src="'.$path.$treeul.'" alt="|" width="16" height="18">';
$cl='<img src="'.$path.$treecl.'" alt="|-" width="16" height="18">';
$dr='<img src="'.$path.$treedr.'" alt="|" width="16" height="18">';
$ur='<img src="'.$path.$treeur.'" alt="|" width="16" height="18">';
$cr='<img src="'.$path.$treecr.'" alt="-|" width="16" height="18">';
$p='<img src="'.$path.$treep.'" alt="|" width="16" height="18">';
$h='<img src="'.$path.$treeh.'" alt="-" width="16" height="18">';


if(!$this->node)
{
	echo 'first generate at least one tree in this project ... or select one from dropdown list [by ID] due creating menu position on backend';
}
else
{
	$i=$this->node[0]->tree_i;		//depth
	$hide=$this->node[0]->hide;		//hide	
?>
<table border="0" cellpadding="0" cellspacing="0" >
<?php
$i=$i-1;                            	//depth
$r=2*(pow(2,$i)); 			//rows
$c=2*$i+1;                              //columns
        $col_hide=$c-2*$hide;			//hiden col
        
for($j=1;$j<$r;$j++)
{
        if($this->node[$j-1]->published ==0)
	{
		;
	}
	else
	{
		echo '<tr>';
		echo '<td height="18"></td>';
		for($k=1;$k<=$c;$k++)
		{
			if($k > $col_hide)
			{
				;
			}
			else
			{
                                echo '<td ';
		          	for($w=0;$w<=$i;$w++)
			        {
				        if(( $k == (1+($w*2)) ) && ( $j % (2*(pow(2,$w))) == (pow(2,$w)) ))
				        {
                                                echo "$style";
				        }
			         }
			         echo ' >';
                                for($w=0;$w<=$i;$w++)
			        {
				       if(( $k == (1+($w*2)) ) && ( $j % (2*(pow(2,$w))) == (pow(2,$w)) ))
				        {
// node_________________________________________________________________________________________
				$jk=$j*2-(pow(2,$w));   //aka 1st line
				$jp=$j*2+(pow(2,$w));   //aka 2nd line
				$kp=($jk+$jp)/2;    //parent node have got child team?

				if(($this->node[$kp-1]->team_name) == ($this->node[$jk-1]->team_name))
				{
				       echo '<b>';
				}
				echo '&nbsp;'.$this->node[$jk-1]->team_name;
				if(($this->node[$kp-1]->team_name) == ($this->node[$jk-1]->team_name))
				{
				        echo '</b>';
				}
				echo '<br/>';
				if(($this->node[$kp-1]->team_name) == ($this->node[$jp-1]->team_name))
				{
				       echo '<b>';
				}
				echo '&nbsp;'.$this->node[$jp-1]->team_name;
				if(($this->node[$kp-1]->team_name) == ($this->node[$jp-1]->team_name))
				{
				        echo '</b>';
				}
// node___________________________________________________________________________________________
			                }
				        elseif(( $k == (2+($w*2)) ) && ( $j % (4*(pow(2,$w))) == (pow(2,$w)) ))
				        {
					       echo "$dl";
				        }
				        elseif(( $k == (2+($w*2)) ) && ( $j % (4*(pow(2,$w))) == (2*(pow(2,$w))) ))
				        {
					       if($this->node[$j-1]->is_leaf == 1)
					       {
						      ;
					       }
					       else
					       {
						      echo "$cl";
					       }
				        }
				        elseif(( $k == (2+($w*2)) ) && ( $j % (4*(pow(2,$w))) == (3*(pow(2,$w))) ))
				        {
					       echo "$ul";
				        }
				        elseif(( $k == (2+($w*2)) ) && ( ( $j % (4*(pow(2,$w))) > (pow(2,$w)) ) && ( $j % (4*(pow(2,$w))) < (3*(pow(2,$w))) ) ))
				        {
					       echo "$p";
				        }
				        else
				        {
					       ;
				        }
			         }
			         echo '</td>';
		          }
		}
		echo '</tr>';
	}
}
?>

</table>

<?php
}
?>