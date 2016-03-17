<?php

namespace Phrity\Log\Test;

class InterpolationTraitTest extends \PHPUnit_Framework_TestCase
{

    public function testTrait()
    {
        $ips = ['i1' => 'p1', 'i2' => 'p2'];

        $mock = $this->getMockForTrait('\Phrity\Log\Util\InterpolationTrait');
        $reflection = new \ReflectionClass(get_class($mock));
        $method = $reflection->getMethod('interpolate');
        $method->setAccessible(true);

        $result = $method->invokeArgs($mock, ['Just a string']);
        $this->assertEquals($result, "Just a string");

        $result = $method->invokeArgs($mock, ['No interpolation', $ips]);
        $this->assertEquals($result, "No interpolation");

        $result = $method->invokeArgs($mock, ['Interpolate {i1}+{i2}', $ips]);
        $this->assertEquals($result, "Interpolate p1+p2");

        $result = $method->invokeArgs($mock, ['Interpolate {a}+{b}', ['a' => 1]]);
        $this->assertEquals($result, "Interpolate 1+{b}");
    }
}
