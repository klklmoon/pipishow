<?php
/**
 * 皮皮乐天通用方法
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: PipiCommon.php 14892 2013-09-09 11:51:51Z hexin $ 
 * @package  componets
 */
class PipiCommon extends CComponent{
	
	public static function truncate_utf8_string($string, $length, $etc = '...')
	{
		$result = '';
		$string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
		$strlen = strlen($string);
		for ($i = 0; (($i < $strlen) && ($length > 0)); $i++)
		{
			if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0'))
			{
				if ($length < 1.0)
				{
					break;
				}
				$result .= substr($string, $i, $number);
				$length -= 1.0;
				$i += $number - 1;
			}
			else
			{
				$result .= substr($string, $i, 1);
				$length -= 0.5;
			}
		}
		$result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
		if ($i < $strlen)
		{
			$result .= $etc;
		}
		return $result;
	}
}