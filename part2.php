<?php

include 'functions.php';

$filename = './inputs/puzzle_input.txt';

$input = Core::get_input($filename);

$grid = Core::make_grid($input);

$part1 = Part1::identify_monitoring_station_location($grid);

$station_location = $part1['best_locations'][0];

$asteroid_to_stop_at = 200;

$part2 = Part2::unleash_the_laser($station_location, $asteroid_to_stop_at);

$prime = Part2::prime_the_laser($station_location, $asteroid_to_stop_at);

// Set the counter to 0
$asteroid_number = 0;

$asteroid_order = [];

// While there are asteroids left in the array continue
while (!empty($prime)) {

    // Loop through each angle
    foreach ($prime as $angle => $asteroids) {
        // increase the counter by 1
        $asteroid_number++;
        // Get the closest asteroid for the angle and remove it from the array
        $asteroid = array_shift($asteroids);
        $asteroid_order[$asteroid['location']] = $asteroid_number;
        $prime[$angle] = $asteroids;

        // Check if the angle has any asteroids left, if not remove it from the $asteroids_by_angle array so we no longer need to consider it
        if (empty($asteroids)) {
            unset($prime[$angle]);

        }
    }
}

$part2_answer = $part1['max_asteroids'];
$best_location = $part1['best_locations'][0];

$count_th_number = 0;
$visible_th_number = 0;

include './views/part2.html';

?>
