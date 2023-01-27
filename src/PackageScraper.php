<?php 

namespace Mh\Wlogic;

class PackageScraper {

    public $package_details = [];
    public $httpClient;

    function __construct(){
        $this->httpClient = new \GuzzleHttp\Client();
    }

    function getWLTestPackages()
    {
        //get page dom as xpath;
        $response = $this->httpClient->get('https://wltest.dns-systems.net/');
        $htmlString = (string) $response->getBody();
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new \DOMXPath($doc);

        //store info 
        $packageNames = $xpath->evaluate('//div[@class="package-features"]//ul//li//div[@class="package-name"]');
        $packageDescriptions = $xpath->evaluate('//div[@class="package-features"]//ul//li//div[@class="package-description"]');
        $packagePriceTexts = $xpath->evaluate('//div[@class="package-features"]//ul//li//div[@class="package-price"]');
        $packagePrices = $xpath->evaluate('//div[@class="package-features"]//ul//li//div[@class="package-price"]//span[@class="price-big"]');
        $packageDiscounts = $xpath->evaluate('//div[@class="package-features"]//ul//li//div[@class="package-price"]//p');

        //loop package names
        foreach($packageNames as $key => $packageName){

            //add data to temp array
            $price_in_pennies = (float) substr($packagePrices[$key]->textContent, 2) * 100;
            $new_package_details = [
                "option_title" => $packageName->textContent,
                "description" => $packageDescriptions[$key]->textContent,
                "discount" => isset($packageDiscounts[$key]) ? $packageDiscounts[$key]->textContent : null,
                "price" => $packagePrices[$key]->textContent,
            ];

            //add annual price key for sorting
            $new_package_details["annual_price_in_pennies"] = !str_contains($packagePriceTexts[$key]->textContent, "Year") ? 
                $price_in_pennies * 12 : $price_in_pennies;
            
            //add to packageDetails array
            array_push($this->package_details, $new_package_details);
        }
    }
    
    function sortPackagesByAnnualPrice()
    { 
        //sort packages by annual price;
        usort($this->package_details, function($a, $b){
            return $b["annual_price_in_pennies"] - $a["annual_price_in_pennies"];
        });
    }

    function saveToFile($file_name)
    {
        //populate output.json file
        file_put_contents($file_name, json_encode($this->package_details, JSON_PRETTY_PRINT));
        echo PHP_EOL."Packages saved to $file_name".PHP_EOL.PHP_EOL;
    }
}

?>