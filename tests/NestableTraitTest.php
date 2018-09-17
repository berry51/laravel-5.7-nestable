<?php

namespace Nestable\Tests;

use RecursiveIteratorIterator;
use RecursiveArrayIterator;

class NestableTraitTest extends DBTestCase
{
    protected $categories;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/factories');

        factory(Model\Menu::class, 50)->create();

        $this->categories = Model\Menu::all()->toArray();
    }

    public function testNested()
    {
        $nested = Model\Menu::nested()->get();

        $iteratorArray = new RecursiveArrayIterator($nested);
        $iterator = new RecursiveIteratorIterator($iteratorArray);

        $this->assertTrue($iterator->valid());
        $this->assertFalse($nested == $this->categories);

        $pid = $this->_get_random_pid($iteratorArray);

        if ($pid) {
            $result = $this->_helper_recursive($nested, $pid);

            $this->assertGreaterThan(0, $result);
        }
    }

    public function testRenderAsArray()
    {
        $this->testNested();
    }

    public function testRenderAsJson()
    {
        $nested = Model\Menu::renderAsJson();

        json_decode($nested);

        $this->assertLessThan(1, json_last_error());

        $this->assertTrue($nested != json_encode($this->categories));
    }

    public function testRenderAsDropdown()
    {
        $dropdown = Model\Menu::renderAsDropdown();
        $this->assertRegExp('/'.$this->_get_pattern('dropdown').'/', $dropdown);

        // test where
        $dropdown = Model\Menu::whereRaw("name like '%i%' OR name like '%a%'")->renderAsDropdown();
        $this->assertRegExp('/'.$this->_get_pattern('dropdown_single_option').'/', $dropdown);
    }

    public function testRenderAsMultiple()
    {
        $dropdown = Model\Menu::renderAsMultiple();
        $this->assertRegExp('/'.$this->_get_pattern('multiple').'/', $dropdown);

        // test where
        $dropdown = Model\Menu::whereRaw("name like '%i%' OR name like '%a%'")->renderAsMultiple();
        $this->assertRegExp('/'.$this->_get_pattern('dropdown_single_option').'/', $dropdown);
    }

    public function testRenderAsHtml()
    {
        $html = Model\Menu::renderAsHtml();
        $this->assertRegExp('/'.$this->_get_pattern('html').'/', $html);
    }
}
