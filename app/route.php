<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\GuestMiddleware;

$app->get('/','HomeController:index')->setName('home');


$app->get('/home','HomeController:index')->setName('home');

$app->group('',function(){

$this->get('/auth/signup','AuthController:getSignUp')->setName('auth.signup');
$this->post('/auth/signup','AuthController:postSignUp');

$this->get('/auth/signin','AuthController:getSignIn')->setName('auth.signin');
$this->post('/auth/signin','AuthController:postSignIn');

})->add(new GuestMiddleware($container));
$app->group('',function(){

$this->get('/auth/signout','AuthController:getSignOut')->setName('auth.signout');
$this->get('/rent/get-free-cars','RentingController:getFreeCars')->setName('rent.getfreecars');
$this->get('/rent/get-own-rents','RentingController:getOwnRents')->setName('rent.getownrents');


})->add(new AuthMiddleware($container));


$app->group('',function(){

$this->get('/rent/add-car','RentingController:getAddCar')->setName('rent.addcar');
$this->post('/rent/add-car','RentingController:postAddCar');

$this->get('/rent/add-rent','RentingController:getAddRent')->setName('rent.addrent');
$this->post('/rent/add-rent','RentingController:postAddRent');

$this->get('/rent/edit-rents','RentingController:getEditRent')->setName('rent.editrents');
$this->get('/rent/rent-data','RentingController:getRentData')->setName('rent.editrentdata');
$this->post('/rent/edit-rents','RentingController:postEditRent');


$this->get('/rent/get-history','RentingController:getRentHistory')->setName('rent.get-history');

$this->get('/rent/remove-car','RentingController:getRemoveCar')->setName('rent.remove-car');
$this->post('/rent/remove-car','RentingController:postRemoveCar');


})->add(new AdminMiddleware($container));



