<?PHP
require_once 'vendor/Propel/runtime/lib/validator/BasicValidator.php';

class Ipv6Validator implements BasicValidator{
	
	public function isValid(ValidatorMap $map, $str){
		return Net_IPv6::checkIPv6($str);
	}
}
?>