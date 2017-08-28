<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

$end = false;

class SearchController extends Controller
{
    private function renderPage($page)
    {
        $templating = $this->container->get('templating');
        $html = $templating->render($page);
        return $html;
    }

    private function getChipDipContent(int $page, string $query, Crawler $crawler)
    {
        if ($page > 0){
            $content = $this->getContent('https://www.ru-chipdip.by/search?searchtext=', $query, $page);
            $crawler = new Crawler($content);
        }
        return $crawler;
    }


    private function getComponentsFromSite(Crawler $crawler, string $query, int $page, bool $isChipDip):array
    {
        $components = [];
        if ($isChipDip){
            $crawler = $this->getChipDipContent($page, $query, $crawler);
            $answer = $crawler->filterXPath("//*[@id='search_items']/tr[@class='with-hover']");
        }else{
            $answer = $crawler->filterXPath("//*[@class='tbl-cel1']/div/div[@class='cat-item']");
        }
        foreach ($answer as $key => $value){
            array_push($components, $value);
        }
        return $components;
    }

    private function parseChipDipComponents(Crawler $crawler)
    {
        $temp = [];
        if ($crawler->filter('td.img > div')->getNode(0) === null) {
            $temp['img'] = '';
        }else {
            $temp['img'] = $crawler->filter('td.img > div > span > img')->attr('src');
        }
        $temp['a'] = 'https://www.ru-chipdip.by/' . $crawler->filter('td.h_name > div > a')->attr('href');
        $temp['name'] = $crawler->filter('td.h_name > div > a')->text();
        $temp['cost'] = $crawler->filter('td.h_pr > span')->text();
        $temp['enabled'] = $crawler->filter('td.h_av > div > span')->text();
        return $temp;
    }

    private function parseBelChipComponents(Crawler $crawler)
    {
        $temp = [];
        $temp['img'] = 'http://belchip.by/' . $crawler->filter('div.cat-pic > a')->last()->filter('img')->attr('src');
        $temp['a'] = 'http://belchip.by/' . $crawler->filter('div.cat-pic > a')->last()->attr('href');
        $temp['name'] = $crawler->filter('h3 > a')->text();
        if ($crawler->filter('div.butt-add > span > div.denoPrice')->getNode(0) === null) {
            $temp['cost'] = '';
            $temp['enabled'] = 'По запросу';
        }else {
            $temp['cost'] = $crawler->filter('div.butt-add > span > div.denoPrice')->text();
            $temp['enabled'] = 'Со склада';
        }

        return $temp;
    }


    private function parseComponents(array $components, bool $isBelChip): array
    {
        $parsedComponents = [];
        foreach ($components as $component){
            $crawler = new Crawler($component);
            if ($isBelChip)
                $temp = $this->parseChipDipComponents($crawler);
            else
                $temp = $this->parseBelchipComponents($crawler);
            array_push($parsedComponents, $temp);
        }
        return $parsedComponents;
    }

    private function checkPagesCount(Crawler $crawler): int
    {
        $pages = 0;
        $answer = $crawler->filterXPath("//*[@id='pager']")->filter('ul > li.pager__page');
        foreach ($answer as $key => $value){
            $pages++;
        }
        return $pages;
    }

    private function getAllComponentsFromOneSite(Crawler $crawler, string $query, bool $isChipDip)
    {
        $components = [];
        if ($this->checkingPresenceComponents($crawler, $isChipDip)){
            if ($isChipDip)
                $pages = $this->checkPagesCount($crawler);
            else
                $pages = 0;
            if ($pages > 0) {
                for ($i = 1; $i <= $pages; $i++) {
                    $tempComponents = $this->getComponentsFromSite($crawler, $query, $i, $isChipDip);
                    $components = array_merge($components, $this->parseComponents($tempComponents, $isChipDip));
                }
            }else{
                $tempComponents = $this->getComponentsFromSite($crawler, $query, 0, $isChipDip);
                $components = array_merge($components, $this->parseComponents($tempComponents, $isChipDip));
            }
        }
        return $components;
    }

