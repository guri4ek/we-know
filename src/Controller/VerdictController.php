<?php

declare(strict_types=1);
namespace App\Controller;

use App\Domain\Answer\Uuid;
use App\Http\TrustedOrigin;
use App\Infrastructure\Persistence\VerdictRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VerdictController extends AbstractController
{
    public function __construct(private readonly VerdictRepository $verdicts, private readonly TrustedOrigin $origin) {}

    #[Route('/a/{publicId}', name: 'app_verdict', methods: ['GET', 'HEAD'])]
    public function __invoke(string $publicId): Response
    {
        if (!Uuid::publicId($publicId)) {
            throw $this->createNotFoundException();
        }
        $verdict = $this->verdicts->find($publicId);
        if ($verdict === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('verdict/show.html.twig', ['verdict' => $verdict, 'canonical' => $this->origin->url('/a/'.$verdict->id)], new Response('', 200, ['Cache-Control' => 'no-store', 'X-Robots-Tag' => 'noindex, nofollow']));
    }
}
