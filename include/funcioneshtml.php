<?php
/**
 *	Draws messages
 *  	@param $message - message text
 *      @param $draw
 *      @param $bullet
 *      @param $br
 *      @param $style
 */
function draw_message($message, $draw = true, $bullet = false, $br = false, $style = '')
{
	$output = '';
	if(!empty($style)) $style = ' style="'.$style.'"';
	if(!empty($message)) $output = '<table width="100%" align="center" class="message_box"'.$style.' border="0" cellspacing="1" cellpadding="1"><tr>'.(($bullet) ? '<td class="message_sign"><img src="images/attention_sign.gif" alt="attention" border="0" /></td>' : '').'<td class="message_text'.((!$bullet) ? '_single' : '').'">'.$message.'</td></tr></table>'.(($br == true) ? '<br />' : '');

	if($draw) echo $output;
	else return $output;
}


?>
