<?php
use Illuminate\Database\Capsule\Manager as Capsule;

class InstallController extends BaseController
{
    /**
     * seed the database with initial value
     */
    public function seed()
    {
        try{
            Sentry::createUser(array(
                'email'       => 'admin@admin.com',
                'password'    => 'password',
                'first_name'  => 'Website',
                'last_name'   => 'Administrator',
                'activated'   => 1,
                'permissions' => array(
                    'admin'     => 1
                )
            ));
            $message = "Seeding successful";
            $success = true;
        }catch(\Exception $e){
            $message = $e->getMessage();
            $success = false;
        }
        Response::setBody(json_encode(
            array(
                'success' => $success,
                'message' => $message
            )
        ));
    }
}