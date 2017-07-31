<?php
use \Psr\Http\Message\ServerRequestInterface as Request; 
use \Psr\Http\Message\ResponseInterface as Response; 
require '../vendor/autoload.php';

require_once 'Database.php'; 
$app->post('/register', function (Request $request, Response $response) {
    
    $data = $request->getParsedBody(); 
    $username = $data['username']; 

    if(!Database::userExists($username))
    {
        if(Database::registerUser($data))
        {
          $response->getBody()->write(json_encode($data).'user registered!');         
        }
    }
    else
        $response->getBody()->write(json_encode('user not registered!'));

    
    return $response; 
}); ?>