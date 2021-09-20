<?php

namespace App\Command;

use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadFromCsv extends Command
{
    // the name of the command
    protected static $defaultName = 'app:upload-from-csv';

    protected function configure(): void
    {
       $this
           ->setDescription('Persists the plants info from csv file to db.')
           ->setHelp("The script uses 2 csv files. One for the distribution zones (csv without heading and only name for each row).\n  The other is the species.csv, which is a dump provided by the trefle team.")
           ->addArgument('species', InputArgument::REQUIRED, 'The full path for the species .csv file.')
           ->addArgument('zones', InputArgument::REQUIRED, 'The full path for the zones .csv file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('max_execution_time', 0);

        $pdo = new PDO('mysql:host=localhost:3306;dbname=flora', "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $zones = [];

        if (($handle = fopen($input->getArgument('zones'), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000)) !== FALSE) {
                $zones[$data[0]] = '';
            }
            fclose($handle);
        }

        $plants = [];
        $row = 0;
        if (($handle = fopen($input->getArgument('species'), "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {

                $row++;

                if ($row != 1 && count($data) >= 2) {
                    foreach ($data as $datum) {
                        $hasZone = array_key_exists($datum, $zones);

                        if ($hasZone){
                            $string = explode(chr(9), $data[0]);

                            if (count($string) >= 2) {
                                $plants[$datum][$string[1]] =  [
                                    'name' => $string[1],
                                    'image_url' => isset($string[10]) && filter_var($string[10], FILTER_VALIDATE_URL) ? $string[10] : null,
                                    'common_name' => $string[8] ?? null
                                ];
                            }
                        }
                    }

                }

            }
            fclose($handle);
        }

        ksort($plants);

//foreach ($plants as $key => $value) {
//    echo "<b>$key</b> has <i>". count($value) . "</i> plants <br/>";
//}
//
//echo "<pre>". print_r($plants['Bulgaria'], 1) ."</pre>";

        $progressBar = new ProgressBar($output, count($plants) * 2);

        foreach ($plants as $zone => $list) {
//            echo "Inserting ". count($list) ." plants for zone - $zone ---\n";

            $sql = "SELECT id FROM distribution_zones WHERE distribution_zones.name = '$zone'";
            $stmt = $pdo->query($sql);
            $zoneId = $stmt->fetch();

            if ($zoneId === false) {
                continue;
            } else {
                $zoneId = $zoneId["id"];
            }

            $plantIds = [];

            $sql = "SELECT scientific_name, id FROM plants WHERE plants.scientific_name IN ('" .implode("', '", array_column($list, "name")). "')";
            $stmt = $pdo->query($sql);
            $existingPlants = $stmt->fetchAll();

            foreach ($existingPlants as $existingPlant) {
                $plantIds[] = $existingPlant["id"];
                unset($list[$existingPlant['scientific_name']]);
            }

            foreach ($list as $plant) {
                $row = '(NULL, "'.$plant['name'].'", "'.$plant['common_name'].'", "'. $plant['image_url'].'", NULL, NULL)';

                $sql = "INSERT INTO `plants` (`id`, `scientific_name`, `common_name`, `image_url`, `description`, `model_path`) VALUES ". $row;
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                $plantIds[] = $pdo->lastInsertId();
            }
            $progressBar->advance();

            $rows = [];
            foreach ($plantIds as $id) {
                $rows[] = "(NULL, '$zoneId', '$id')";

                if (count($rows) > 500) {
                    $sql = "INSERT INTO `distribution_zone_plant` (`id`, `distribution_zone_id`, `plant_id`) VALUES ". implode(",", $rows);
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();

                    $rows = [];
                }
            }

            if (!empty($rows)) {
                $sql = "INSERT INTO `distribution_zone_plant` (`id`, `distribution_zone_id`, `plant_id`) VALUES ". implode(",", $rows);
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
            }

            $progressBar->advance();
//            echo "Finished inserting plants for zone<h3>$zone</h3> ---\n";
        }

        $sql = "UPDATE `distribution_zones` SET `fully_fetched` = '1'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $progressBar->finish();

        return Command::SUCCESS;
    }
}