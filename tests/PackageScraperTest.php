<?php
    use Mh\Wlogic\PackageScraper;
    use PHPUnit\Framework\TestCase;
    
    class PackageScraperTest extends TestCase
    {
        public function testGetWLTestPackagesReturnsPackageCount()
        {
            $package_scrapper = new PackageScraper();
            
            //get packages
            $package_scrapper->getWLTestPackages();
            
            //check array has 6 packages
            $this->assertEquals(count($package_scrapper->package_details), 6);
        }

        public function testSortPackagesByAnnualPrice()
        {
            $package_scrapper = new PackageScraper();
            
            //get packages
            $package_scrapper->getWLTestPackages();            
            
            //sort packages by annual price
            $package_scrapper->sortPackagesByAnnualPrice();

            //get annual_prices_descending
            $max_to_min_annual_prices = array_column($package_scrapper->package_details, 'annual_price_in_pennies');

            //check if 1st package is the most expensive;
            $this->assertEquals($package_scrapper->package_details[0]['annual_price_in_pennies'], $max_to_min_annual_prices[0]);
        }

        public function testOutputFileSaved()
        {
            $package_scrapper = new PackageScraper();
            
            //get packages
            $package_scrapper->getWLTestPackages();            
            
            //sort packages by annual price
            $package_scrapper->sortPackagesByAnnualPrice();

            //output file
            $package_scrapper->saveToFile("test_output.json");

            //check file exists
            $this->assertFileExists("test_output.json");
        }
    }
?>