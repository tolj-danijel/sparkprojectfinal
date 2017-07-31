<?php

namespace App\Controllers\RentingSystem;

use App\Controllers\Controller;
use Slim\Views\Twig as View;
use App\Models\Car as Car;
use App\Models\User as User;
use App\Models\RentedCar as RentedCar;
use Respect\Validation\Validator as v;
use Illuminate\Database\Eloquent\Collection;

class RentingController extends Controller
{
    public function getAddCar($request,$response)
    {
        return $this->view->render($response,'rent/addcar.html.twig');
    }
    public function postAddCar($request,$response)
    {
          $validation = $this->validator->validate($request,[
            'make' => v::notEmpty(),
            'model' => v::notEmpty(),
            'year' => v::notEmpty(),
        ]);

        if($validation->failed())
        {
             $this->flash->addMessage('danger','Fields not filled in!!');            
            return $response->withRedirect($this->router->pathFor('rent.addcar'));
        }
               Car::create([
            'make'=>$request->getParam('make'),
            'model'=>$request->getParam('model'),
            'year'=>$request->getParam('year')
        ]);

            $this->container->flash->addMessage('info','A car has been added!');
            return $response->withRedirect($this->router->pathFor('home')); 
            
    }


    public function getFreeCars($request,$response)
    {
        $collection=Car::where('is_rented','0')->where('is_visible','1')->get();

        $cars = [];
        foreach($collection as $car)
        {
            $obj['make']=$car->make;
            $obj['model']=$car->model;
            $obj['year']=$car->year;

            $cars[]=$obj;   
        }

        return $this->view->render($response,'rent/getfreecars.html.twig',['cars'=>$cars]);
    }

     public function getAddRent($request,$response)
    {

        $colusers=User::all();
        $colcars=Car::where('is_rented','0')->get();

        $users=[];

        foreach($colusers as $user)
        {
            $objuser['username']=$user->username;
            $users[]=$objuser;
        }


        $cars = [];


        
        foreach($colcars as $car)
        {
            $obj['id']=$car->car_id;
            $obj['make']=$car->make;
            $obj['model']=$car->model;
            $obj['year']=$car->year;

            $cars[]=$obj;   
        }


        return $this->view->render($response,'rent/addrent.html.twig',['users'=>$users,'cars'=>$cars]);
    
    }
    
    public function postAddRent($request,$response)
    {
        $date = date('m-d-Y h:i:s a', time());
        if(!empty($request->getParam('date')))
        {
            $date= $request->getParam('date');
        }
        $userId=User::where('username',$request->getParam('user'))->get()->first()->user_id;
        $carId=Car::where('car_id',$request->getParam('car'))->get()->first()->car_id;

         RentedCar::create([
            'user_id'=>$userId,
            'car_id'=>$carId,
            'date_rented'=>$request->getParam('date')
        ]);

        Car::where('car_id',$carId)->update(['is_rented'=>1]);

        
            $this->container->flash->addMessage('info','A car has been rented!');
            return $response->withRedirect($this->router->pathFor('home')); 

    }

        public function getOwnRents($request,$response)
        {

        $userId=User::where('user_id',$_SESSION['user'])->get()->first()->user_id;


        $collection = RentedCar::where('user_id',$userId)->get();
        
        $details=[];

        foreach($collection as $rent)
        {
            $car=Car::where('car_id',$rent->car_id)->get()->first();
            $objRent['make']=$car->make;
            $objRent['model']=$car->model;
            $objRent['year']=$car->year;

            $objRent['date_rented']=$rent->date_rented;
            $objRent['date_returned']=$rent->date_returned;
            $objRent['price']=$rent->price;

            $details[]=$objRent;
        }

            
        return $this->view->render($response,'rent/getownrents.html.twig',['rents'=>$details]);
        
        }



