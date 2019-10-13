<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 12/10/2019
 * Time: 23:46
 */

namespace App\Event;


use Symfony\Component\HttpFoundation\Response;

class BetaHtmlAdder
{

    public function addBeta(Response $response,$remainingDays){
        $content = $response->getContent();

        $html = '<div class="bg-warning text-center p-1 mb-5" style="position: fixed; top: 0; right: 0; z-index: 1; width: 8%;">Noël JJ-'.
            '<span class="text-success">'.(int) $remainingDays.'</span> !</div>';

        $content = str_replace('<body>','<body>'.$html,$content);

        //modification de la reponse
        $response->setContent($content);
        return $response;
    }

}