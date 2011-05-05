<?php
class YaffmapWsdlCreator{
	
	protected $reflectedClasses = array();
	
	public function __construct($classmap){
		foreach($classmap as $key => $val){
			$this->reflectedClasses[] = new ReflectionClass($val);
		}
	}
	
	protected function parseComment($item){
		$comment = trim($item->getDocComment());
		if($comment == ''){
			return null;
		}else{
			$varType = null;
			$varDoc = new StdClass();
			$varDoc->type = null;
			$varDoc->typeOf = null;
			$lines = preg_split('(\\n\\r|\\r\\n\\|\\r|\\n)', $comment);
			foreach($lines as $line){
				$line = trim($line);
				$line = trim(substr($line, strpos($line, '* ')+2));
				if(isset($line[0]) && $line[0] == '@'){
					$parts = explode(' ', $line);
					if($parts[0] == '@return'){
						$methodDoc->return = $parts[1];
					}elseif($parts[0] == '@param'){
						$methodDoc->params[] = array('name' => substr($parts[2], 1), 'type' => $parts[1]);
					}elseif($parts[0] == '@var'){
						if(isset($parts[1]) && $parts[1] == 'array'){
							$varDoc->type = 'array';
							$varDoc->typeOf = $parts[2];
						}elseif(isset($parts[1]) && $parts[1] == 'object'){
							$varDoc->type = 'object';
							$varDoc->typeOf = $parts[2];
						}else{
							$varDoc->type = $parts[1];
						}
					}elseif($parts[0] == '@ignore' && $parts[1] == 'wsdl'){
						return null;
					}
				}
			}
		}
		if($item instanceof ReflectionProperty){
			return $varDoc;
		}elseif($item instanceof ReflectionMethod){
			return $methodDoc;
		}else{
			throw new Exception('unknown element given to parse');
		}
	}
	
	public function createWsdlSchema($url, $path = null){
		$out = '<xsd:schema elementFormDefault="qualified" xmlns="'.$url.'" xmlns:xsd="http://www.w3.org/2001/XMLSchema" targetNamespace="'.$url.$path.'">';
		foreach($this->reflectedClasses as $c){
			$out .= '<xsd:complexType name="'.substr($c->getName(), 1).'"><xsd:all>';
    		foreach($c->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED) as $prop){
				$doc = $this->parseComment($prop);
				if($doc == null){
					continue;
				}
    			if($doc->type == 'array'){
    				$out .= '<xsd:element name="'.$prop->getName().'" minOccurs="0" maxOccurs="unbounded" type="xsd:'.substr($doc->typeOf, 1).'" />';
    			}elseif($doc->type == 'object'){
    				$out .= '<xsd:element name="'.$prop->getName().'" type="xsd:'.substr($doc->typeOf, 1).'" />';
    			}else{	
    				$out .= '<xsd:element name="'.$prop->getName().'" type="xsd:'.$doc->type.'" />';
    			}
    		}
    		$out .= '</xsd:all></xsd:complexType>';
		}
		return $out.'</xsd:schema>';
	}
	
	public function createWsdl($url, $path = null){
		$definitions = '<definitions targetNamespace="'.$url.$path.'" xmlns:xts="'.$url.$path.'" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns="http://schemas.xmlsoap.org/wsdl/">';
		$types = '<types><xsd:schema targetNamespace="'.$url.$path.'"><xsd:include schemaLocation="'.$url.$path.'/soap.php?schema" /></xsd:schema></types>';
		$c = new ReflectionClass('YaffmapSoapServer');
		$methods = $c->getMethods();
		foreach($methods as $method){
			if($doc = $this->parseComment($method)){
				$messages .= '<message name="'.$method->getName().'Request">';
				if(is_array($doc->params)){
					foreach($doc->params as $param){
						$messages .= '<part name="'.$param['name'].'" type="'.(($param['type'] == 'string')?'xsd':'xts').':'.$param['type'].'" />';
					}
				}
  				$messages .= '</message>';
  				$messages .= '<message name="'.$method->getName().'Response">';
  				if(isset($doc->return)){
	  				$messages .= '<part name="returnValue" type="'.(($doc->return == 'string')?'xsd':'xts').':'.$doc->return.'"/>';
  				}
  				$messages .= '</message>';
		   	 	$operations .= '<operation name="'.$method->getName().'">';
		     	$operations .= '<input message="'.$method->getName().'Request" />';
		     	$operations .= '<output message="'.$method->getName().'Response" />';
		   		$operations .= '</operation>';
		   		$bindingOperation .= '<operation name="'.$method->getName().'">';
				$bindingOperation .= '<soap:operation soapAction="'.$url.$path.'#'.$method->getName().'"/>';
				$bindingOperation .= '<input><soap:body use="literal" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="'.$url.$path.'"/></input>';
				$bindingOperation .= '<output><soap:body use="literal" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="'.$url.$path.'"/></output>';
				$bindingOperation .= '</operation>';
		   		
			}
		}
		$port= '<portType name="ServicePortType">';
		$port .= $operations.'</portType>';
		$binding = '<binding name="ServiceBinding" type="ServicePortType"><soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>';
		$binding .= $bindingOperation.'</binding>';
		$service = '<service name="YaffmapDataService"><port binding="ServiceBinding" name="ServicePort1"><soap:address location="'.$url.$path.'/soap.php"/></port></service>';
		return $definitions.$types.$messages.$port.$binding.$service.'</definitions>';
	}
}
?>