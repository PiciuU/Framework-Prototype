<?php

namespace Services\Scraper;

class CURL {

    protected $HTTP_USER_AGENT = null;
    protected $CURL_OPTIONS = array();
    protected $CURL_RESULT = null;
    protected $COOKIES = null;

    protected $DOCUMENT = null;
    protected $XPATH = null;

    public function __construct($url = null) {
        $this->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
        $this->CURL_OPTIONS = array(
            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           => false,        //set to GET
            CURLOPT_USERAGENT      => $this->HTTP_USER_AGENT, //set user agent
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => true,    // don't return headers
            CURLOPT_FOLLOWLOCATION => false,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 0,       // stop after 10 redirects)
        );

        if ($url) $this->fetchWebsite($url);
    }

    public function fetchWebsite($url) {
        $curl_connection = curl_init($url);
        curl_setopt_array($curl_connection, $this->CURL_OPTIONS);
        if ($this->COOKIES != null) curl_setopt($curl_connection, CURLOPT_COOKIE, $this->COOKIES);
        $this->CURL_RESULT = curl_exec($curl_connection);
        curl_close($curl_connection);

        if ($this->COOKIES == null) $this->injectCookies();
        $this->setDocument();
    }

    public function setDocument() {
        $this->DOCUMENT = new \DomDocument('1.0', 'UTF-8');
        $this->DOCUMENT->loadHTML($this->CURL_RESULT, LIBXML_NOERROR);
        $this->XPATH = new \DOMXPath($this->DOCUMENT);
    }

    public function getResult() {
        return $this->CURL_RESULT;
    }

    public function getCookies() {
        return $this->COOKIES;
    }

    public function getDocument() {
        return $this->DOCUMENT;
    }

    public function getXPath() {
        return $this->XPATH;
    }
}
