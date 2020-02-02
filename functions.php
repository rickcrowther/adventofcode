<?php

/**
 * Rick Crowther
 * Advent of Code 2019 - Day 10
 * https://adventofcode.com/2019/day/10
 */

class Core {

    /**
     * @param string $filename
     *
     * @return array
     */

    public function get_input($filename){
        // Grab the file contents - Change this here to change the file to test
        // Convert to an array of lines
        $input = explode("\n", trim(file_get_contents($filename)));

        return $input;
    }

    /**
     * @param array $input
     *
     * @return array
     */

    public function make_grid($input){
        // Create empty arrays so we can map the input into a set of X and Y Coordinates that can be accessed by Key
        $grid = [];
        $grid['grid'] = [];
        $asteroids = [];

        // Define the grid size of the input
        $width = strlen($input[0]); // I've assumed each row is the same size as it's a grid
        $height = count($input);

        $grid['width'] = $width;
        $grid['height'] = $height;

        // Define the grid by looping through the input row by row, column by column
        foreach ($input as $x => $row) {
            // Each Row is a string, lets convert to an array so we can then loop through each column
            foreach (str_split($row) as $y => $column) {
                // Set the content of each square in the grid
                $grid['grid'][$x][$y] = $column;
                // Identify where all the asteroids are in one clean array
                if($column === '#'){
                    $asteroids[$x][$y] = $column;
                }
            }
        }

        $grid['asteroids'] = $asteroids;

        return $grid;

    }

    /**
     * return information about the asteroid with the given co-ordinates for the given grid
     *
     * @param int $x
     * @param int $y
     * @param array $grid
     *
     * @return array
     */


    public function get_asteroid_info($x, $y, $grid){
        // We've now identified an asteroid in the array - we need to determine the angle each other asteroid is at relative to the source
        // Did some digging on how to work this out and found this....
        // https://math.stackexchange.com/questions/1201337/finding-the-angle-between-two-points
        // Turns out there is a PHP function for atan2 which can be used to get the angle :)
        // Going to also convert this value to a 360 degree version (Added to deal with Part2)
        // I also want to identify which Asteroids that asteroid can see rather than just the number so I am going to calculate the distance between the source asteroid and the asteroid we are looking at. Here's the guide I followed on how to do that:
        // https://www.wikihow.com/Find-the-Distance-Between-Two-Points
        $asteroid_visible_asteroids = [];
        $asteroid_all_asteroids = [];
        $angles_unique = [];
        $distances = [];

        // Loop through the asteroid array again and do a check to see if the asteroid in question can see it or not
        foreach($grid['asteroids'] as $x2 => $row2){
            foreach($row2 as $y2 => $column2){
                // We've now identified an asteroid to check against - can the source asteroid see it?
                // First ignore it if the asteroid has the same keys as the source asteroid
                if ($x === $x2 && $y2 === $y) continue;
                // Set the angle as the key in the $angles_unique array, if it has already been set it will just set it again - This will provide us with a unique array of all the different angles
                $angle = (string) atan2($y - $y2, $x2 - $x);
                $angles_unique[$angle] = true;
                // For Part2 ...
                // If we are rotating clockwise we start at pi and then go negative until we get back to Pi
                // Problem is it will go Pi then a negative number so sorting will be really difficult :(
                // need to convert my atan2 angle to 0 to 360 degrees
                $angle_as_degrees = rad2deg($angle + M_PI);
                // If the angle is 360 set it to 0 - this is so when we rotate in Part 2 we start with a 0
                if($angle_as_degrees >= 360){
                    $angle_as_degrees = 0;
                }
                // Define the distance the asteroid is from the source asteroid - I'm using the formula from here: https://www.wikihow.com/Find-the-Distance-Between-Two-Points
                $distance = sqrt(pow(($x2 - $x), 2) + pow(($y2 - $y), 2));
                $distances[$x2."_".$y2] = $distance;
                // Determine what asteroids are visible to the source asteroid - We only want to store the closest asteroid for the specified angle
                if(isset($asteroid_visible_asteroids[$angle])){
                    if($distances[$asteroid_visible_asteroids[$angle]] > $distances[$x2."_".$y2]){
                        $asteroid_visible_asteroids[$angle] = $x2."_".$y2;
                    }
                } else {
                    $asteroid_visible_asteroids[$angle] = $x2."_".$y2;
                }
                $asteroid_all_asteroids[$y2."_".$x2]['location'] = $y2."_".$x2;
                $asteroid_all_asteroids[$y2."_".$x2]['angle'] = $angle;
                $asteroid_all_asteroids[$y2."_".$x2]['angle_as_degrees'] = $angle_as_degrees;
                $asteroid_all_asteroids[$y2."_".$x2]['distance'] = $distance;
            }
        }

        return [
            'location' => ['x' => $x, 'y' => $y],
            'angles' => $angles_unique,
            'count' => count($asteroid_visible_asteroids),
            'all_asteroids' => $asteroid_all_asteroids,
            'visible_asteroids' => $asteroid_visible_asteroids,
        ];


    }

}


