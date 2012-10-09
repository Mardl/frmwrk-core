<?php
/**
 * Languagemanager
 *
 * PHP version 5.3
 *
 * @category Manager
 * @package  Manager
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core\Application\Manager;

use jamwork\common\Registry,
	Core\SystemMessages,
	jamwork\database\MysqlRecordset as Recordset;

/**
 * Languagemanager
 *
 * @category Manager
 * @package  Manager
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Language extends \Core\Application\Manager\Base implements \Core\Application\Interfaces\ModelsInterface
{

	public function getDataRow()
	{
		$data = array(
			'id'		=> $this->getId(),
			'volltext'	=> $this->getVolltext(),
			'isocode' 	=> $this->getIsocode(),
			'country' 	=> $this->getCountry()
		);

		return $data;
	}

}

?>