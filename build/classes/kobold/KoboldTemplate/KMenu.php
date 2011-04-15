<?php

class KMenu extends KoboldTemplate{
	
	protected $div = null;
	
	public function __construct(){
		$this->div = new KDiv();
		$this->div->addAttribute(array(new KAttributeId('menu')));
		
	}
	
	public function addMenuTitle($title){
		$ul = new KListUnordered();
		$h2 = new KGenericTag('h2');
		$h2->addItem($title);
		$ul->addItem($h2);
		$this->div->addItem($ul);
	}
	
	public function addMenuItem($href, $text){
		$ul = new KListUnordered();
		$a = new KSimpleLink($href, ':: '.$text);
		$ul->addItem($a);
		$this->div->addItem($ul);
	}
	
	public static function includeCss(){
		return '<STYLE type="text/css">
			/* css for KMenu */
			#menu { width: 100%; background: #eee; } 
			#menu ul { list-style: none; margin: 0; padding: 0; }
			#menu a, #menu h2 { font: bold 11px/16px arial, helvetica, sans-serif;
			display: block; border-width: 1px; border-style: solid;
			border-color: #FFF;
			//border-color: #ccc #888 #555 #bbb;
			margin: 0; padding: 2px 3px; }
			#menu h2 { color: #000; background-color: #D3DCE3;
			// background: #000;
			text-transform: uppercase; }
			#menu a { color: #000; background: #efefef; text-decoration: none; }
			#menu a:hover { color: #a00; background: #fff; }
		</STYLE>';
	}
	
	public function get(){
		return $this->div->get();
	}
}
?>