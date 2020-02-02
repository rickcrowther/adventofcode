<?php

include 'functions.php';

$filename = './inputs/puzzle_input.txt';

$input = Core::get_input($filename);

$grid = Core::make_grid($input);

$part1 = Part1::identify_monitoring_station_location($grid);

$part1_answer = $part1['max_asteroids'];
$best_location = $part1['best_locations'][0];

$count_th_number = 0;
$visible_th_number = 0;

include './views/part1.html';

?>
