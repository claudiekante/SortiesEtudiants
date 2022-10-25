<?php

namespace App\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler extends AbstractController implements AccessDeniedHandlerInterface
{

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $this->addFlash('danger', 'Accès refusé');
        return $this->redirect($this->generateUrl('main_accueil'));
    }
}