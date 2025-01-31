<?php

namespace App\Http\Controller;

use App\Http\Requirements;
use App\Http\Security\CourseVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller utilisé par nginx pour le auth_request
 * Plus d'infos : http://nginx.org/en/docs/http/ngx_http_auth_request_module.html.
 */
class NginxAuthController extends AbstractController
{
    #[Route(path: '/downloads/videos/{video}', name: 'stream_video', requirements: ['video' => Requirements::ANY])]
    public function videos(): Response
    {
        if ($this->isGranted(CourseVoter::DOWNLOAD_VIDEO)) {
            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_FORBIDDEN);
    }

    #[Route(path: '/downloads/sources/{source}', name: 'download_source', requirements: ['source' => Requirements::ANY])]
    public function sources(): Response
    {
        if ($this->isGranted(CourseVoter::DOWNLOAD_SOURCE)) {
            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_FORBIDDEN);
    }

    #[Route(path: '/report.html', name: 'report_stats')]
    public function report(): Response
    {
        if ($this->isGranted('admin')) {
            return new Response(null, Response::HTTP_OK);
        }

        return new Response(null, Response::HTTP_FORBIDDEN);
    }

    #[Route(path: '/goaccessws', name: 'report_ws')]
    public function goaccess(): Response
    {
        return $this->report();
    }
}
