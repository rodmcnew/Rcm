<?php
/**
 * RCM Route Listener
 *
 * Route Listener for Zend Event "route"
 *
 * PHP version 5.3
 *
 * LICENSE: No License yet
 *
 * @category  Reliv
 * @package   Rcm
 * @author    Westin Shafer <wshafer@relivinc.com>
 * @copyright 2012 Reliv International
 * @license   License.txt New BSD License
 * @version   GIT: <git_id>
 * @link      http://github.com/reliv
 */

namespace Rcm\EventListener;

use Rcm\Entity\Site;
use Rcm\Repository\Redirect as RedirectRepo;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

/**
 * RCM Route Listener
 *
 * This Route listener check that the domain requested is known to the CMS.
 * It will also test the request url to see if a defined redirect exists and
 * redirect the requester if needed.
 *
 * @category  Reliv
 * @package   Rcm
 * @author    Westin Shafer <wshafer@relivinc.com>
 * @copyright 2012 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: 1.0
 * @link      http://github.com/reliv
 */
class RouteListener
{
    /** @var \Rcm\Repository\Redirect */
    protected $redirectRepo;

    protected $currentSite;

    /**
     * Constructor
     *
     * @param Site         $currentSite  Current Site Entity
     * @param RedirectRepo $redirectRepo Rcm Redirect Manager
     */
    public function __construct(
        Site $currentSite,
        RedirectRepo $redirectRepo
    ) {
        $this->currentSite = $currentSite;
        $this->redirectRepo = $redirectRepo;
    }

    /**
     * Check the domain is a known domain for the CMS.  If not the primary, it will
     * redirect the user to the primary domain.  Useful for multiple domain sites.
     *
     * @param MvcEvent $event Zend MVC Event
     *
     * @return null|Response
     */
    public function checkDomain(MvcEvent $event)
    {
        if (empty($this->currentSite->getSiteId())) {
            $response = new Response();
            $response->setStatusCode(404);
            $event->stopPropagation(true);

            return $response;
        }

        $primary = $this->currentSite->getDomain()->getPrimary();

        if (!empty($primary)) {
            $response = new Response();
            $response->setStatusCode(302);
            $response->getHeaders()
                ->addHeaderLine(
                    'Location',
                    '//' . $primary->getDomainName()
                );

            $event->stopPropagation(true);

            return $response;
        }

        return null;

    }

    /**
     * Check the defined redirects.  If requested URL is found, redirect to the
     * new location.
     *
     * @param MvcEvent $event Zend MVC Event
     *
     * @return null|Response
     */
    public function checkRedirect(MvcEvent $event)
    {
        $siteId = $this->currentSite->getSiteId();

        if (empty($siteId)) {
            return null;
        }

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $event->getRequest();

        $serverParam = $request->getServer();
        $requestUri = $serverParam->get('REQUEST_URI');

        $redirectUrl = $this->redirectManager->getRedirectUrl($httpHost, $requestUri); // returns null if not redirecting or returns url if we are

        if (!empty($redirectUrl)) {
            $response = new Response();
            $response->setStatusCode(302);
            $response->getHeaders()
                ->addHeaderLine(
                    'Location',
                    '//' . $redirectUrl
                );
            $event->stopPropagation(true);

            return $response;
        }

        return null;
    }

    /**
     * Set the system locale to Site Requirements
     *
     * @return null
     */
    public function addLocale()
    {
        $locale = $this->currentSite->getLocale();
        setlocale(
            LC_ALL,
            $locale
        );
        \Locale::setDefault($locale);

        return null;
    }
}
