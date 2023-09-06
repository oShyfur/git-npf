<?php

namespace Np\Structure\Classes\Oisf;

class Utility
{
    public function getNonce()
    {
        $factory = new RandomLib\Factory;
        $generator = $factory->getMediumStrengthGenerator();

        return $generator->generate(32);
    }
}