class Part1 {

    /**
     * @param array $input
     *
     * @return array
     */

    function identify_monitoring_station_location($grid){

        $asteroid_counts = [];
        $asteroid_angles = [];
        $asteroid_info = [];

        // Go through each Asteroid location and get the information on each one...
        foreach($grid['asteroids'] as $x => $row){
            foreach($row as $y => $column){
                $asteroid_info[$y . "_" . $x] = Core::get_asteroid_info($x, $y, $grid);
                $asteroid_angles[$y . "_" . $x] = $asteroid_info[$y . "_" . $x]['angles'];
                $asteroid_counts[$y . "_" . $x] = $asteroid_info[$y . "_" . $x]['count'];
            }
        }

        // The max asteroids visible is simply the max value of count in the $asteroid_info array
        $max_asteroids = max(array_column($asteroid_info, 'count'));
        // Identify the asteroid(s) where the max value happens
        $best_locations = [];
        foreach ($asteroid_info as $key => $val) {
            if ($val['count'] === $max_asteroids) {
                $best_locations[] = $asteroid_info[$key];
            }
        }

        return [
            'best_locations' => $best_locations,
            'max_asteroids' => $max_asteroids,
            'angles' => $asteroid_angles,
            'counts' => $asteroid_counts,
            'asteroid_info' => $asteroid_info,
        ];

    }

}

class Part2 {

    /**
     * @param array $monitoring_station_info
     *
     * @return array
     */

    function prime_the_laser($monitoring_station_info){

        // Set a blank array to populate
        $asteroids_to_blow_up = [];

        // Loop through the all_asteroids array for the monitoring station
        foreach($monitoring_station_info['all_asteroids'] as $asteroid){
            $asteroids_to_blow_up[(string)$asteroid['angle_as_degrees']][(string)$asteroid['distance']] = $asteroid;
        }

        // Sort the array by key to get them in angle order with 0 first
        ksort($asteroids_to_blow_up);

        // Create a new array so we can then sort each angle by distance so the closest is first
        $asteroids_by_angle = [];

        // Loop through the $asteroids_to_blow_up array
        foreach($asteroids_to_blow_up as $k => $angle){
            // sort each angle's asteroids into distance order
            ksort($angle);

            // Add as a new record in the $asteroids_by_angle array
            $asteroids_by_angle[$k] = $angle;
        }

        return $asteroids_by_angle;
    }

    /**
     * @param array $monitoring_station_info
     * @param int $asteroid_to_stop_at
     *
     * @return int
     */

    function unleash_the_laser($monitoring_station_info, $asteroid_to_stop_at = null){

        $asteroids_by_angle = Self::prime_the_laser($monitoring_station_info);

        // Asteroid to stop at - Default to the last asteroid is not set
        if($asteroid_to_stop_at == null){
            $asteroid_to_stop_at = sizeof($monitoring_station_info['all_asteroids']);
        }

        // Set the counter to 0
        $asteroid_number = 0;

        // While there are asteroids left in the array continue
        while (!empty($asteroids_by_angle)) {

            // Loop through each angle
            foreach ($asteroids_by_angle as $angle => $asteroids) {
                // increase the counter by 1
                $asteroid_number++;
                // Get the closest asteroid for the angle and remove it from the array
                $asteroid = array_shift($asteroids);
                $asteroids_by_angle[$angle] = $asteroids;

                // Check if the asteroid number matches the number to stop at
                if ($asteroid_number === $asteroid_to_stop_at) {
                    $co_ordinates = explode('_', $asteroid['location']);
                    // Return the required response for the answer to Part2 (X co-ordinate times 100 plus Y co-ordinate)
                    return ($co_ordinates[0] * 100) + $co_ordinates[1];
                }
                // Check if the angle has any asteroids left, if not remove it from the $asteroids_by_angle array so we no longer need to consider it
                if (empty($asteroids)) {
                    unset($asteroids_by_angle[$angle]);

                }
            }
        }


    }
}

?>
