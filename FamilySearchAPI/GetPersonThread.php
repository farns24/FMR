<?php
require_once('pthread/Objects.php');

class GetPersonThread extends Thread {
    public $url;
    public $data;

    public function __construct($url) {
        $this->url = $url;
    }

    public function run() {
        if (($url = $this->url)) {
            /*
             * If a large amount of data is being requested, you might want to
             * fsockopen and read using usleep in between reads
             */
            $this->data = file_get_contents($url);
        } else
            printf("Thread #%lu was not provided a URL\n", $this->getThreadId());
    }
}

?>