    private function getPartComponentsFromOneSite(Crawler $crawler, string $query, string $page, bool $isChipDip)
    {
        $components = [];
        if ($this->checkingPresenceComponents($crawler, $isChipDip)) {
            $tempComponents = $this->getComponentsFromSite($crawler, $query, $page, $isChipDip);
            $components = array_merge($components, $this->parseComponents($tempComponents, $isChipDip));
        }
        return $components;
    }


    private function getCrawler(string $query, string $url)
    {
        $content = $this->getContent($url, $query, 0);
        $crawler = new Crawler($content);
        return $crawler;
    }

    private function checkPageOnChipDip(Crawler $crawler, string $page): bool
    {
        $pages = 0;
        $answer = $crawler->filterXPath("//*[@id='pager']")->filter('ul > li.pager__page');
        foreach ($answer as $key => $value){
            $pages++;
        }
        if ($pages == 0)
            return true;
        if ($page > $pages)
            return false;
        else
            return true;
    }



    private function getComponents(string $query, string $loadingPart, bool &$end): array
    {
        $components = [];
        $crawler = $this->getCrawler($query, 'https://www.ru-chipdip.by/search?searchtext=');
        $currentPage = $this->checkPageOnChipDip($crawler, $loadingPart);
        if ($currentPage) {
            $components = $this->getPartComponentsFromOneSite($crawler, $query, $loadingPart, true);
            if (count($components) > 0){
                return $components;
            }

        }
        $crawler = $this->getCrawler($query, 'http://belchip.by/search/?query=');
        $components = array_merge($components, $this->getAllComponentsFromOneSite($crawler, $query, false));
        $end = true;

        return $components;
    }


    private function getContent(string $url, string $query, int $page)
    {
        if ($page > 0)
            $url = $url . $query . '&page=' . $page;
        else
            $url = $url . $query;
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($handle);
        curl_close($handle);
        return $content;
    }

    private function checkingPresenceComponentsOnChipDip(Crawler $crawler): bool
    {
        $answer = $crawler->filterXPath("//*[@id='content_main']/div/h1/sub");
        if ($answer->text() > 0)
            return true;
        else
            return false;
    }

    private function checkingPresenceComponentsOnBelChip(Crawler $crawler): bool
    {
        $answer = $crawler->filterXPath("//*[@class='c-div']/h1");
        if ($answer->getNode(0) === null)
            return false;
        else
            return true;
    }

    private function checkingPresenceComponents(Crawler $crawler, bool $isChipDip):bool
    {
        if ($isChipDip){
            return $this->checkingPresenceComponentsOnChipDip($crawler);
        }else{
            return $this->checkingPresenceComponentsOnBelChip($crawler);
        }
    }

    private function generateHTML(array $components)
    {
        $html = '';
        foreach ($components as $component){
            $html .= '<tr><td class="text-center"><img class="img_size" src="'.$component['img'].'"></td><td><a href="'.$component['a'].'">'.$component['name'].'</a></td><td>'.$component['enabled'].'</td><td>'.$component['cost'].'</td></tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * @Route("/search", name = "search")
     */
    public function showSearchAction()
    {
        return new Response($this->renderPage('search/index.html.twig'));
    }

    /**
     * @Route("/start_search", name = "start_search")
     */
    public function startSearchAction()
    {
        $end = false;
        $query = urlencode($_GET['search']);
        $loadingPart = $_GET['loadPart'];
        if(strlen($query) < 2)
            return new Response('<h2 class="text-center">Nothing found</h2>');
        $components = $this->getComponents($query, $loadingPart, $end);

        if (count($components) > 0){
            $html = $this->generateHTML($components);
            if ($end)
                $html .= 'stop';
            return new Response($html);
        }else{
            return new Response('<h2 class="text-center">Nothing found</h2>');
        }
    }
}