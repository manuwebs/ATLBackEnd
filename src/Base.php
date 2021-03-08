<?php
namespace Src;

class Base
{
    public $db;
    private $requestMethod;
    public $id;
    private $findQuery;

    public function __construct($db, $requestMethod, $id, $findQuery)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->id = $id;
        $this->findQuery = $findQuery;
    }

    final public function processRequest()
    {
        switch ($this->requestMethod) {
            
            case 'GET':
                if ($this->id) {
                    $response = $this->getSingle();
                } else {
                    $response = $this->getAll();
                };
                break;

            case 'POST':
                $response = $this->create();
                break;

            case 'PUT':
                $response = $this->update();
                break;

            case 'DELETE':
                $response = $this->delete();
                break;

            default:
                $response = $this->notFoundResponse();
                break;
        }

        header($response['statusCode']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    //Must override
    public function getAll()
    {
        return $this->needChildImplementation();
    }

    public function create()
    {
        return $this->needChildImplementation();
    }

    public function update()
    {
        return $this->needChildImplementation();
    }

    public function delete()
    {
        return $this->needChildImplementation();
    }

    final public function _getAll($query)
    {
        try {
            $statement = $this->db->query($query);
            $result = $statement->fetchAll(\PDO::FETCH_CLASS);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
        
        return $this->prepareResponse('200', $result);
    }

    final public function getSingle()
    {
        $result = $this->find($this->id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        
        return $this->prepareResponse('200', $result);
    }


    //Base functions

    public function find($id)
    {
        try {
            $statement = $this->db->prepare($this->findQuery);
            $statement->execute(['id' => $id]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function prepareResponse($statusCode, $body)
    {
        $headerMessage;
        switch ($statusCode) {
            
            case '200':
                $headerMessage = 'OK';
                break;

            case '201':
                $headerMessage = 'Created';
                break;
                
            case '400':
                $headerMessage = 'Bad Request';
                break;
            
            case '404':
                $headerMessage = 'Not Found';
                break;
        }

        $response['statusCode'] = 'HTTP/1.1 '.$statusCode.' '.$headerMessage;
        $response['body'] = json_encode($body);

        return $response;
    }

    public function badRequestResponse()
    {
        return $this->prepareResponse('400', ['error' => 'Invalid input']);
    }

    public function notFoundResponse()
    {
        return $this->prepareResponse('404', null);
    }

    public function needChildImplementation()
    {
        return $this->prepareResponse('400', ['message' => 'need to implement on child']);
    }
}
