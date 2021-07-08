<?php

declare(strict_types=1);

use League\CommonMark\GithubFlavoredMarkdownConverter;

return [
    Slim\Flash\Messages::class => function() {
        return new Slim\Flash\Messages();
    },

    Slim\Views\Twig::class => function() {
        $twig = Slim\Views\Twig::create(
            __DIR__ . '/../templates',
            [/*'cache' => __DIR__ . '/../var/cache'*/'debug' => true,],
        );

        $twig->addExtension(new \Twig\Extra\Intl\IntlExtension());
        $twig->addExtension(new \Twig\Extra\Markdown\MarkdownExtension());

        $twig->addExtension(new \Twig\Extension\DebugExtension());

        $twig->addRuntimeLoader(new class implements Twig\RuntimeLoader\RuntimeLoaderInterface {
            public function load($class) {
                if (Twig\Extra\Markdown\MarkdownRuntime::class === $class) {
                    return new Twig\Extra\Markdown\MarkdownRuntime(
                        new Twig\Extra\Markdown\LeagueMarkdown(
                            new GithubFlavoredMarkdownConverter()
                        )
                    );
                }
            }
        });

        return $twig;
    },
];
