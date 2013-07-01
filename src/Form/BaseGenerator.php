<?php

namespace Core\Form;

use Core\Form, Core\SystemMessages;

/**
 * Class BaseGenerator
 *
 * @category Core
 * @package  Core\Form
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
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
	 * @return mixed
	 */
	public function __toString()
	{
		return $this->form->__toString();
	}

	/**
	 * Standardvalidierung nach Pflichtfeldern
	 *
	 * @param \Core\Form|\Core\Form\Element $elementContainer
	 *
	 * @return bool
	 */
	protected function checkRequired($elementContainer)
	{
		$checkup = true;

		foreach ($elementContainer->getElements() as $el)
		{
			$check = true;
			if ($el instanceof \Core\Html\Input)
			{
				$check = $el->validate();
			}

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
