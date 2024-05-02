<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

class Datalist8Type extends AbstractType
{
    public function getParent()
    {
        return EntityType::class;
    }
}