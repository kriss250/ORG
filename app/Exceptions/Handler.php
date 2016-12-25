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
        $email = \App\Settings::get("log_email");
        $msg = $e->getMessage()." on Line # ".$e->getLine()." in ".$e->getFile();
        \Mail::later(50,[],[], function ($message) use($email,$msg) {
            $message->from("orgsystem250@gmail.com","ORG LOGS");
            $message->to($email->value);
            $message->setSubject("ORG # Bug Report".rand(0,9));
            $message->setBody($msg);
        });
        return parent::report($e);
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

        return response()->view('errors.Error', ["e"=>$e->getMessage()." Line # ".$e->getLine()], 503);
    }
}
