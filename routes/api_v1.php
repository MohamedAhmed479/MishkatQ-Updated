<?php


// Load all authentication-related routes for both regular users and admins.
// These routes are defined in: routes/api/V1/auth.php
require __DIR__ . '/api/V1/auth.php';


// Load all routes related to user management.
// These routes are defined in: routes/api/V1/user.php
require __DIR__ . '/api/V1/user.php';


require __DIR__ . '/api/V1/quran.php';
