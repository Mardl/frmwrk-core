<?php
/**
 * Core\Cli-Class
 *
 * PHP version 5.3
 *
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core;

class Navigation
{
	protected $files = array();
	protected $links = array();
	
	public function __construct(){
		$this->_open(SITE_PATH);
		$this->_extract();
	}
	
	public function render()
	{
		$groups = \jamwork\common\Registry::getInstance()->conf->NAVGROUPS;
		
		
		
		if (!empty($groups))
		{
			foreach ($groups as $name => $arr){
				if (isset($this->links[$name]))
				{
					$groups[$name]['links'] = $this->links[$name];
					unset($this->links[$name]);
				}
			}
			
			if (count($this->links) > 0)
			{
				foreach ($this->links as $group => $actions)
				{
					$groups[$group]['class'] = '';
					$groups[$group]['links'] = $actions;
				}
			}
		} 
		else 
		{
			$groups = $this->links;
		}
				
		echo "<div class='tabmenu'>";
		echo "<ul>";
		
		foreach ($groups as $group => $actions)
		{
			echo "<li><a href='#' class='".$actions['class']."'><span>".$group."</span></a>";
				
			echo "<ul class='subnav'>";

			ksort($actions['links']);
			
			foreach ($actions['links'] as $action)
			{
				echo "<li><a href='".$action['url']."'><span>".$action['title'].'</span></a>';
			}
					
			echo "</ul>";
			
			
		}
		
		echo "</ul>";
		echo '</div>';
	}
	
	private function _open($dir)
	{
		$temp = explode('/', $dir);
		if ( array_pop($temp) == 'Views'){
			return;
		}
		
		$directory = opendir($dir);
		
		while ( ($file = readdir($directory)) == true )
		{
			if ($file != '.' && $file != '..')
			{
				if (is_dir($dir.'/'.$file))
				{
					$this->_open($dir.'/'.$file);
				}
				else
				{
					$this->files[] = $dir.'/'.$file;
				}
				
			}
		}
	}
	
	private function _extract()
	{
		$view = new \Core\View();
		
		foreach ($this->files as $controller)
		{
			preg_match("/.*\/Modules\/([A-Z]{1}[a-zA-Z]+)\/Controller\/([A-Z]{1}[a-zA-Z]+)\.php/",$controller, $matches);
			
			if (!empty($matches) && count($matches) == 3)
			{
				$module = $matches[1];
				$controller = $matches[2];
				
				$reflect = new \ReflectionClass("\\App\\Modules\\".$module."\\Controller\\".$controller);
				$methods = $reflect->getMethods();
				
				
				foreach ($methods as $method)
				{
					preg_match("/(.+)(HTML|Html)Action/", $method->getName(), $matches);
					if (!empty($matches)){
						$docComment = $method->getDocComment();
						
						if ($docComment !== false)
						{
							//Show in Navigation
							preg_match('/.*\@showInNavigation ([a-z]+).*/', $docComment, $matchDoc);
							
							if (!empty($matchDoc) && $matchDoc[1] == 'true'){
								preg_match('/.*\@navigationName ([A-Za-z0-9äöüÄÖÜ]+).*/s', $docComment, $matchDoc);
								$navigationName = $matchDoc[1];
								
								preg_match('/.*\@navigationSort ([0-9]+).*/s', $docComment, $matchDoc);
								$navigationSort = $matchDoc[1];
								
								preg_match('/.*\@navigationGroup ([A-Za-z0-9]+).*/s', $docComment, $matchDoc);
								$navigationGroup = $matchDoc[1];
								
								$conf = array(
									'module' => strtolower($module),
									'controller' => strtolower($controller),
									'action' => strtolower($matches[1])
								);
									
								$conf['url'] = $view->url($conf,'default');
								$conf['title'] = $navigationName;
								
								$this->links[$navigationGroup][$navigationSort.'-'.$navigationName] = $conf;
							}
						}
					}
					
				}
				
			}
			
			
			
		}
		
		
	}
	
	
}
?>