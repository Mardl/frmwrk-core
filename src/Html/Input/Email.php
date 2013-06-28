<?php

namespace Core\Html\Input;

use Core\Html\Input;

/**
 * Class Email
 *
 * @category Core
 * @package  Core\Html\Input
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Email extends Input
{

	/**
	 * E-Mail Validierung
	 *
	 * @return bool|string
	 */
	public function validate()
	{
		$val = $this->getValue();

		if ($this->isRequired() && empty($val))
		{
			if ($this->label)
			{
				return "Fehlende Eingabe für " . $this->label->getValue();
			}
			else
			{
				return "Fehlende Eingabe für " . $this->getId();
			}
		}
		else
		{
			if (!empty($val))
			{
				if (!filter_var($val, FILTER_VALIDATE_EMAIL))
				{
					return "Die E-Mail-Adresse wird als ungültig angesehen!";
				}
			}
		}

		return true;
	}
}
