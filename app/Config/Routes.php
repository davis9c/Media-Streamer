<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// dashboard
$routes->get('/dashboard', 'Dashboard::index');


$routes->get('/', 'Home::index');
$routes->get('/admin', 'Admin::index');
$routes->post('/admin/upload', 'Admin::upload');
$routes->get('/stream/(:any)', 'Stream::play/$1');


// Video
$routes->get('/video', 'Video::index');
$routes->post('/video/upload', 'Video::upload');
$routes->get('/video/stream/(:any)', 'Video::stream/$1');
$routes->get('/video/delete/(:num)', 'Video::delete/$1');


//playlist
$routes->get('/playlist', 'Playlist::index');
$routes->get('/playlist/create', 'Playlist::create');
$routes->post('/playlist/store', 'Playlist::store');
$routes->get('/playlist/delete/(:num)', 'Playlist::delete/$1');



//stream
$routes->get('/stream', 'Stream::index');
$routes->get('/stream/(:num)', 'Stream::play/$1');

//API
// API VIDEO
$routes->get('/api/videos', 'Video::apiList');
$routes->get('/api/videos/(:num)', 'Video::apiDetail/$1');
$routes->get('/api/videos/(:num)/stream', 'Video::apiStream/$1');
