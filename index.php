<?php
header('Content-type: text/html; charset=utf-8');
require_once "lib/simple_html_dom.php";
require_once "helper.php";

//create an array where we'll save data
$productArr = array();
$comments = array();

// TODO: we need all products links from https://www.amazon.com/gp/bestsellers
$linksArr = array();

$data = curl_get('https://www.amazon.com/gp/bestsellers');

$dom = str_get_html($data);

$elements = $dom->find('#zg_left_col1');

foreach ($elements as $element) {
  $categories = $element->find('.zg_homeWidget');

  foreach ($categories as $category){
    $title = $category->find('h3', 0)->plaintext;

    //skip 'Gift Cards'
    if ($title == "Gift Cards") {
      continue;
    }

    //get the product block
    $products = $category->find('.zg_homeWidgetItem');

    // get the link
    foreach($products as $product){
      $link = $product->find('a.a-link-normal',1);

      if($link != null){
        $link = "https://amazon.com/" . $link->href;
        $linksArr[] = $link;
      }
    }
  }
}

// parse the comments
for($i=0; $i<6; $i++){

  $url = $linksArr[$i];

  $content = curl_get($url);

  $dom = str_get_html($content);

  $elements = $dom->find('.view-point');
  foreach ($elements as $element) {

    // get the positive comment
    $positive = $element->find('.positive-review');

      foreach ($positive as $item ) {
      $name = $item->find('span.a-profile-name',0)->plaintext;
      $date = $item->find('span.review-date',0)->plaintext;
      $text = $item->find('.a-spacing-top-mini',0)->plaintext;
      $link = $item->find('a.a-size-base',0);

      $text =  str_replace(array("\r\n", "\r", "\n"), '',  strip_tags($text));
      $link = "http://www.amazon.com". $link->href;

      $arr = compact('name', 'date', 'text', 'link');

      if(!in_array($arr, $comments)){
        $comments['positive']= $arr;
      }
      
    }

    // get the critical comment
    $critical = $element->find('.critical-review');
    foreach ($critical as $item ) {
      $name = $item->find('span.a-profile-name',0)->plaintext;
      $date = $item->find('span.review-date',0)->plaintext;
      $text = $item->find('.a-spacing-top-mini',0)->plaintext;
      $link = $item->find('a.a-size-base',0);

      $text =  str_replace(array("\r\n", "\r", "\n"), '',  strip_tags($text));
      $link = "http://www.amazon.com". $link->href;

      $arr = compact('name', 'date', 'text', 'link');

      if(!in_array($arr, $comments)){
        $comments['critical']= $arr;
      }
    }

    // get the title of product
    $title = $dom->find('.a-fixed-left-grid-inner');

    $item = $title[0]; //instead foreach
      $a = $item->find('.product-title',0);

      $compare = $a->plaintext;
      
      if(!array_key_exists($compare, $product)){
        $productArr["$compare"] = $comments;
      }
    
  }

}

echo "<pre>";
print_r($productArr);
echo "</pre>";
