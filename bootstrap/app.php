<?php

use Respect\Validation\Validator as v;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$app= new \Slim\App([

    'settings'=>[
        'displayErrorDetails'=>true,
        'addContentLengthHeader' => false,
        'db'=>[
            'driver'=>'mysql',
            'host'=>'127.0.0.1',
            'database'=>'rentacar',
            'username'=>'root',
            'password'=>'',
            'charset'=>'utf8',
            'prefix'=>'',
        ]
    ],

]);



$container= $app->getContainer();

$GLOBALS['adminpw']='$2y$10$G7qD/FKw1/8IvA755wP9fuNp3kElY7oYWSJKeUc7BaWQ/Jc2tjJUK';
$GLOBALS['adminuname']='admin';

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db']= function($container) use ($capsule)
{
    return $capsule;
};  

$container['csrf']= function($container)
{
    return new \Slim\Csrf\Guard();

};

$container['flash']= function($container)
{
    return new \Slim\Flash\Messages();
};

$container['view'] = function($container)
{

    $view= new \Slim\Views\Twig(__DIR__ . '/../resources/views',[
        'cache'=>false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

            

    $view->getEnvironment()->addGlobal('auth',[
    'check'=> $container->auth->check(),
    'ifadmin'=> $container->auth->checkIfAdmin(),
    'user' => $container->auth->user()
    ]);

    $view->getEnvironment()->addGlobal('flash',$container->flash);


    return $view;   
};

$container['HomeController']= function($container)
{
        return new \App\Controllers\HomeController($container);
};
$container['AuthController']= function($container)
{
        return new \App\Controllers\Auth\AuthController($container);
};
$container['RentingController']= function($container)
{
        return new \App\Controllers\RentingSystem\RentingController($container);
};
$container['validator']= function($container)
{
        return new \App\Controllers\Validation\Validator($container);
};

$container['auth']= function($container)
{
        return new \App\Auth\Auth;
};



$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));



$app->add($container['csrf']);

v::with('App\\Controllers\\Validation\\Rules\\');

require __DIR__ .'/../app/route.php';
