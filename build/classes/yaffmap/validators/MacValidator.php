<?PHP
require_once 'vendor/Propel/runtime/lib/validator/BasicValidator.php';

class MacValidator implements BasicValidator{
	
	public function isValid(ValidatorMap $map, $str){
		return Net_MAC::check($str);
	}
}
?>