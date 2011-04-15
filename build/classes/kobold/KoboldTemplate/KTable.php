<?php
/**
 * abstract class for tables
 */
abstract class KoboldTemplateTable extends KoboldTemplate{}

/**
 * abstract class for table rows (<tr>)
 */
abstract class KoboldTemplateTableTr extends KoboldTemplate{}

/**
 * abstract class for table cols (<td>)
 */
abstract class KoboldTemplateTableTd extends KoboldTemplate{}

class KTable extends KoboldTemplateTable{
	
	/**
	 * number of rows in table
	 * @var integer
	 */
	private $_numTr = 0;
	
	/**
	 * attributes of cols in a row
	 * @var array
	 */
	private $_rowAttributes = array();
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
//		$this->addAttribute(array('border' => '1', 'width' => '100%'), false);
	}
	
	/**
	 * add item to table, item must be <tr>
	 * @param array KoboldTemplateTableTr
	 */
	public function addItem($item){
		if(!($item instanceof KoboldTemplateTableTr)){
			die(get_class($this).' got incompatible item');
		}else{
			if(is_array($item)){
				// items given as array
				foreach($item as $val){
					$this->_numTr++;
					if($this->_numTr % 2 == 0){
						$val->addAttribute(array('class' => 'even'), false);
					}else{
						$val->addAttribute(array('class' => 'odd'), false);
					}
					$this->item[] = $val;
				}
			}else{
				// only one item is given
				$this->_numTr++;
				if($this->_numTr % 2 == 0){
					$item->addAttribute(array('class' => 'even'), false);
				}else{
					$item->addAttribute(array('class' => 'odd'), false);
				}
				$this->item[] = $item;
			}
		}
	}
	
	/**
	 * adds a row to table
	 * 
	 * @example addRow(array(new KTd(), new KTd()))
	 * @return KTr
	 */
	public function addRow(array $items){
		$tr = new KTr();
		$tr->addRowAttributes($this->_rowAttributes);
		foreach($items as $item){
			if($item instanceof KoboldTemplateTableTd){
				// if item is <td>
				$tr->addItem($item);
			}else{
				// if item is NOT <td>
				$td = new KTd();
				if($item == null){
					$td->addItem(new KBlanc());
				}else{
					$td->addItem($item);
				}
				$tr->addItem($td);
			}
		}
		$this->addItem($tr);
		return $tr;
	}
	
	/**
	 * adds a table head row to table
	 * 
	 * @example addThRow(array(new KTd(), new KTd()))
	 * @return KTr
	 */
	public function addThRow(array $items){
		$tr = new KTr();
		$tr->addAttribute(array('class' => 'tableHead'));
		foreach($items as $item){
			if($item instanceof KoboldTemplateTableTd){
				// if item is <td> 
				$tr->addItem($item);
			}else{
				// if item is NOT <td>
				if(!$item){
					// if false given, do nothing
					continue;
				}
				$td = new KTd();
				$td->addItem($item);
				$tr->addItem($td);
			}
		}
		$this->addItem($tr);
		return $tr;
	}
	
	/**
	 * adds array of attributes to all tr's. use it with KTable::addRow();
	 * 
	 * @example $table->addRowAttributes(array('width' => array('30', '*', '100', '100'), 'align' => array('center', 'left', 'center', 'center')));
	 * @param array $attributes
	 */
	public function addRowAttributes(array $attributes){
		foreach($attributes as $attr => $values){
			$this->_rowAttributes[$attr] = array_reverse($values);
		} 
	}
	
	/**
	 * @todo implement
	 * sorts table by given attributes
	 * @example $tbl->sortBy(array('datum', 'nummer'));
	 * @param unknown_type $sortBy
	 */
	public function sortBy(array $sortBy = null){
		// $data = array_reverse(multisort($data, array('datum')));
		echo '<pre>';
		print_r ($this->item);
		echo '</pre>';
	}
	
	/**
	 * builds the table
	 * @return string html-table
	 */
	public function get(){
		return $this->_get('table');
	}
}

class KTr extends KoboldTemplateTableTr{
	
	private $_rowAttributes = null;
	
