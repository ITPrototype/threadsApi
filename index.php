<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *,Authorization ');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');

header('Content-type: json/application');
require './requires/config.php';
require './requires/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

$q = $_GET['q'];
$params = explode('/', $q);

$type = $params[0];
if (isset($params[1])) {
  $id = $params[1];
}

if ($method === 'GET') {
  if ($type === 'threads') {
    if (isset($id)) {
      getThread($connect, $id);
    } else {
      getThreads($connect);
    }
  }
} elseif ($method === 'POST') {
  if ($type === 'threads') {
    addThread($connect, $_POST);
  }
} elseif ($method === 'PATCH') {
  if ($type === 'threads') {
    if (isset($id)) {
      $data = file_get_contents('php://input');
      $data = json_decode($data, true);
      updatePost($connect, $id, $data);
    }
  }
} elseif ($method === 'DELETE') {
  if ($type === 'threads') {
    if (isset($id)) {
      deleteThread($connect, $id);
    }
  }
}