        public function getEditRent($request,$response)
        {

        $collection = RentedCar::where('active',1)->get();
        
        $details=[];

        foreach($collection as $rent)
        {
            $car=Car::where('car_id',$rent->car_id)->get()->first();
            $user=User::where('user_id',$rent->user_id)->get()->first();

            $objRent['make']=$car->make;
            $objRent['model']=$car->model;
            $objRent['year']=$car->year;

            
            $objRent['username']=$user->username;
            $objRent['user_id']=$user->user_id;
            
            $objRent['rent_number']=$rent->rent_number;            
            $objRent['date_rented']=$rent->date_rented;
            $objRent['date_returned']=$rent->date_returned;
            $objRent['price']=$rent->price;

            $details[]=$objRent;
        }

            
        return $this->view->render($response,'rent/editrents.html.twig',['rents'=>$details]);

        }

        public function getRentData($request,$response)
        {

            if(empty($request->getParam('rent_number')))
            {
            return $response->withRedirect($this->router->pathFor('rent.editrents')); 
                
            }

            $rentedCar= RentedCar::where('rent_number',$request->getParam('rent_number'))->get()->first();
            $car['returned_date']=$rentedCar->returned_date;
            $car['rented_date']=$rentedCar->rented_date;
            $car['rent_number']=$rentedCar->rent_number;
        return $this->view->render($response,'rent/rentdata.html.twig',['rent'=>$car]);
            

        }

        public function postEditRent($request,$response)
        {


            if(!empty($request->getParam('date_rented')))
            {
             RentedCar::where('rent_number',$request->getParam('rent_number'))->update([
                 'date_rented'=>$request->getParam('date_rented')
                 ]);
            }


            if(!empty($request->getParam('return')))
            {
             $carId=RentedCar::where('rent_number',$request->getParam('rent_number'))->get()->first()->car_id;                        
             RentedCar::where('rent_number',$request->getParam('rent_number'))->update(['active'=>0]);
             Car::where('car_id',$carId)->update([
                 'is_rented'=>0]
                 );
            }   


            
             RentedCar::where('rent_number',$request->getParam('rent_number'))->update([
                 'date_returned'=>$request->getParam('date_returned'),
                 'price'=>$request->getParam('price')
                 ]);
             
             
             $this->flash->addMessage('info','Rent info edited!');            
             return $response->withRedirect($this->router->pathFor('rent.editrents'));


        }

         public function getRentHistory($request,$response)
         {

          
        $collection = RentedCar::where('active',0)->get();
        
        
        $details=[];

        foreach($collection as $rent)
        {
              
            $car=Car::where('car_id',$rent->car_id)->get()->first();
        
            $objRent['username']=User::where('user_id',$rent->user_id)->get()->first()->username;
            $objRent['make']=$car->make;
            $objRent['model']=$car->model;
            $objRent['year']=$car->year;

            $objRent['date_rented']=$rent->date_rented;
            $objRent['date_returned']=$rent->date_returned;
            $objRent['price']=$rent->price;

            $details[]=$objRent;
        }

            
        return $this->view->render($response,'rent/getrenthistory.html.twig',['rents'=>$details]);
        

         }

         public function getRemoveCar($request,$response)
         {

                $collection= Car::where('is_visible','1')->where('is_rented',0)->get();

                $cars=[];
                 foreach($collection as $rent)
                {
                    $obj['id']=$rent->car_id;
                    $obj['make']=$rent->make;
                    $obj['model']=$rent->model;
                    $obj['year']=$rent->year;
                    
                    $cars[]=$obj;
                }

             return $this->view->render($response,'rent/delete-car.html.twig',['cars'=>$cars]);
                

         }


         public function postRemoveCar($request,$response)
         {
            Car::where('car_id',$request->getParam('car'))->update(['is_visible'=>0]);

             $this->container->flash->addMessage('info','A car has been removed!');
            
             return $response->withRedirect($this->router->pathFor('home'));
            
         }
}