<?php
namespace Core\Form\Input;

use Core\Form\Input;

class Email extends Input{

	public function validate()
	{
		$val = $this->getValue();
			
		if ($this->isRequired() && empty($val))
		{
			if ($this->label)
			{
				return "Fehlende Eingabe für ".$this->label->getValue();
			}
			else
			{
				return "Fehlende Eingabe für ".$this->getId();
			}
		}
		else if (!empty($val))
		{
			if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
				return "Die Emailadresse wird als ungültig angesehen";
			}
		}
		
		return true;
	}
	
}