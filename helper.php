<?php

function curl_get($url){

  //create a few different referer array to avoid captcha
  $refArray = [
    "http://www.google.com",
    "http://www.yahoo.com",
    "http://www.yandex.ru",
    "http://www.bing.com",
    "https://duckduckgo.com/",
    "https://www.dogpile.com/"
  ];

  //get random referer
  $i = rand(0, 5);
  $referer = $refArray[$i];
  
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_REFERER, $referer);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_TIMEOUT, 40);
  curl_setopt($ch, CURLOPT_ENCODING ,"");

  $content = curl_exec($ch);
  curl_close($ch);

  return $content;

}