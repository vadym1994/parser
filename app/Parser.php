<?php
namespace app;

class Parser
{
    public $start_url;
    public $domain;

    function __construct($start_url)
    {
        $this->start_url = "http://".$start_url;
        $this->domain = $this->getDomain($this->start_url);

        if($this->domain == NULL) {
            echo "Your link is not available.";
            die;
        }
    }

    /**
     * @param $string
     * @return null || string
     */
    public function getDomain($string) {
        $domain = isset(parse_url($string)['host']) ? parse_url($string)['host'] : NULL;
        return $domain;
    }

    /**
     * @param $url
     * @return null || string
     */
    public function getDocument($url) {
        if(filter_var($url, FILTER_VALIDATE_URL)) {
            try {
                $content = file_get_contents($url);
            } catch (Exception $e) {
                $content = NULL;
            }
            return $content;
        }
        return NULL;
    }

    /**
     * @param $url
     * @return array
     */
    public function getLinks($url) {
        $links = [];
        if($this->getDocument($url) !== NULL) {
            preg_match_all('/<a\s+[^>]*?href=\"([^\"@]*)\">(.*)<\/a>/iU',file_get_contents($url), $results);
            foreach ($results[1] as $result) {
                if($this->getDomain($result) == $this->domain || $this->getDomain($result) == NULL) {
                    $path = (parse_url($result)['path'] == '/') ? "" : parse_url($result)['path'];
                    $links[] = 'http://'.$this->domain . $path;
                }
            }
        }
        return array_unique($links);
    }


    /**
     * @param $url
     * @return array
     */
    public function getImages($url) {
        if($this->getDocument($url) !== NULL) {
            preg_match_all('/<img\s+[^>]*?src=("|\')([^"\']+)\1/',$this->getDocument($url), $results);
        }

        if(is_array($results[2])) {
            $images = array_unique($results[2]);
        } else {
            $images = [];
        }
        $full_urls = [];
        foreach ($images as $img) {
            if($this->getDomain($img) == $this->domain || $this->getDomain($img) == NULL) {
                $path = (parse_url($img)['path'] == '/') ? "" : parse_url($img)['path'];
                $full_urls[] = 'http://'.$this->domain . $path;
            }
        }

        return $full_urls;
    }

}
