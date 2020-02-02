<?php

/**
 * Rick Crowther
 * Advent of Code 2019 - Day 10
 * https://adventofcode.com/2019/day/10
 *
 * This file is what I used to check my functions worked as expected
 *
 */

include 'functions.php';

$filename = './inputs/puzzle_input.txt';

$input = Core::get_input($filename);

$grid = Core::make_grid($input);

$part1 = Part1::identify_monitoring_station_location($grid);

$asteroid_to_stop_at = 200;


$prime = Part2::prime_the_laser($part1['best_locations'][0],$asteroid_to_stop_at);

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

$unleash = Part2::unleash_the_laser($part1['best_locations'][0],$asteroid_to_stop_at);

$grid_check_th_number = 0;
$angles_check_th_number = 0;
$count_check_th_number = 0;
$info_check_th_number = 0;
$part2_check_th_number = 0;

?>

<html>
    <head>
        <title>Rick Crowther - Advent of Code - Day 10 - ScratchPad</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>
    <body>
        <h1 class="mb-4">Part 1</h1>
        <div class="container-fluid mb-6">
            <h2 class="mb-4">Grid Check</h2>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <?php while($grid_check_th_number < $grid['width']){
                        echo '<th>'.$grid_check_th_number.'</th>';
                        $grid_check_th_number++;
                    } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($grid['grid'] as $x => $row) {
                    echo '<tr>';
                    echo '<td>'.$x.'</td>';
                    foreach ($row as $y => $column) {
                        echo '<td>'.$column.'</td>';
                    }
                    echo '</tr>';
                } ?>
                </tbody>
            </table>
        </div>
        <div class="container-fluid mb-6">
            <h2 class="mb-4">Angles Check</h2>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <?php while($angles_check_th_number < $grid['width']){
                        echo '<th>'.$angles_check_th_number.'</th>';
                        $angles_check_th_number++;
                    } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($grid['grid'] as $x => $row) {
                    echo '<tr>';
                    echo '<td>'.$x.'</td>';
                    foreach ($row as $y => $column) {
                        if(isset($part1['angles'][$y . "_" . $x])){
                            echo '<td>';
                            foreach($part1['angles'][$y . "_" . $x] as $angle => $k){
                                echo '<p>'. (string)$angle . '</p>';
                            }
                            echo '</td>';
                        } else {
                            echo '<td>'.$column.'</td>';
                        }
                    }
                    echo '</tr>';
                } ?>
                </tbody>
            </table>
        </div>
        <div class="container-fluid mb-6">
            <h2 class="mb-4">Count Check</h2>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <?php while($count_check_th_number < $grid['width']){
                        echo '<th>'.$count_check_th_number.'</th>';
                        $count_check_th_number++;
                    } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($grid['grid'] as $x => $row) {
                    echo '<tr>';
                    echo '<td>'.$x.'</td>';
                    foreach ($row as $y => $column) {
                        if(isset($part1['asteroid_info'][$y . "_" . $x])){
                            echo '<td>'.$part1['asteroid_info'][$y . "_" . $x]['count'].'</td>';
                        } else {
                            echo '<td>'.$column.'</td>';
                        }
                    }
                    echo '</tr>';
                } ?>
                </tbody>
            </table>
        </div>
        <div class="container-fluid mb-6">
            <h2 class="mb-4">info Check</h2>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <?php while($info_check_th_number < $grid['width']){
                        echo '<th>'.$info_check_th_number.'</th>';
                        $info_check_th_number++;
                    } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($grid['grid'] as $x => $row) {
                    echo '<tr>';
                    echo '<td>'.$x.'</td>';
                    foreach ($row as $y => $column) {
                        if(isset($part1['$asteroid_info'][$y . "_" . $x])){
                            echo '<td>';
                            foreach($part1['$asteroid_info'][$y . "_" . $x]['angles'] as $angle => $k){
                                echo '<p>'. (string)$angle . '</p>';
                            }
                            echo '</td>';
                        } else {
                            echo '<td>'.$column.'</td>';
                        }
                    }
                    echo '</tr>';
                } ?>
                </tbody>
            </table>
            <h4>Max Asteroid Count = <?php echo $part1['max_asteroids'] ?></h4>
            <h4>Best Station Location = <?php echo $part1['best_locations'][0]['location']['y'] . "_" . $part1['best_locations'][0]['location']['x']?></h4>
            <h4>Best Station Location Visible Asteroids:</h4>
            <?php foreach($part1['best_locations'][0]['visible_asteroids'] as $asteroid){
                echo '<p>'.$asteroid.'</p>';
            } ?>
        </div>
        <h1 class="mb-4">Part 2</h1>
        <div class="container-fluid mb-6">
            <h2 class="mb-4">Final Asteroid Check</h2>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <?php while($part2_check_th_number < $grid['width']){
                        echo '<th>'.$part2_check_th_number.'</th>';
                        $part2_check_th_number++;
                    } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($grid['grid'] as $x => $row) {
                    echo '<tr>';
                    echo '<td>'.$x.'</td>';
                    foreach ($row as $y => $column) {
                        if(isset($asteroid_order[$y . "_" . $x])){
                            echo '<td>';
                                echo '<p>'.$asteroid_order[$y . "_" . $x].'</p>';
                            echo '</td>';
                        } else {
                            echo '<td>'.$column.'</td>';
                        }
                    }
                    echo '</tr>';
                } ?>
                </tbody>
            </table>
            <h4>Final Asteroid to be blown up location = <?php echo array_search (200, $asteroid_order) ?></h4>
            <h4>Part2 Answer = <?php echo $unleash; ?></h4>
        </div>
    </body>
</html>
