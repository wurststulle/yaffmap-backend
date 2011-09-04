<?PHP
require_once 'vendor/Propel/runtime/lib/validator/BasicValidator.php';

class Ipv4Validator implements BasicValidator{
	
	public function isValid(ValidatorMap $map, $str){
		return Net_IPv4::validateIP($str);
	}
}
?>