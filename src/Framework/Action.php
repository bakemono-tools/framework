<?php

namespace Framework;

use Orm\EntityManager;
use Orm\FormBuilder;
use Orm\Schema;
use Psr\Http\Message\ServerRequestInterface;

class Action
{
    protected $formBuilder;

    protected $em;

    protected $request;

    public function __construct(Schema $schema, EntityManager $entityManager, ServerRequestInterface $request)
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
     * @return ServerRequestInterface
     */
    public function getRequest() : ServerRequestInterface
    {
        return $this->request;
    }
}