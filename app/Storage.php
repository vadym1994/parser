<?php
namespace app;

class Storage
{
    public $file_name;
    public $storage_path = "storage/";

    public function __construct($filename = NULL)
    {
        $this->file_name = $filename;
    }

    /**
     * @return string
     */
    public function getDataFromCSV() {
        $row = 1;
        $report_text = "";

        if (($handle = fopen($this->storage_path.$this->file_name, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                for ($i = 0; $i < count($data); $i++) {
                    $exploded_row = explode(';',$data[$i]);
                    $report_text .= "\nPage:  ".$exploded_row[0]."\nImage: ".$exploded_row[1]."\n";
                }
            }
            fclose($handle);
        }
        return $report_text;
    }

    /**
     * @param $page
     * @param $image_links
     * @return true|false
     */
    public function putDataIntoCSV($page, $image_links) {
        if(!is_array($image_links)) return false;
        //открываем файл
        $opened_file = fopen($this->storage_path.$this->file_name, "a");
        //пишем данные построково
        foreach ($image_links as $image_link) {
            fputcsv($opened_file, [$page,$image_link], ";");
        }
        //закрываем файл
        fclose($opened_file);
        return true;
    }

}