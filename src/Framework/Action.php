<?php

namespace Framework;

use GuzzleHttp\Psr7\Request;
use Orm\EntityManager;
use Orm\FormBuilder;
use Orm\Schema;

class Action
{
    protected $formBuilder;

    protected $em;

    protected $request;

    public function __construct(Schema $schema, EntityManager $entityManager, Request $request)
    {
        $this->formBuilder = new FormBuilder($schema);
        $this->em = $entityManager;
        $this->request = $request;
    }

    /**
     * @return FormBuilder
     */
    public function getFormBuilder() : FormBuilder
    {
        return $this->formBuilder;
    }

    /**
     * @return Request
     */
    public function getRequest() : Request
    {
        return $this->request;
    }
}