<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
function compress()
{
$CI =& get_instance();
$buffer = $CI->output->get_output();

$inline_scripts=array();
if (preg_match_all('|([removed]]*?&gt;.*?<\/script>)|is', $buffer, $pock))
{
foreach ($pock[1] as $key=>$content)
{
$inline_scripts['INLINE_SCRIPT_'.$key]=$content;
}
$buffer=str_replace(array_values($inline_scripts), array_keys($inline_scripts), $buffer);
}

$search = array(
'/\>[^\S ]+/s',
'/[^\S ]+\</s',
'/(\s)+/s',
);
$replace = array(
'>',
'<',
'\\1',
);

$buffer = preg_replace($search, $replace, $buffer);

if (count($inline_scripts)>0)
{
$buffer=str_replace(array_keys($inline_scripts), array_values($inline_scripts), $buffer);
}

$CI->output->set_output($buffer);
$CI->output->_display();
} 
/* End of file compress.php */
/* Location: ./system/application/hools/compress.php */