<?php



/**
 * Skeleton subclass for representing a row from the 'ffmap_metricType' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ffmap
 */
class MetricType extends BaseMetricType {

	public function preInsert(PropelPDO $con = null){
    	$this->setId(md5(mt_rand(1, 1000).date('U')));
    	return true;
	}
} // MetricType
