<?php

namespace Ainab\Really\Model;

class Request
{
    private $params;
    private $query;
    private $body;
    private $files;
    private $headers;
    private $cookies;
    private $method;
    private $url;
    private $path;
    private $protocol;
    private $hostname;

    public function __construct()
    {
        $this->params = $_REQUEST;
        $this->query = $_GET;
        $this->body = $_POST;
        $this->files = $_FILES;
        $this->headers = getallheaders();
        $this->cookies = $_COOKIE;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->url = $_SERVER['REQUEST_URI'];
        $this->path = parse_url($this->url, PHP_URL_PATH);
        $this->protocol = $_SERVER['SERVER_PROTOCOL'];
        $this->hostname = $_SERVER['HTTP_HOST'];
    }

    public function param($key)
    {
        return $this->params[$key] ?? null;
    }

    public function query($key)
    {
        return $this->query[$key] ?? null;
    }

    public function body($key)
    {
        return $this->body[$key] ?? null;
    }

    public function file($key)
    {
        return $this->files[$key] ?? null;
    }

    public function header($key)
    {
        return $this->headers[$key] ?? null;
    }

    public function cookie($key)
    {
        return $this->cookies[$key] ?? null;
    }

    public function method()
    {
        return $this->method;
    }

    public function url()
    {
        return $this->url;
    }

    public function path()
    {
        return $this->path;
    }

    public function protocol()
    {
        return $this->protocol;
    }

    public function hostname()
    {
        return $this->hostname;
    }

    public function all()
    {
        return $this->params;
    }

    public function allQuery()
    {
        return $this->query;
    }

    public function allBody()
    {
        return $this->body;
    }

    public function allFiles()
    {
        return $this->files;
    }

    public function allHeaders()
    {
        return $this->headers;
    }

    public function allCookies()
    {
        return $this->cookies;
    }

    public function isJson()
    {
        return $this->header('Content-Type') === 'application/json';
    }

    public function json()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function isXml()
    {
        return $this->header('Content-Type') === 'application/xml';
    }

    public function xml()
    {
        return simplexml_load_string(file_get_contents('php://input'));
    }

    public function isAjax()
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    public function isSecure()
    {
        return $this->protocol === 'HTTPS';
    }

    public function isGet()
    {
        return $this->method === 'GET';
    }

    public function isPost()
    {
        return $this->method === 'POST';
    }

    public function isPut()
    {
        return $this->method === 'PUT';
    }

    public function isPatch()
    {
        return $this->method === 'PATCH';
    }

    public function isDelete()
    {
        return $this->method === 'DELETE';
    }

    public function isOptions()
    {
        return $this->method === 'OPTIONS';
    }

    public function isHead()
    {
        return $this->method === 'HEAD';
    }

    public function isMethod($method)
    {
        return $this->method === $method;
    }

    public function is($method)
    {
        return $this->isMethod($method);
    }

    public function isUrl($url)
    {
        return $this->url === $url;
    }

    public function isPath($path)
    {
        return $this->path === $path;
    }

    public function isHostname($hostname)
    {
        return $this->hostname === $hostname;
    }

    public function isProtocol($protocol)
    {
        return $this->protocol === $protocol;
    }

    public function isSecureProtocol()
    {
        return $this->isSecure();
    }

    public function isHttpProtocol()
    {
        return !$this->isSecure();
    }
}
