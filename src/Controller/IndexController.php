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

class IndexController
{
    public function __construct(
        protected Flash\Messages $flashMessages,
        protected SpaceApiClient $spaceApiClient
    ) {
    }

    public function __invoke(ServerRequest $request, Response $response, array $args): ResponseInterface {
        $page = (int) ($args['page'] ?? 1);
        list($blogPosts, $pages) = $this->fetchBlogPosts($page);

        $twig = Twig::fromRequest($request);

        $spaceStatus = $this->spaceApiClient->getSpaceStatus();

        return $twig->render(
            $response,
            'index.html.twig',
            [
                'blogPosts' => $blogPosts,
                'messages' => $this->flashMessages->getMessages(),
                'page' => $page,
                'pages' => $pages,
                'spaceStatus' => $spaceStatus,
            ]
        );
    }

    protected function fetchBlogPosts(int $page, int $perPage = 5): array {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../content/blog');
        $finder->sortByName(true);
        $finder->reverseSorting();

        $blogPosts = [];
        $pages = 1;

        $upperEndFileNr = $page * $perPage;
        $lowerEndFileNr = $upperEndFileNr - $perPage;

        $fileNr = 0;
        foreach ($finder as $file) {
            $fileNr++;

            if ($fileNr <= $upperEndFileNr && $fileNr > $lowerEndFileNr) {
                $fileName = $file->getRelativePathname();

                $filenameComponents = explode('_', $fileName);
                $blogPostDate = $filenameComponents[0];

                $blogPosts[] = [
                    'content' => $file->getContents(),
                    'date' => $blogPostDate,
                ];
            }

            $pages = ceil($fileNr / $perPage);
        }

        return [$blogPosts, $pages];
    }
}
