<?php
/**
 * Lifemeter\Form\Base-Class
 *
 * PHP version 5.3
 *
 * @category Forms
 * @package  Lifemeter
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core\Form;

use Core\Form,
	Core\SystemMessages;

class BaseGenerator
{
	protected $form;

	/**
	 * Konstruktor
	 *
	 * @param string $action Ziel des Formulars
	 * @param array  $data   Formulardaten
	 */
	public function __construct($action, $data = array())
	{
		$this->form = new Form($data);
		$this->form->setAction($action);
	}

	/**
	 * Liefert das Formularobjekt
	 *
	 * @return \Core\Form
	 */
	public function getForm()
	{
		return $this->form;
	}

	/**
	 * Liefert das Formularobjekt zur Ausgabe
	 *
	 * @return \Core\Form
	 */
	public function asString()
	{
		return $this->form;
	}

	/**
	 * Standardvalidierung nach Pflichtfeldern
	 *
	 * @param Core\Form|\Core\Form\Element $elementContainer
	 *
	 * @return boolean
	 */
	protected function checkRequired($elementContainer)
	{
		$checkup = true;

		foreach ($elementContainer->getElements() as $el)
		{
			$check = $el->validate();

			if ($check !== true)
			{
				SystemMessages::addError($check);
				$el->addCssClass('error');
				$checkup = false;
			}

			if ($el->hasElements())
			{
				$check = $this->checkRequired($el);
				$checkup = $checkup && $check;
			}
		}

		return $checkup;
	}
}
