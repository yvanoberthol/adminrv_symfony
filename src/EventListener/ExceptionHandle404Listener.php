<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 13/10/2019
 * Time: 04:08
 */

namespace App\EventListener;


use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHandle404Listener
{
    private $exception_handler;


    /**
     * ExceptionHandle404Listener constructor.
     * @param $exception_handler
     */
    public function __construct($exception_handler)
    {
        $this->exception_handler = $exception_handler;
    }

    public function process404(ExceptionEvent $event){

        if (!$event->getException() instanceof NotFoundHttpException){
            return;
        }

        $response = $this->exception_handler->show();

        $event->setResponse($response);
    }
}