<?php
/**
 	KoboldTemplate

	Copyright (C) 2010-2011 Holger Gross

    KoboldTemplate is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    KoboldTemplate is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with KoboldTemplate. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * TODO:
 * - methode für breitenangebe der tds einer tabelle $table->addWidth('50%', '25%', '25%');
 * 		(breiten in array schreiben und bei jedem td mit pop() rausnehmen bis keins mehr 
 * 		 da ist)
 *
 */

require_once 'KoboldAttribute.php';

require_once 'KoboldTemplate/IKoboldTemplate.php';

require_once 'KoboldTemplate/KGeneric.php';
require_once 'KoboldTemplate/KDropDown.php';
require_once 'KoboldTemplate/KForm.php';
require_once 'KoboldTemplate/KInput.php';
require_once 'KoboldTemplate/KLayer.php';
require_once 'KoboldTemplate/KLink.php';
require_once 'KoboldTemplate/KList.php';
require_once 'KoboldTemplate/KMenu.php';
require_once 'KoboldTemplate/KTable.php';

/**
 * provides complex structures such as tables from sql querys
 * @todo implement
 */
class KTemplate{
	
	protected $layer = null;
	
	public function __construct(){
		$this->layer = new KLayer();
	}
	
	public function addItem($item){
		if(is_array($item)){
			// items given as array
			// like: addItem(array('a', '2'));
			foreach($item as $val){
				$this->layer->addItem($val);
			}
		}else{
			// only one item is given
			$this->layer->addItem($item);
		}
	}
	
	public function addTable($data){
		$table = new KTable();
		foreach($data as $item){
			$tr = new KTr();
			foreach($item as $key => $value){
				$td = new KTd();
				$td->addItem($value);
				$tr->addItem($td);
			}
			$table->addItem($tr);
		}
		$this->layer->addItem($table);
	}
	
	public function __toString(){
		return $this->layer->get();
	}
	
	/**
	 * build yes/no dropdown menu, return 1 if yes, 0 if no
	 * @param $tag
	 * @param $selectedID
	 * @param KoboldTranslator $translator (optional)
	 * @return KSelect
	 */
	public static function trueFalseDropDown($tag, $selectedID, $translator = null){
		$dd = new KSelect();
		$dd->addAttribute(array('id' => $tag, 'name' => $tag));
		$dd->addOption(1, (($translator == null) ? 'yes' : $translator->_('yes')), (($selectedID == '1') ? true : false));
		$dd->addOption(0, (($translator == null) ? 'no' : $translator->_('no')), (($selectedID == '0') ? true : false));
		return $dd;
	}
}
?>