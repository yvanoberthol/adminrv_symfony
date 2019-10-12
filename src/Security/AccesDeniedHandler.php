<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 12/10/2019
 * Time: 03:24
 */

namespace App\Security;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccesDeniedHandler implements AccessDeniedHandlerInterface
{

    /**
     * Handles an access denied failure.
     *
     * @param Request $request
     * @param AccessDeniedException $accessDeniedException
     * @return Response
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {

            $html = $twig->render('error403.html.twig');
            return new Response($html,403);

    }
}