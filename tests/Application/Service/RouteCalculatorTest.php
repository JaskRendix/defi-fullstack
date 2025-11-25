<?php

namespace Tests\Application\Service;

use PHPUnit\Framework\TestCase;
use App\Application\Service\RouteCalculator;
use App\Infrastructure\DataLoader\RailDataLoader;

// NOTE: We rely on the mock data in distances.json and stations.json
// being present in the root directory for this test to run.

class RouteCalculatorTest extends TestCase
{
    private RouteCalculator $calculator;

    protected function setUp(): void
    {
        // Setup the dependency: RailDataLoader
        $dataLoader = new RailDataLoader();
        
        // Load data before running tests
        $dataLoader->loadData(); 
        
        $this->calculator = new RouteCalculator($dataLoader);
    }

    /**
     * Test case 1: Standard shortest path on the main line.
     */
    public function testFindShortestRoute_SimplePath(): void
    {
        $route = $this->calculator->findShortestRoute('MX', 'SCH', 'PASSENGER');
        
        // Expected Path: MX -> CGE -> ZW -> SCH
        $this->assertEquals(0.65 + 1.20 + 0.50, $route->distanceKm, 0.001, 'Distance should match direct path.');
        $this->assertEquals(['MX', 'CGE', 'ZW', 'SCH'], $route->path, 'Path should follow the main route.');
    }

    /**
     * Test case 2: Path with a choice (MX -> BR is longer than MX -> CGE)
     */
    public function testFindShortestRoute_ChoosesShortestPath(): void
    {
        // Goal: MX -> ZW. 
        // Path 1 (via CGE): 0.65 + 1.20 = 1.85 km
        // Path 2 (via BR, assuming connection exists to ZW, but it doesn't in mock data, let's test MX -> IND instead)
        $route = $this->calculator->findShortestRoute('MX', 'IND', 'FREIGHT');

        // Expected Path: MX -> BR -> IND (1.50 + 2.10 = 3.60 km)
        $this->assertEquals(3.60, $route->distanceKm, 0.001, 'Distance should be 3.60 km via BR.');
        $this->assertEquals(['MX', 'BR', 'IND'], $route->path, 'Path should go through BR to IND.');
    }

    /**
     * Test case 3: Route between two stations on an isolated line.
     */
    public function testFindShortestRoute_IsolatedLine(): void
    {
        $route = $this->calculator->findShortestRoute('ISO', 'END', 'MAINTENANCE');
        
        // Expected Path: ISO -> END (5.0 km)
        $this->assertEquals(5.0, $route->distanceKm, 0.001, 'Distance should be 5.0 km on isolated line.');
        $this->assertEquals(['ISO', 'END'], $route->path, 'Path should be direct on isolated line.');
    }

    /**
     * Test case 4: No route exists between two separate parts of the network.
     */
    public function testFindShortestRoute_NoPath(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Route not found');
        
        // Attempt to go from the main network (MX) to the isolated network (END)
        $this->calculator->findShortestRoute('MX', 'END', 'PASSENGER');
    }
    
    /**
     * Test case 5: Start and end station are the same.
     */
    public function testFindShortestRoute_StartEqualsEnd(): void
    {
        $route = $this->calculator->findShortestRoute('MX', 'MX', 'PASSENGER');
        
        $this->assertEquals(0.0, $route->distanceKm, 'Distance must be zero.');
        $this->assertEquals(['MX'], $route->path, 'Path should only contain the start/end station.');
    }

    /**
     * Test case 6: Invalid station ID provided.
     */
    public function testFindShortestRoute_InvalidStation(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Route not found');
        
        // "XYZ" is not in stations.json
        $this->calculator->findShortestRoute('MX', 'XYZ', 'PASSENGER');
    }
}
