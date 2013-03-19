<?php
/**
 * Interface ModelsInterface
 *
 * PHP version 5.3
 *
 * @category Interface
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core\Application\Interfaces;

/**
 *
 */
interface ModelsInterface
{
	public function getTableName();
	public function getTablePrefix();
	public function getIdField();
	public function getDataRow();
	public function setDataRow($data = array());
	public function getId();
	public function setId($id);
	public function setCreated($datetime = 'now');
	public function setModified($datetime = 'now');
	public function setCreateduser_Id($userId = 0);
	public function setModifieduser_Id($userId = NULL);
}
