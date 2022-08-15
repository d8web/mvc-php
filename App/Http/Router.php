<?php

namespace App\Http;

use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;

class Router {

    /**
     * Url completa do projeto (raiz)
     * @var string
     */
    private $url = "";

    /**
     * Prefixo de todas as rotas
     * @var string
     */
    private $prefix = "";

    /**
     * Índice de rotas
     * @var array
     */
    private $routes = [];

    /**
     * Instancia de request
     * @var Request
     */
    private $request;

    /**
     * Content-Type padrão do response
     * @var string
     */
    private $contentType = "text/html";

    /**
     * Método responsável por iniciar a classe
     * @param string $url
     */
    public function __construct($url) {
        $this->request = new Request($this);
        $this->url     = $url;
        $this->setPrefix();
    }

    /**
     * Método responsável por alterar o valor do contentType
     * @param string $contentType
     */
    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    /**
     * Método responsável por definir o prefixo das rotas
     */
    private function setPrefix() {
        // Informações da url atual
        $parseUrl = parse_url($this->url);
        
        // Define o prefixo
        $this->prefix = $parseUrl["path"] ?? "";
    }

    /**
     * Método responsável por adicionar uma rota na classe
     * @param string $method
     * @param string $route
     * @param array @params
     */
    private function addRoute($method, $route, $params = []) {
        // Validação dos parâmetros
        foreach($params as $key => $value) {
            if($value instanceof Closure) {
                $params["controller"] = $value;
                unset($params[$key]);
                continue;
            }
        }

        // Middlewares da rota
        $params["middlewares"] = $params["middlewares"] ?? [];

        // Variáveis da rota
        $params["variables"] = [];

        // Padrão de validação das variáveis das rotas
        $patternVariable = "/{(.*?)}/";
        if(preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, "(.*?)", $route);
            $params["variables"] = $matches[1];
        }

        // Padrão de validação da URL
        $patternRoute = "/^" . str_replace("/", "\/", $route) . "$/";
        
        // Adiciona rota dentro da classe
        $this->routes[$patternRoute][$method] = $params;
    }

    /**
     * Método responsável por definir uma rota GET
     * @param string $route
     * @param array $params
     */
    public function get($route, $params = []) {
        return $this->addRoute("GET", $route, $params);
    }

    /**
     * Método responsável por definir uma rota POST
     * @param string $route
     * @param array $params
     */
    public function post($route, $params = []) {
        return $this->addRoute("POST", $route, $params);
    }

    /**
     * Método responsável por definir uma rota PUT
     * @param string $route
     * @param array $params
     */
    public function put($route, $params = []) {
        return $this->addRoute("PUT", $route, $params);
    }

    /**
     * Método responsável por definir uma rota DELETE
     * @param string $route
     * @param array $params
     */
    public function delete($route, $params = []) {
        return $this->addRoute("DELETE", $route, $params);
    }

    /**
     * Método responsável por retornar a URI desconsiderando o prefixo
     * @return string
     */
    private function getUri() {
        // URI da Request
        $uri = $this->request->getUri();
        
        // Fatia a uri com o prefixo
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        // Retorna a uri sem prefixo
        return rtrim(end($xUri), "/");
    }

    /**
     * Método responsável por retornar os dados rota atual
     * @return array
     */
    private function getRoute() {
        // Uri
        $uri = $this->getUri();
        
        // Método
        $httpMethod = $this->request->getHttpMethod();
        
        // Validar as rotas
        foreach($this->routes as $patternRoute => $methods) {
            // Verificar se a uri bate com o padrão
            if(preg_match($patternRoute, $uri, $matches)) {
                // Verifica o método
                if(isset($methods[$httpMethod])) {
                    // Remove a primeira posição do array matches
                    unset($matches[0]);

                    // Variáveis processadas
                    $keys = $methods[$httpMethod]["variables"];
                    $methods[$httpMethod]["variables"] = array_combine($keys, $matches);
                    $methods[$httpMethod]["variables"]["request"] = $this->request;

                    // Retorno dos parâmetros da rota
                    return $methods[$httpMethod];
                }

                // Método não permitido/definido
                throw new Exception("Método não permitido!", 405);
            }
        }

        // Url não encontrada
        throw new Exception("URL não encontrada!", 404);
    }

    /**
     * Método responsável por executar a rota atual
     * @return Response
     */
    public function run() {
        try {
            // Obtém a rota atual
            $route = $this->getRoute();
            
            // Verificar o controllador
            if(!isset($route["controller"])) {
                throw new Exception("URL não pode ser processada!", 500);
            }

            // Argumentos da função
            $args = [];

            // Reflection
            $reflection = new ReflectionFunction($route["controller"]);
            foreach($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route["variables"][$name] ?? "";
            }

            // Retorna a execução da fila de middlewares
            return (new MiddlewareQueue($route["middlewares"], $route["controller"], $args))->next($this->request);
        } catch(Exception $e) {
            return new Response($e->getCode(),$this->getErrorMessage($e->getMessage()), $this->contentType);
        }
    }

    /**
     * Método responsável por retornar a mensagem de erro com base no content-type
     * @param string $message
     * @return mixed
     */
    private function getErrorMessage($message) {
        switch($this->contentType) {
            case "application/json":
                return [
                    "error" => $message
                ];
                break;
            default:
                return $message;
                break;
        }
    }

    /**
     * Método responsável por retornar a Url atual
     * @return string
     */
    public function getCurrentUrl() {
        return $this->url . $this->getUri();
    }

    /**
     * Método responsável por redirecionar a URL
     * @param string $route
     */
    public function redirect($route) {
        // Url
        $url = $this->url . $route;
        
        // Executa o redirecionamento
        header("location: ".$url);
        exit;
    }

}