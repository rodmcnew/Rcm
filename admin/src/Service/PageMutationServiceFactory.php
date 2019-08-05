<?php

namespace RcmAdmin\Service;

use Psr\Container\ContainerInterface;
use Rcm\RequestContext\AppContext;
use Rcm\RequestContext\RequestContextBindings;

class PageMutationServiceFactory
{
    public function __invoke(ContainerInterface $requestContext)
    {
        $appContext = $requestContext->get(AppContext::class);

        return new PageMutationService(
            $appContext->get(\RcmUser\Service\RcmUserService::class),
            $appContext->get(\Doctrine\ORM\EntityManager::class),
            $appContext->get('Rcm\ImmutableHistory\PageVersionRepo'),
            $appContext->get('Rcm\ImmutableHistory\SiteWideContainerVersionRepo'),
            $appContext->get(\Rcm\ImmutableHistory\Page\PageContentFactory::class),
            $appContext->get(\Rcm\ImmutableHistory\Page\RcmPageNameToPathname::class),
            $appContext->get(\Rcm\Service\CurrentSite::class), //ideally should come from $requestContext instead
            $requestContext->get(\Rcm\Acl\GetCurrentUser::class)
        );
    }
}
