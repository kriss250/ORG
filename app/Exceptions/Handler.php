<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
       // return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {

        if($request->ajax()){
            self::emailLog($e->__toString());
            return new \Illuminate\Http\Response($e->__toString(),500);
        }
        return response()->view('errors.Error', ["ex"=>$e, "e"=>$e->getMessage()." Line # ".$e->getLine()], 500);
    }

    public static function emailLog($_msg)
    {
        $email = \App\Settings::get("log_email");
        $msg = $_msg;
        try {
            \Mail::later(50,[],[], function ($message) use($email,$msg) {
                $message->from("orgsystem250@gmail.com","ORG LOGS");
                $message->to($email);
                $message->setSubject("Bug Report ".\App\Settings::get("name"));
                $message->setBody($msg);
            });
        }catch(Exception $ex){
          // echo $ex;
        }
    }
}
