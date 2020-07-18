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


use App\Controller\Common;
use App\Entity\Patient;
use App\Entity\SiteNews;

class CommonNews extends Common
{
    protected function buildNewsItems(array $newsItems)
    {
        /** @var SiteNews[] $newsItems */
        /** @var SiteNews[] $newsItemsSorted */
        $newsItemsSorted = [];
        $return = [];
        foreach ($newsItems as $newsItem) {
            $newsItemsSorted[$newsItem->getPublished()->format("U") . $newsItem->getId()] = $newsItem;
        }

        arsort($newsItemsSorted);

        foreach ($newsItemsSorted as $newsItem) {
            if (is_null($newsItem->getExpires()) || $newsItem->getExpires()->format("U") > date("U")) {
                $newReturn = [
                    "id" => $newsItem->getId(),
                    "title" => $newsItem->getTitle(),
                    "text" => $newsItem->getText(),
                    "accent" => str_replace("list-group-item-accent-", "", $newsItem->getAccent()),
                    "displayed" => $newsItem->getDisplayed(),
                    "expires" => $newsItem->getExpires(),
                    "link" => $newsItem->getLink(),
                    "priority" => $newsItem->getPriority(),
                    "published" => $newsItem->getPublished()->format("l, F jS H:i:s"),
                ];

                if (is_null($newsItem->getImage())) {
                    $newReturn['imageName'] = null;
                    $newReturn['imageHref'] = null;
                } else {
                    if (substr($newsItem->getImage(), 0, 4) == "http") {
                        $newReturn['imageName'] = null;
                        $newReturn['imageHref'] = $newsItem->getImage();
                    } else {
                        $newReturn['imageName'] = $newsItem->getImage();
                        $newReturn['imageHref'] = null;
                    }
                }

                $return[] = $newReturn;
            }
        }

        return $return;
    }

    protected function getNewItemsArray(Patient $patient = null)
    {
        /** @var SiteNews[] $newsItemsSite */
        $newsItemsSite = $this->getDoctrine()
            ->getRepository(SiteNews::class)
            ->findBy(['patient' => $patient], ['published' => 'DESC']);

        if ($newsItemsSite) {
            return $this->buildNewsItems($newsItemsSite);
        } else {
            return [];
        }
    }
}
