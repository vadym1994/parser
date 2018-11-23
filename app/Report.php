<?php
namespace app;

class Report
{
    public $domain;
    public $reports_store = "public/reports.json";

    function __construct($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param $file_name
     */
    public function saveReport($file_name) {
        //получаем данные из файла отчетов и декодируем в массив
        $reports = json_decode(file_get_contents($this->reports_store),true);
        //добавляем данные новый файл отчета
        $reports[$this->domain][] = $file_name;
        file_put_contents($this->reports_store,json_encode($reports));
    }

    /**
     * @return array
     */
    public function getReports() {
        $result = [];
        //получаем данные из файла отчетов и декодируем в массив
        $reports = json_decode(file_get_contents($this->reports_store),true);
        //если есть данные по ключу (домен) - пишем их в переменную result
        if(isset($reports[$this->domain])) $result = $reports[$this->domain];
        return $result;
    }
}
