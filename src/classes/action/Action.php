<?php

namespace iutnc\deefy\action;

abstract class Action
{
    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    public function __construct() {
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    public function execute(): string {
        switch ($this->http_method) {
            case 'GET' : return $this->get();
            case 'POST' : return $this->post();
            default : return '';
        }

    }

    public function __invoke() : string {
        return $this->execute();
    }

    abstract public function get() : string;

    abstract public function post() : string;
}