<?php
// Justin PHP Framework
// It's a portable framework for PHP 8.0+, powered by open source community.
// Licensed under the MIT License. (https://ncurl.xyz/s/2ltII6Ang)
// (c) 2022 Star Inc. (https://starinc.xyz)

namespace JustinExample\Kernel;

class Context
{
    private State $state;
    private Session $session;
    private Request $request;
    private Response $response;
    protected Config $config;
    protected Database $database;

    public function __construct()
    {
        $this->state = new State();
        $this->session = new Session();
        $this->request = new Request();
        $this->response = new Response();
        $this->config = new Config();
        $this->database = new Database($this->config);
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }
}
