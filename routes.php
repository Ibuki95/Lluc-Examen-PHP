<?php

$router->get('/', 'index.php');
$router->get('/about', 'about.php');
$router->get('/contact', 'contact.php');

$router->get('/notes', 'NotesController.php')->only('auth');
$router->get('/note', 'NotesController.php')->only('auth');
$router->delete('/note', 'NotesController.php')->only('auth');

$router->get('/note/edit', 'NotesController.php')->only('auth');
$router->patch('/note', 'NotesController.php')->only('auth');

$router->get('/notes/create', 'NotesController.php')->only('auth');
$router->post('/notes', 'NotesController.php')->only('auth');

$router->get('/register', 'registration/create.php')->only('guest');
$router->post('/register', 'registration/store.php')->only('guest');

$router->get('/login', 'session/create.php')->only('guest');
$router->post('/session', 'session/store.php')->only('guest');
$router->delete('/session', 'session/destroy.php')->only('auth');
