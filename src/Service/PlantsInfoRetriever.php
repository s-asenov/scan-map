<?php


namespace App\Service;


use App\Entity\Plant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class PlantsInfoRetriever
 *
 * The class responsible for adding information in the plant entity.
 * Call the wikipedia api and parses the necessary data.
 *
 * @package App\Service
 */
class PlantsInfoRetriever
{
    const WIKI_EXTRACTS_LIMIT = 20;
    private $wikiApi;
    private $em;

    public function __construct(HttpClientInterface $wikiApi, EntityManagerInterface $em)
    {
        $this->wikiApi = $wikiApi;
        $this->em = $em;
    }

    /**
     * @param array $pl all plants from zone
     * @return array of all plants with update information
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getInfo(array $pl): array
    {
        /*
         * Filter an array of plants for which we need to get the data.
         */
        $filter = function ($plant)
        {
            return $plant !== null && $plant->getDescription() === null;
        };

        $plants = array_filter($pl, $filter);

        /**
         * Split the array into chunks to match the wikipedia api limit.
         */
        $plantsChunks = array_chunk($plants, self::WIKI_EXTRACTS_LIMIT, true);

        foreach ($plantsChunks as $chunk) {
            /*
             * Wiki api separates the different pages by |
             */
            $names = implode("|", array_keys($chunk));

            $request = $this->wikiApi->request('GET', "?titles={$names}&prop=extracts& exintro&explaintext");

            $data = $request->toArray();

            $pages = array_values($data['query']['pages']); //changing keys 0, 1, 2

            foreach ($pages as $key => $page) {
                /*
                 * Check if wikipedia api changes the name and get the new one if there is one.
                 */
                if (isset($data['query']['normalized'])) {
                    $initName = $data['query']['normalized'][$key]['from'];
                } else {
                    $initName = $page['title'];
                }

                /*
                 * Check if the page exists and there is information,
                 * otherwise set the info to empty string showing that the plant has already been searched.
                 */
                if (!isset($page['pageid']) || $page['pageid'] < 0 || !isset($page['extract'])) {
                    $info = "";
                } else {
                    $info = $this->removeLineBreaks($page['extract']);
                }

//                /**
//                 * @var Plant
//                 */
//                $plant = $plants[$initName];
//                $plant->setInformation($info);
            }
        }

        return $plants + $pl;
    }


    private function removeLineBreaks(string $string): string
    {
        return trim(preg_replace('/\s+/', ' ', $string));
    }

    public function getInfoOfPlant(Plant $plant): string
    {
        $name = $plant->getScientificName();

        $request = $this->wikiApi->request('GET', "?titles={$name}&prop=extracts&exintro&explaintext");

        $data = $request->toArray();

        $page = array_values($data['query']['pages'])[0];

        if (!isset($page['pageid']) || $page['pageid'] < 0 || !isset($page['extract'])) {
            $info = "";
        } else {
            $info = $this->removeLineBreaks($page['extract']);
        }

        return $info;
    }
}