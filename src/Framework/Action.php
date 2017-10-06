<?php

namespace Framework;

use Orm\EntityManager;
use Orm\FormBuilder;
use Orm\Schema;

class Action
{
    private $formBuilder;

    private $em;

    public function __construct(Schema $schema, EntityManager $entityManager)
    {
        $this->formBuilder = new FormBuilder($schema);
        $this->em = $entityManager;
    }

    public function getFormBuidler() {
        return $this->formBuilder;
    }
}