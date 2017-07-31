<?php

namespace App\Controllers\Auth;

use Slim\Views\Twig as View;
use App\Controllers\Controller;
use App\Models\User;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{


    public function getSignOut($request,$response)
    {
            
            $this->auth->logout();
            return $response->withRedirect($this->router->pathFor('home'));        
    }


    public function getSignIn($request,$response)
    {
        return $this->view->render($response,'auth/signin.html.twig');
    }

    public function postSignIn($request,$response)
    {
        $auth = $this->auth->attempt(
            $request->getParam('username'),
            $request->getParam('password')
        );
        
        
        if(!$auth)
        {
            $this->flash->addMessage('danger','Wrong credentials!');
            return $response->withRedirect($this->router->pathFor('auth.signin')); 
        }

        else
        {
            $this->flash->addMessage('info','You have signed in!');            
            return $response->withRedirect($this->router->pathFor('home'));
        }   
    }



    public function getSignup($request,$response)
    {
        return $this->view->render($response,'auth/signup.html.twig');
    }

    public function postSignup($request,$response)
    {

        $validation = $this->validator->validate($request,[
            'username' => v::alnum()->noWhitespace()->length(4, 16)->notEmpty()->usernameAvailable(),
            'password' => v::noWhiteSpace()->notEmpty(),
        ]);


        if($validation->failed())
        {
            //redirect 
            $this->flash->addMessage('danger','Wrong credentials!');            

            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }

        User::create([
            'username'=>$request->getParam('username'),
            'password_hash'=>password_hash($request->getParam('password'),PASSWORD_DEFAULT),
        ]);

        $this->auth->attempt(
            $request->getParam('username'),
            $request->getParam('password')
        );
        
        $this->flash->addMessage('info','You have signed up!');            

        return $response->withRedirect($this->router->pathFor('home'));
    }
}