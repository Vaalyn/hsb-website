<?php

declare(strict_types=1);

namespace HackerspaceBielefeld\Website\Controller;

use HackerspaceBielefeld\Website\Service\SpaceApiClient;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Slim\Views\Twig;
use Symfony\Component\Finder\Finder;

abstract class AbstractPageController
{
    public string $pageContentFilename = '';

    public function __construct(
        protected Flash\Messages $flashMessages,
        protected SpaceApiClient $spaceApiClient
    ) {
    }

    public function __invoke(ServerRequest $request, Response $response, array $args): ResponseInterface {
        $twig = Twig::fromRequest($request);

        $spaceStatus = $this->spaceApiClient->getSpaceStatus();

        return $twig->render(
            $response,
            'page.html.twig',
            [
                'pageContent' => $this->getPageContent(),
                'messages' => $this->flashMessages->getMessages(),
                'spaceStatus' => $spaceStatus,
            ]
        );
    }

    protected function getPageContent(): string {
        $finder = new Finder();
        $finder->files()
            ->in(__DIR__ . '/../../content/page')
            ->name($this->pageContentFilename);

        $pageContent = '';

        foreach ($finder as $file) {
            $pageContent .= $file->getContents();
        }

        return $pageContent;
    }
}
