<?php

namespace App\Service;

use App\Entity\Participation;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ParticipationPdfService
{
    public function __construct(
        private Environment $twig
    ) {
    }

    
    public function generatePdfResponse(Participation $participation): Response
    {
        $html = $this->twig->render('participation/pdf.html.twig', [
            'participation' => $participation,
            'generatedAt' => new \DateTime(),
        ]);

        $response = new Response($html);
        $response->headers->set('Content-Type', 'text/html; charset=utf-8');
        $response->headers->set('Content-Disposition', 'inline; filename="participation-' . $participation->getId() . '.html"');
        
        return $response;
    }

   
    public function generatePdfHtml(Participation $participation): string
    {
        return $this->twig->render('participation/pdf.html.twig', [
            'participation' => $participation,
            'generatedAt' => new \DateTime(),
        ]);
    }
}
