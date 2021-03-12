<?php


namespace App\Service;


use App\Entity\DistributionZone;
use App\Repository\DistributionZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Run this piece of code if you want to save the Distribution Zones in database.
 *
 * Class DistributionZonesUploader
 * @package App\Service
 */
class DistributionZonesUploader
{
    public function __construct(
        private EntityManagerInterface $em, 
        private HttpClientInterface $trefleApi,
        private DistributionZoneRepository $repository
    ) { }

    /**
     * The method loops through all urls and gets the distribution zone.
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function uploadToDB()
    {
        $min = 1; //first page
        $max = 37; //last page

        for ($i = $min; $i <= $max; $i++) {
            $response = $this->trefleApi->request('GET', "distributions?page={$i}");

            $body = $response->toArray();

            $data = $body['data'];

            foreach ($data as $item) {
                $existing = $this->repository->find($item['id']);

                if ($existing) {
                    $zone = $existing;
                } else {
                    $zone = new DistributionZone();

                    $zone->setId($item['id'])
                        ->setName($item['name']);

                    $this->em->persist($zone);
                }

                $children = $item['children'];

                if (!empty($children)) {
                    foreach ($children as $child) {
                        $existingChild = $this->repository->find($child['id']);

                        if ($existingChild) {
                            $childZone = $existingChild;
                        } else {
                            $childZone = new DistributionZone();

                            $childZone->setId($child['id'])
                                ->setName($child['name']);

                            $this->em->persist($childZone);
                        }

                        $zone->addChild($childZone);
                    }
                }
            }
        }

        $this->em->flush();
    }
}