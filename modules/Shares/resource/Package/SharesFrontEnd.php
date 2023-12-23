<?php

namespace Shares\Package;

use RouteInterface;
use Uss;
use Route;
use SharesImmutable;
use UserDashboard;

class SharesFrontEnd
{
    protected Uss $uss;

    public function __construct()
    {
        $this->uss = Uss::instance();
        $userDashboard = UserDashboard::instance();

        $globals = [
            'front_uri' => $this->uss->abspathToUrl(SharesImmutable::TEMPLATE_DIR . '/frontend'),
            'register_url' => $userDashboard->urlGenerator('/register')->getResult(),
            'login_url' => $userDashboard->urlGenerator("/")->getResult(),
        ];

        foreach($globals as $key => $value) {
            $this->uss->addGlobalTwigOption($key, $value);
        }
        
        $this->indexRoute();
        $this->aboutRoute();
        $this->planRoute();
        $this->blogRoute();
        $this->contactRoute();
        $this->faqRoute();
        $this->termsConditionRoute();
    }

    protected function indexRoute(): void
    {
        new Route("/", new class () implements RouteInterface {
            public function onload(array $matches)
            {
                $uss = Uss::instance();
                $uss->render("@Shares/frontend/index.html.twig", [
                    'page_title' => $uss->options->get('company:name'),
                ]);
            }
        });
    }

    protected function aboutRoute(): void
    {
        new Route("/about", new class () implements RouteInterface {
            public function onload(array $matches)
            {
                Uss::instance()->render('@Shares/frontend/about.html.twig', [
                    'banner' => [
                        'title' => 'About Company',
                        'name' => 'About'
                    ]
                ]);
            }
        });
    }

    protected function planRoute(): void
    {
        new Route("/plan", new class () implements RouteInterface {
            public function onload(array $matches)
            {
                Uss::instance()->render('@Shares/frontend/plan.html.twig', [
                    'banner' => [
                        'title' => 'Investment Plan',
                        'name' => 'Investment Plan'
                    ]
                ]);
            }
        });
    }

    protected function blogRoute(): void
    {
        new Route("/blog", new class () implements RouteInterface {
            public function onload(array $matches)
            {
                Uss::instance()->render('@Shares/frontend/blog.html.twig', [
                    'banner' => [
                        'title' => 'Latest Blog Post',
                        'name' => 'Blog Post'
                    ]
                ]);
            }
        });
    }

    protected function contactRoute(): void
    {
        new Route("/contact", new class () implements RouteInterface {
            public function onload(array $matches)
            {
                Uss::instance()->render('@Shares/frontend/contact.html.twig', [
                    'banner' => [
                        'title' => 'Get in Touch with Us',
                        'name' => 'Contact'
                    ]
                ]);
            }
        });
    }

    protected function faqRoute(): void
    {
        new Route("/faq", new class () implements RouteInterface {
            public function onload(array $matches)
            {
                Uss::instance()->render('@Shares/frontend/faq.html.twig', [
                    'banner' => [
                        'title' => 'Freqently Asked Questions',
                        'name' => 'FAQS'
                    ]
                ]);
            }
        });
    }

    protected function termsConditionRoute(): void
    {
        new Route("/tos", new class () implements RouteInterface {
            public function onload(array $matches)
            {
                Uss::instance()->render('@Shares/frontend/terms-conditions.html.twig', [
                    'banner' => [
                        'title' => 'Terms & Condition',
                        'name' => 'TOS'
                    ]
                ]);
            }
        });
    }
}
 