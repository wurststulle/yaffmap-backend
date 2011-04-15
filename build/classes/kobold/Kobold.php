<?php
/**
 	Kobold

	Copyright (C) 2010-2011 Holger Gross

    Kobold is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Kobold is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Kobold. If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'KoboldAction.php';
require_once 'KoboldAttribute.php';
require_once 'KoboldListener.php';
require_once 'KoboldTemplate.php';
require_once 'KoboldCookie.php';
require_once 'KoboldSession.php';
require_once 'KoboldAuth.php';

class Kobold{
	
	public static function dump($var){
		echo '<pre>'.print_r($var, true).'</pre>';
	}
	
	public static function dump_ret($var){
		$ret = null;
		ob_start();
		var_dump($var);
		$ret = ob_get_clean();
		ob_end_clean();
		return $ret;
	}
}
?>