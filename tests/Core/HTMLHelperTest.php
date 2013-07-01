<?php
/**
 * Unittest für Core\HTMLHelper
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Core;

use Core\HTMLHelper;

/**
 * HTMLHelper test case.
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class HTMLHelperTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet eine Instanz von Core\HTMLHelper
	 * 
	 * @var HTMLHelper
	 */
	private $HTMLHelper;
	
	/**
	 * Prepares the environment before running a test.
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->HTMLHelper = new HTMLHelper(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
		// TODO Auto-generated HTMLHelperTest::tearDown()
		
		$this->HTMLHelper = null;
		
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests HTMLHelper->app()
	 * 
	 * @return void
	 */
	public function testApp()
	{
		// TODO Auto-generated HTMLHelperTest->testApp()
		$this->markTestIncomplete("app test not implemented");
		
		$this->HTMLHelper->app(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->getSystemMessages()
	 * 
	 * @return void
	 */
	public function testGetSystemMessages()
	{
		// TODO Auto-generated HTMLHelperTest->testGetSystemMessages()
		$this->markTestSkipped("getSystemMessages test will not implemented");
		
		$this->HTMLHelper->getSystemMessages(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->setCSSFile()
	 * 
	 * @return void
	 */
	public function testSetCSSFile()
	{
		// TODO Auto-generated HTMLHelperTest->testSetCSSFile()
		$this->markTestIncomplete("setCSSFile test not implemented");
		
		$this->HTMLHelper->setCSSFile(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->addCssAsset()
	 * 
	 * @return void
	 */
	public function testAddCssAsset()
	{
		// TODO Auto-generated HTMLHelperTest->testAddCssAsset()
		$this->markTestIncomplete("addCssAsset test not implemented");
		
		$this->HTMLHelper->addCssAsset(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->addCSSFile()
	 * 
	 * @return void
	 */
	public function testAddCSSFile()
	{
		// TODO Auto-generated HTMLHelperTest->testAddCSSFile()
		$this->markTestIncomplete("addCSSFile test not implemented");
		
		$this->HTMLHelper->addCSSFile(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->getCSSFiles()
	 * 
	 * @return void
	 */
	public function testGetCSSFiles()
	{
		// TODO Auto-generated HTMLHelperTest->testGetCSSFiles()
		$this->markTestIncomplete("getCSSFiles test not implemented");
		
		$this->HTMLHelper->getCSSFiles(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->renderCSSFiles()
	 * 
	 * @return void
	 */
	public function testRenderCSSFiles()
	{
		// TODO Auto-generated HTMLHelperTest->testRenderCSSFiles()
		$this->markTestIncomplete("renderCSSFiles test not implemented");
		
		$this->HTMLHelper->renderCSSFiles(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->addJsAsset()
	 * 
	 * @return void
	 */
	public function testAddJsAsset()
	{
		// TODO Auto-generated HTMLHelperTest->testAddJsAsset()
		$this->markTestIncomplete("addJsAsset test not implemented");
		
		$this->HTMLHelper->addJsAsset(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->setJsAsset()
	 * 
	 * @return void
	 */
	public function testSetJsAsset()
	{
		// TODO Auto-generated HTMLHelperTest->testSetJsAsset()
		$this->markTestIncomplete("setJsAsset test not implemented");
		
		$this->HTMLHelper->setJsAsset(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->addJsFile()
	 * 
	 * @return void
	 */
	public function testAddJsFile()
	{
		// TODO Auto-generated HTMLHelperTest->testAddJsFile()
		$this->markTestIncomplete("addJsFile test not implemented");
		
		$this->HTMLHelper->addJsFile(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->getJsAssets()
	 * 
	 * @return void
	 */
	public function testGetJsAssets()
	{
		// TODO Auto-generated HTMLHelperTest->testGetJsAssets()
		$this->markTestIncomplete("getJsAssets test not implemented");
		
		$this->HTMLHelper->getJsAssets(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->getJsAssetFiles()
	 * 
	 * @return void
	 */
	public function testGetJsAssetFiles()
	{
		// TODO Auto-generated HTMLHelperTest->testGetJsAssetFiles()
		$this->markTestIncomplete("getJsAssetFiles test not implemented");
		
		$this->HTMLHelper->getJsAssetFiles(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->getJsVariables()
	 * 
	 * @return void
	 */
	public function testGetJsVariables()
	{
		// TODO Auto-generated HTMLHelperTest->testGetJsVariables()
		$this->markTestIncomplete("getJsVariables test not implemented");
		
		$this->HTMLHelper->getJsVariables(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->setJsVariable()
	 * 
	 * @return void
	 */
	public function testSetJsVariable()
	{
		// TODO Auto-generated HTMLHelperTest->testSetJsVariable()
		$this->markTestIncomplete("setJsVariable test not implemented");
		
		$this->HTMLHelper->setJsVariable(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->addJsVariable()
	 * 
	 * @return void
	 */
	public function testAddJsVariable()
	{
		// TODO Auto-generated HTMLHelperTest->testAddJsVariable()
		$this->markTestIncomplete("addJsVariable test not implemented");
		
		$this->HTMLHelper->addJsVariable(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->addBreadcrumb()
	 * 
	 * @return void
	 */
	public function testAddBreadcrumb()
	{
		// TODO Auto-generated HTMLHelperTest->testAddBreadcrumb()
		$this->markTestIncomplete("addBreadcrumb test not implemented");
		
		$this->HTMLHelper->addBreadcrumb(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->getBreadcrumbs()
	 * 
	 * @return void
	 */
	public function testGetBreadcrumbs()
	{
		// TODO Auto-generated HTMLHelperTest->testGetBreadcrumbs()
		$this->markTestIncomplete("getBreadcrumbs test not implemented");
		
		$this->HTMLHelper->getBreadcrumbs(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->setBreadcrumbHome()
	 * 
	 * @return void
	 */
	public function testSetBreadcrumbHome()
	{
		// TODO Auto-generated HTMLHelperTest->testSetBreadcrumbHome()
		$this->markTestIncomplete("setBreadcrumbHome test not implemented");
		
		$this->HTMLHelper->setBreadcrumbHome(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->getBreadcrumbHome()
	 * 
	 * @return void
	 */
	public function testGetBreadcrumbHome()
	{
		// TODO Auto-generated HTMLHelperTest->testGetBreadcrumbHome()
		$this->markTestIncomplete("getBreadcrumbHome test not implemented");
		
		$this->HTMLHelper->getBreadcrumbHome(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->viewBreadcrumbs()
	 * 
	 * @return void
	 */
	public function testViewBreadcrumbs()
	{
		// TODO Auto-generated HTMLHelperTest->testViewBreadcrumbs()
		$this->markTestIncomplete("viewBreadcrumbs test not implemented");
		
		$this->HTMLHelper->viewBreadcrumbs(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->truncate()
	 * 
	 * @return void
	 */
	public function testTruncate()
	{
		// TODO Auto-generated HTMLHelperTest->testTruncate()
		$this->markTestIncomplete("truncate test not implemented");
		
		$this->HTMLHelper->truncate(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->addLink()
	 * 
	 * @return void
	 */
	public function testAddLink()
	{
		// TODO Auto-generated HTMLHelperTest->testAddLink()
		$this->markTestIncomplete("addLink test not implemented");
		
		$this->HTMLHelper->addLink(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->getRenderedLinks()
	 * 
	 * @return void
	 */
	public function testGetRenderedLinks()
	{
		// TODO Auto-generated HTMLHelperTest->testGetRenderedLinks()
		$this->markTestIncomplete("getRenderedLinks test not implemented");
		
		$this->HTMLHelper->getRenderedLinks(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->addMeta()
	 * 
	 * @return void
	 */
	public function testAddMeta()
	{
		// TODO Auto-generated HTMLHelperTest->testAddMeta()
		$this->markTestIncomplete("addMeta test not implemented");
		
		$this->HTMLHelper->addMeta(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->getMetas()
	 * 
	 * @return void
	 */
	public function testGetMetas()
	{
		// TODO Auto-generated HTMLHelperTest->testGetMetas()
		$this->markTestIncomplete("getMetas test not implemented");
		
		$this->HTMLHelper->getMetas(/* parameters */);
	
	}
	
	/**
	 * Tests HTMLHelper->renderMetas()
	 * 
	 * @return void
	 */
	public function testRenderMetas()
	{
		// TODO Auto-generated HTMLHelperTest->testRenderMetas()
		$this->markTestIncomplete("renderMetas test not implemented");
		
		$this->HTMLHelper->renderMetas(/* parameters */);
	
	}

}

