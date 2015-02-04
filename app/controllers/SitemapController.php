<?php

namespace App\Controllers;

use App\Models\Agents,
    Phalcon\Http\Response;

class SitemapController extends ControllerBase
{

    public function initialize()
    {
        $this->view->setVar('title', $this->title);
        $this->view->disable();
    }
    public function indexAction()
    {

        $response = new Response();

        $expireDate = new \DateTime();
        $expireDate->modify('+1 day');

        $response->setExpires($expireDate);

        $response->setHeader('Content-Type', "application/xml; charset=UTF-8");

        $sitemap = new \DOMDocument("1.0", "UTF-8");

        $urlset = $sitemap->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        $baseUrl = $this->config->site->url;
        $baseUrl .= 'http://proreverse.com';

        //base
        $url = $sitemap->createElement('url');
        $url->appendChild($sitemap->createElement('loc', $baseUrl .'/'));
        $url->appendChild($sitemap->createElement('changefreq', 'daily'));
        $url->appendChild($sitemap->createElement('lastmod', date('Y-m-d')));
        $url->appendChild($sitemap->createElement('priority', '1.0'));
        $urlset->appendChild($url);


        /**
         * List of pages
         */
        $pages = array(

                            );
        /**
         * Loop through each page and build url in sitemap
         */
        foreach ($pages as $page) {
                $url = $sitemap->createElement('url');
                $url->appendChild($sitemap->createElement('loc', $baseUrl . '/' . $page));
                $url->appendChild($sitemap->createElement('changefreq', 'weekly'));
                $url->appendChild($sitemap->createElement('lastmod', date('Y-m-d')));
                $url->appendChild($sitemap->createElement('priority', '0.5'));
                $urlset->appendChild($url);
            }


        /**
         * Build link for each active agent
         */
        $agent_object = new Agents();
        $agents = $agent_object->getAgents();

        $modifiedAt = new \DateTime();
        $modifiedAt->setTimezone(new \DateTimeZone('America/Los_Angeles'));

        //loop through agents for url

        foreach ($agents as $agent) {

            $modifiedAt->setTimestamp(strtotime($agent->getDateEntered()));

            $url = $sitemap->createElement('url');
            $href = $baseUrl . '/' . strtolower($agent->getFirstName()) . '-' . strtolower($agent->getLastName());
            $url->appendChild(
                $sitemap->createElement('loc', $href)
            );

            $url->appendChild($sitemap->createElement('lastmod', $modifiedAt->format('Y-m-d\TH:i:s\Z')));
            $urlset->appendChild($url);
        }

        $sitemap->appendChild($urlset);

        $response->setContent($sitemap->saveXML());
        return $response;

    }

}

