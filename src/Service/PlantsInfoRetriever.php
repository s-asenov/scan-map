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

class PlantsInfoRetriever
{
    private $wikiApi;
    private $em;

    public function __construct(HttpClientInterface $wikiApi, EntityManagerInterface $em)
    {
        $this->wikiApi = $wikiApi;
        $this->em = $em;
    }

    /**
     * @param Plant[] $plants
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getInfo(array $plants): array
    {
        $batchSize = 1000;
        $count = 0;

        $plantsChunks = array_chunk($plants, 50, true);

        foreach ($plantsChunks as $chunk) {
            $names = implode("|", array_column($chunk, 'scientificName'));

            $response = $this->wikiApi->request('GET', "?titles={$names}&prop=extracts&exintro&explaintext");

            $data = $response->toArray();

            $pages = array_values($data['query']['pages']); //changing keys 0, 1, 2
//            $normalized = $data['query']['normalized'];

            $count += count($pages);

            foreach ($pages as $key => $page) {
                if (isset($data['query']['normalized'])) {
                    $initName = $data['query']['normalized'][$key]['from'];
                } else {
                    $initName = $page['title'];
                }
//                $initName = $normalized[$key]['from'];

                if (!isset($page['pageid']) || $page['pageid'] < 0 || !isset($page['extract'])) {
                    $info = "";
                } else {
                    $info = $this->removeLineBreaks($page['extract']);
                }

                /**
                 * @var Plant
                 */
                $plant = $plants[$initName];
                $plant->setInformation($info);

                $this->em->persist($plant);

                if ($count % $batchSize === 0) {
                    $this->em->flush();
                    $this->em->clear();
                }
            }
        }

        $this->em->flush();
        $this->em->clear();

        return $plants;
    }


    private function removeLineBreaks(string $string): string
    {
        return trim(preg_replace('/\s+/', ' ', $string));
    }
}