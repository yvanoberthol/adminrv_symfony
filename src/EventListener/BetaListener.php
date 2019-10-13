<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 13/10/2019
 * Time: 00:00
 */

namespace App\EventListener;


use Symfony\Component\HttpKernel\Event\ResponseEvent;

class BetaListener
{

    protected $betaHtmlAdder;
    protected $endDate;

    /**
     * BetaListener constructor.
     * @param $betaHtmlAdder
     * @param $endDate
     */
    public function __construct($betaHtmlAdder, $endDate)
    {
        $this->betaHtmlAdder = $betaHtmlAdder;
        $this->endDate = new \DateTime($endDate);
    }

    public function processusBeta(ResponseEvent $event){

        if (!$event->isMasterRequest()){
            return;
        }

        $remainingDate = $this->endDate->diff(new \DateTime())->days;

        if ($remainingDate <= 0){
            return;
        }

        $response = $this->betaHtmlAdder->addBeta($event->getResponse(),$remainingDate);

        $event->setResponse($response);

    }


}