	/**
	 * adds item to tr, item must be instance of KoboldTemplateTableTd
	 * @param KoboldTemplateTableTd $item
	 */
	public function addItem($item){
		if(is_array($item)){
			// items given as array
			foreach($item as $val){
				if($value instanceof KoboldTemplateTableTd){
					if(!empty($this->_rowAttributes)){
						// attribute collection for row available
						foreach($this->_rowAttributes as $key => $attr){
							$val->addAttribute(array($key => array_pop($this->_rowAttributes[$key])));
						}
					}
					$this->item[] = $val;
				}else{
					die(get_class($this).' got incompatible item');
				}
			}
		}else{
			// only one item is given
			if($item instanceof KoboldTemplateTableTd){
				if(!empty($this->_rowAttributes)){
					// attribute collection for row available
					foreach($this->_rowAttributes as $key => $attr){
						$item->addAttribute(array($key => array_pop($this->_rowAttributes[$key])));
					}
				}
				$this->item[] = $item;
			}else{
				die(get_class($this).' got incompatible item');
			}
		}
	}
	
	/**
	 * build tr
	 * 
	 * @return string html-tr
	 */
	public function get(){
		$out = '<tr';
		foreach($this->attribute as $key => $value){
			$out .= ' '.$key.'="'.$value.'"';
		}
		$out .= '>';
		foreach($this->item as $value){
			if($value instanceof KoboldTemplateTableTd){
				// is part of a table (td)
				$out .= $value->get();
			}else{
				// is an other element (e.g. button)
				$td = new KTd();
				$td->addItem($value);
				$out .= $td->get();
			}
		}
		$out .= '</tr>';
		return $out;
	}
	
	/**
	 * wrapper for addTds()
	 * 
	 * @param unknown_type $item
	 * @return KoboldTemplateTableTd created or given KoboldTemplateTableTd (td) element
	 */
	public function addTd($item){
		if(is_array($item)){
			return $this->addTds($item);
		}else{
			return $this->addTds(array($item));
		}
	}
	
	/**
	 * adds item to tr, if item is not instance of KoboldTemplateTableTd, td will be surrounded.
	 * if false is given, nothing will be done
	 * 
	 * @param array of items or tds
	 * @return KoboldTemplateTableTd created or given KoboldTemplateTableTd (td) element
	 */
	public function addTds(){
		$num = func_num_args();
		$args = func_get_args();
		if(count($args) > 0 && !$args){
			return;
		}
		foreach($args as $value){
			if($value instanceof KoboldTemplateTableTd){
				$this->addItem($value);
				return $value;
			}else{
				$td = new KTd();
				$td->addItem($value);
				$this->addItem($td);
				return $td;
			}
		}
	}
	
	/**
	 * adds array of attributes to this tr. DON'T CALL FROM OUTSIDE!
	 * 
	 * @param array $attr
	 */
	public function addRowAttributes(array $attr){
		$this->_rowAttributes = $attr;
	}
	
	/**
	 * wrapper for addThs()
	 * 
	 * @deprecated
	 * @param unknown_type $item
	 */
	public function addTh($item){
		$this->addThs($item);
	}
	
	/**
	 * adds item to tr, if item is not instance of KoboldTemplateTableTd, td will be surrounded
	 * 
	 * @deprecated
	 * @param array of items or ths
	 */
	public function addThs(){
		$num = func_num_args();
		$args = func_get_args();
		call_user_func_array(array($this, 'addTds'), $args);
	}
}

class KTd extends KoboldTemplateTableTd{
	
	public function __construct(){
		$args = func_get_args();
		if(count($args) == 1){
			// add items only
			$this->addItem($args[0]);
		}else{
			call_user_func_array('parent::__construct', $args);
		}
	}
	
	/**
	 * adds item to td
	 * 
	 * @see KoboldTemplate::addItem()
	 */
	public function addItem($item){
		if(is_array($item)){
			// items given as array
			foreach($item as $val){
				if($val === ''){
					$this->item[] = '&nbsp;';
				}else{
					$this->item[] = $val;
				}
			}
		}else{
			// only one item is given
			if($item === ''){
				$this->item[] = '&nbsp;';
			}else{
				$this->item[] = $item;
			}
		}
	}
	
	public function get(){
		$out = '<td ';
		foreach($this->attribute as $key => $value){
			$out .= ' '.$key.'="'.$value.'"';
		}
		$out .= '>';
		foreach($this->item as $value){
			if($value instanceof KoboldTemplate){
				$out .= $value->get();
			}else{
				$out .= $value;
			}
		}
		$out .= '</td>';
		return $out;
	}
}

/**
 * @deprecated
 */
class KTh extends KTd{}

?>