<?php
// start php session 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// function to load smartphone data from JSON file
function loadSmartphones()
{
    $json = file_get_contents(__DIR__ . '/../data/smartphones.json');
    return json_decode($json, true);
}
// function to load the users data from the JSON file
function loadUsers()
{
    $json = file_get_contents(__DIR__ . '/../data/users.json');
    return json_decode($json, true);
}
// function to save the users data to the JSON file
function saveUsers($users)
{
    $json = json_encode($users, JSON_PRETTY_PRINT);
    file_put_contents(__DIR__ . '/../data/users.json', $json);
}
//function to locate a smartphone via the uniqueId
function findSmartphones($id)
{
    $smartphones = loadSmartphones();
    foreach ($smartphones as $phone) {
        if ($phone['uniqueId'] === $id) {
            return $phone;
        }
    }
    return null;
}

// function to locate a user via their username
function getUser($userName)
{
    $users = loadUsers();
    foreach ($users as $user) {
        if ($user['userName'] === $userName) {
            return $user;
        }
    }
    return null;
}

// function to load all of the reviews held on the JSON file
function loadReviews()
{
    $json = file_get_contents(__DIR__ . '/../data/reviews.json');
    return json_decode($json, true) ?? [];
}
// function to save all of the reviews onto the JSON file
function saveReviews($reviews)
{
    $json = json_encode($reviews, JSON_PRETTY_PRINT);
    file_put_contents(__DIR__ . '/../data/reviews.json', $json);
}

// function to get the reviews for a specific smartphone ID
function getReviews($smartphoneId)
{
    $allReviews = loadReviews();
    return array_filter($allReviews, function ($review) use ($smartphoneId) {
        return $review['smartphone_id'] == $smartphoneId;
    });
}
