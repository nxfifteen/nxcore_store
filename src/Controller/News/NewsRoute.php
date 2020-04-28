<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * @link      https://nxfifteen.me.uk/projects/nxcore/
 * @link      https://gitlab.com/nx-core/store
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @copyright Copyright (c) 2020. Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @license   https://nxfifteen.me.uk/api/license/mit/license.html MIT
 */

namespace App\Controller\News;

use App\Entity\SiteNews;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/** @noinspection PhpUnused */
class NewsRoute extends CommonNews
{

    /**
     * @Route("/news/personal", name="index_news_personal")
     */
    public function index_news_personal()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $return['items'] = $this->getNewItemsArray($this->patient);

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/news/push", name="index_news_push")
     */
    public function index_news_push()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        /** @var SiteNews[] $newsItems */
        $newsItemsFalse = $this->getDoctrine()
            ->getRepository(SiteNews::class)
            ->findBy(['patient' => $this->patient, 'priority' => 3, 'displayed' => false], ['published' => 'DESC']);

        /** @var SiteNews[] $newsItems */
        $newsItemsNull = $this->getDoctrine()
            ->getRepository(SiteNews::class)
            ->findBy(['patient' => $this->patient, 'priority' => 3, 'displayed' => null], ['published' => 'DESC']);

        $newsItems = array_merge($newsItemsNull, $newsItemsFalse);

        if ($newsItems && is_array($newsItems) && count($newsItems) > 0) {
            $return['items'] = $this->buildNewsItems($newsItems);
        } else {
            $return['items'] = [];
        }

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/news/push/seen", name="index_news_push_seen")
     * @param ManagerRegistry $doctrine
     * @param Request         $request
     *
     * @return JsonResponse
     */
    public function index_news_push_seen(ManagerRegistry $doctrine, Request $request)
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $requestBody = $request->getContent();
        $requestBody = str_replace("'", "\"", $requestBody);
        $requestBody = str_replace('&#39;', "'", $requestBody);
        $requestJson = json_decode($requestBody, false);

        if (is_object($requestJson)) {
            if (property_exists($requestJson, "toastId") && $requestJson->toastId > 0) {
                /** @var SiteNews[] $newsItems */
                $newsItems = $this->getDoctrine()
                    ->getRepository(SiteNews::class)
                    ->findBy(['patient' => $this->patient, 'id' => $requestJson->toastId]);
            } else {
                /** @var SiteNews[] $newsItems */
                $newsItems = $this->getDoctrine()
                    ->getRepository(SiteNews::class)
                    ->findBy(['patient' => $this->patient, 'text' => $requestJson->message]);
            }

            if ($newsItems) {
                $entityManager = $doctrine->getManager();
                foreach ($newsItems as $newsItem) {
                    $newsItem->setDisplayed(true);
                    $entityManager->persist($newsItem);
                }
                $entityManager->flush();
            }
        }

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }

    /**
     * @Route("/news/site", name="index_news_site")
     */
    public function index_news_site()
    {
        $return = [];
        $return['genTime'] = -1;
        $a = microtime(true);

        $this->setupRoute();

        $return['items'] = $this->getNewItemsArray();

        $b = microtime(true);
        $c = $b - $a;
        $return['genTime'] = round($c, 4);
        return $this->json($return);
    }
}
