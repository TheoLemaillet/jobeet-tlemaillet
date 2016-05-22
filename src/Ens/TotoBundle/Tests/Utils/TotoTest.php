<?php
// src/Ens/JobeetBundle/Tests/Utils/JobeetTest.php

namespace Ens\TotoBundle\Tests\Utils;
use Ens\TotoBundle\Utils\Toto;

class JobeetTest extends \PHPUnit_Framework_TestCase
{
    public function testSlugify()
    {
        $this->assertEquals('sensio', Toto::slugify('Sensio'));
        $this->assertEquals('sensio-labs', Toto::slugify('sensio labs'));
        $this->assertEquals('sensio-labs', Toto::slugify('sensio   labs'));
        $this->assertEquals('paris-france', Toto::slugify('paris,france'));
        $this->assertEquals('sensio', Toto::slugify('  sensio'));
        $this->assertEquals('sensio', Toto::slugify('sensio  '));
        $this->assertEquals('n-a', Toto::slugify(''));
        $this->assertEquals('n-a', Toto::slugify(' - '));

        if (function_exists('iconv'))
        {
            $this->assertEquals('dveloppeur-web', Toto::slugify('Développeur Web'));
        }
    }
}

