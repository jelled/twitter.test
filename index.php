<?php
/**
 * Author: Ben Bjurstrom
 * Date: 3/24/14
 * Time: 2:22 PM
 * File: index.php
 */

require_once 'utilities.php';

$config = array(
    'consumer_key' => 'YOUR_CONSUMER_KEY',
    'consumer_secret' => 'YOUR_CONSUMER_SECRET',
    'token' => 'A_USER_TOKEN',
    'secret' => 'A_USER_SECRET',
    'bearer' => 'YOUR_OAUTH2_TOKEN',
);

//instantiate our twitter class
$twitter = new twitter($config);

//set the search term
$q = '"social analytics"';

//setup a few variables
$statuses = array();
$users = array();
$max_id = 0;


//pull 1000 statuses;
while (count($statuses) < 1000){
    $code = $twitter->apponly_request(array(
        'method' => 'GET',
        'url' => $twitter->url('1.1/search/tweets'),
        'params' => array(
            'q' => $q,
            'count' => 100,
            'result_type' => 'recent',
            'max_id' => $max_id
        )
    ));

    if ($code == 200) {
        //decode the response
        $response = json_decode($twitter->response['response']);

        //set the new max_id cursor
        $end = end($response->statuses);
        $max_id = $end->id - 1;

        //merge current  results into our statuses array
        $statuses = array_merge($statuses, $response->statuses);
    } else {
        exit ('Error fetching tweets');
    }
}

//trim off any excess statuses
$statuses = array_slice($statuses, 0, 1000);

//extract the users
foreach($statuses as $status){
    //set the array key to the userid to remove duplicates
    $users[$status->user->id] = array(
        'twitter_id'=> $status->user->id,
        'twitter_name' => $status->user->name,
        'location_string' => $status->user->location,
        'url' => $status->user->url,
        'profile' => $status->user->description
    );
}

//sort the $users array by twitter name
usort($users, make_comparer(['twitter_name', SORT_ASC]));

///take the top 100 and print them
$print_users = array_slice($users, 0, 100);
print_r($print_users);




