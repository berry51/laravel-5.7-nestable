<?php

namespace Nestable\Tests\Services;

use RecursiveIteratorIterator;
use RecursiveArrayIterator;
use Nestable\Tests\TestCase;

class NestableServiceTest extends TestCase
{
    protected $menus;

    public function setUp()
    {
        parent::setUp();

        $this->menus = $this->dummyData();
    }

    public function testMake()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $this->assertContainsOnlyInstancesOf(\Nestable\Services\NestableService::class, array($nested));
    }

    public function testRenderAsArray()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus)->renderAsArray();

        $iteratorArray = new RecursiveArrayIterator($nested);
        $iterator = new RecursiveIteratorIterator($iteratorArray);

        $this->assertTrue($iterator->valid());
        $this->assertFalse($nested == $this->menus);

        $pid = $this->_get_random_pid($iteratorArray);

        if ($pid) {
            $result = $this->_helper_recursive($nested, $pid);

            $this->assertGreaterThan(0, $result);
        }
    }

    public function testRenderAsJson()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus)->renderAsJson();

        json_decode($nested);

        $this->assertLessThan(1, json_last_error());

        $this->assertTrue($nested != json_encode($this->menus));
    }

    public function testRenderAsDropdown()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus)->renderAsDropdown();

        $this->assertRegExp('/'.$this->_get_pattern('dropdown').'/', $nested);
    }

    public function testRenderAsMultiple()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus)->renderAsMultiple();

        $this->assertRegExp('/'.$this->_get_pattern('multiple').'/', $nested);
    }

    public function testRenderAsHtml()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus)->renderAsHtml();

        $this->assertRegExp('/'.$this->_get_pattern('html ').'/', $nested);
    }

    public function testAttr()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $dropdown = $nested->attr(['name' => 'menu', 'id' => 'menu'])->renderAsDropdown();

        $this->assertRegExp('/\<select\s+?name\=\"menu\"\s+id=\"menu\"\s+\>/', $dropdown);
    }

    public function testSelected()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $dropdown = $nested->selected(2)->renderAsDropdown();

        $this->assertRegExp('/\<option\s+?selected="selected"\s+value=\"2\"\>/', $dropdown);

        $nested = $nestable->make($this->menus);
        $dropdown = $nested->selected(2, 3, 4, 6)->renderAsMultiple();
        $this->assertRegExp('/\<option\s+?selected="selected"\s+value=\"2\"\>/', $dropdown);
        $this->assertRegExp('/\<option\s+?selected="selected"\s+value=\"3\"\>/', $dropdown);
        $this->assertRegExp('/\<option\s+?selected="selected"\s+value=\"4\"\>/', $dropdown);
        $this->assertRegExp('/\<option\s+?selected="selected"\s+value=\"6\"\>/', $dropdown);

        $nested = $nestable->make($this->menus);
        $dropdown = $nested->selected(function($option, $value, $label) {
            if ($label === 'Light Blue T-Shirts') $option->addAttr('selected', 'true');
        })->renderAsMultiple();

        $this->assertRegExp('/\<option\s+?selected="true".*\>/', $dropdown);        
    }

    public function testPlaceholder()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $dropdown = $nested->placeholder('', '-- Please Choose --')->renderAsDropdown();

        $this->assertRegExp('/\<option\s+?value=\"\"\>-- Please Choose --\<\/option\>/', $dropdown);
    }

    public function testParent()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus)->parent(1)->renderAsArray();

        $this->assertEquals(collect($nested)->where('pid', 1)->count(), 3);
    }

    public function testActive()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $html = $nested->active('sweaters')->renderAsHtml();
        $this->assertRegExp('/\<li\s+?class=\"active\"\><a\s+?href=\".*\/sweaters\"\>Sweaters\<\/a\>/', $html);

        $html = $nested->active('sweaters', 'black-sweaters')->renderAsHtml();
        $this->assertRegExp('/\<li\s+?class=\"active\"\><a\s+?href=\".*\/sweaters\"\>Sweaters\<\/a\>/', $html);

        $html = $nested->active(['sweaters'])->renderAsHtml();
        $this->assertRegExp('/\<li\s+?class=\"active\"\><a\s+?href=\".*\/sweaters\"\>Sweaters\<\/a\>/', $html);

        $html = $nested->active(function ($li, $href, $label) {
            $li->addAttr('data-label', 'label');
        })->renderAsHtml();
        $this->assertRegExp('/\<li\s+?data\-label=\"label\"\><a\s+?href=\".*\/sweaters\"\>Sweaters\<\/a\>/', $html);
    }

    public function testUlAttr()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);
        $html = $nested->ulAttr(['class' => 'nav-bar'])->renderAsHtml();
        $this->assertRegExp('/'.$this->_get_pattern('attribute_pattern_for_ul').'/', $html);
    }

    public function testFirstUlAttr() {
        $nestable = new \Nestable\Services\NestableService();
        
        $nested = $nestable->make($this->menus);
        $html = $nested->firstUlAttr('class', 'first-item')->renderAsHtml();
        $this->assertRegExp('/'.$this->_get_pattern('html-first-item').'/', $html);

        $nested = $nestable->make($this->menus);
        $html = $nested->firstUlAttr(['class' => 'first-item'])->renderAsHtml();
        $this->assertRegExp('/'.$this->_get_pattern('html-first-item').'/', $html);
    }

    public function testRoute()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $html = $nested->route(['menu' => 'slug'])->renderAsHtml();

        $this->assertRegExp('/http\:\/\/localhost\/menu\/.*/', $html);
    }

    public function testCustomUrl()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $html = $nested->customUrl('product/{slug}/detail')->renderAsHtml();

        $this->assertRegExp('/http\:\/\/localhost\/product\/.*\/detail/', $html);
    }

    public function testIsValidForArray()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $this->assertTrue($nested->isValidForArray());

        $nested = $nested->isValidForArray(true);

        $iteratorArray = new RecursiveArrayIterator($nested);
        $iterator = new RecursiveIteratorIterator($iteratorArray);

        $this->assertTrue($iterator->valid());
        $this->assertFalse($nested == $this->menus);

        $pid = $this->_get_random_pid($iteratorArray);

        if ($pid) {
            $result = $this->_helper_recursive($nested, $pid);

            $this->assertGreaterThan(0, $result);
        }
    }

    public function testIsValidForJson()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $this->assertTrue($nested->isValidForJson());

        $nested = $nested->isValidForJson(true);

        json_decode($nested);

        $this->assertLessThan(1, json_last_error());

        $this->assertTrue($nested != json_encode($this->menus));
    }

    public function testIsValidForHtml()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $this->assertTrue($nested->isValidForHtml());

        $this->assertRegExp('/'.$this->_get_pattern('html ').'/', $nested->isValidForHtml(true));
    }

    public function testIsValidForDropdown()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $this->assertTrue($nested->isValidForDropdown());

        $this->assertRegExp('/'.$this->_get_pattern('dropdown').'/', $nested->isValidForDropdown(true));
    }

    public function testIsValidForMultiple()
    {
        $nestable = new \Nestable\Services\NestableService();
        $nested = $nestable->make($this->menus);

        $this->assertTrue($nested->isValidForMultiple());

        $this->assertRegExp('/'.$this->_get_pattern('multiple').'/', $nested->isValidForMultiple(true));
    }
}
