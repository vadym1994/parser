<?php
require __DIR__ . '/vendor/autoload.php';

//если не введена команда
if(!isset($argv[1])) {
    echo "You must enter a command.\n"
        ."For getting list of available methods run \"script_name help\"\n";
    exit();
}
//проверяем команды
switch ($argv[1]) {
    case 'parse':
        //если парамметр не был введен
        if(!isset($argv[2])) {
            echo "You must enter a domain.\nCommand \"parse domain.com\"\n";
            exit();
        }
        $url = str_replace(['http://','https://'],['',''], $argv[2]);

        $parser = new App\Parser($url);
        echo "Processing...\nPlease, wait. It's may take a few minutes.\n";
        //собираем все ссылки на всех страницах (начиная со стартовой)
        $all_links = [$parser->start_url];
        /*
         * запускаем цикл, в котором постепенно дополняем массив ссылок, которые нужно обработать
         * проходимся по всем ссылкам и
         */
        $report_file_name = date('d-m-Y_H-i-s')."_".str_replace(".","_",$parser->domain).".csv";
        $storage = new App\Storage($report_file_name);

        for ($i = 0; $i < count($all_links); $i++) {
            //получаем ссылки со страницы
            $new_links = $parser->getLinks($all_links[$i]);
            //сливаем старые ссылки и новые, удаляя совпадения и сдвигая индексы
            $all_links = array_values(array_unique(array_merge($all_links,$new_links)));
            //получаем картинки с текщей страницы
            $images = $parser->getImages($all_links[$i]);
            //пишем ссылки на картинки в csv
            $storage->putDataIntoCSV($all_links[$i],$images);
        }
        //сохраняем в json файл отчет по домену
        $report = new App\Report($parser->domain);
        $report->saveReport($storage->file_name);

        echo "Done! Pages processed: ".count($all_links)."\n";
        echo "Report file: ".__DIR__."\\storage\\".$storage->file_name."\n\n";
        break;

    case 'help':
        echo "Commands list:\n\n"
            ."help\n"
            ."parse <domain>\n"
            ."report <domain>\n";
        break;

    case 'report':
        //если парамметр не был введен
        if(!isset($argv[2])) {
            echo "You must enter a domain.\nCommand \"report domain.com\"\n";
            exit();
        }
        //присваеваем переменной значение парамметра отвечающего за домен и избавляемся от протокола
        $domain = str_replace(['http://','https://'],['',''], $argv[2]);
        //получаем список файлов с отчетами по данному домену
        $report = new App\Report($domain);
        $report_files = $report->getReports();
        if(count($report_files) == 0) {
            echo "Report for \"".$domain."\" is empty!";
            exit();
        }
        //выводим отчет по последнему файлу
        echo "Report for \"".$domain."\"\n";
        $storage = new App\Storage(array_pop($report_files));
        echo $storage->getDataFromCSV();
        break;

    default:
        echo "Undefined command.\n"
        ."For getting list of available methods run \"script_name help\"\n";
        break;
}
