<?php 
    require 'vendor/autoload.php';

    use Mh\Wlogic\PackageScraper;

    //load class
    $package_scraper = new PackageScraper();
    
    //get the packages 
    $package_scraper->getWLTestPackages();
    
    //sort the packages
    $package_scraper->sortPackagesByAnnualPrice();
    
    //output the packages
    $package_scraper->saveToFile('output.json');
?>