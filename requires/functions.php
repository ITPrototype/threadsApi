<?php

function getThreads($connect)
{
  $threads = mysqli_query($connect, "select * from `threads`");

  $threadList = [];
  while ($thread = mysqli_fetch_assoc($threads)) {
    $threadList[] = $thread;
  }
  echo json_encode($threadList);
}


function getThread($connect, $id)
{
  $thread = mysqli_query($connect, "select * from `threads` where `id` = '$id'");
  if (mysqli_num_rows($thread) === 0) {
    http_response_code(404);
    $res = [
      "status" => false,
      "message" => "Thread not found"
    ];
    echo json_encode($res);
  } else {
    $thread = mysqli_fetch_assoc($thread);
    echo json_encode($thread);
  }
}

function addThread($connect, $data)
{
  $replyToThread = isset($data['reply_to_thread']) ? $data['reply_to_thread'] : null;
  $text = isset($data['text']) ? mysqli_real_escape_string($connect, $data['text']) : '';
  if ($text != '') {
    mysqli_query($connect, "INSERT INTO `threads` (`text`) VALUES ('$text')");
    if (isset($replyToThread)) {
      mysqli_query($connect, "UPDATE `threads` SET `replies` = `replies` + 1 WHERE `id` = $replyToThread");
      mysqli_query($connect, "INSERT INTO `threads` (`text`,`reply_to_thread`) VALUES ('$text','$replyToThread')");
    }
    http_response_code(201);
    $res = [
      "status" => true,
      "post_id" => mysqli_insert_id($connect),
    ];
    echo json_encode($res);
  } else {
    http_response_code(400);
    $res = [
      "status" => false,
      "message" => "Missing or empty 'text' parameter.",
    ];
    echo json_encode($res);
  }
}


function updatePost($connect, $id, $thread)
{
  $text = $thread['text'];
  mysqli_query($connect, "UPDATE `threads` SET `text` = '$text' WHERE `threads`.`id` = '$id'");

  http_response_code(200);
  $res = [
    "status" => true,
    "post_id" => "Thread updated"
  ];
  echo json_encode($res);
}

function deleteThread($connect, $id)
{
  mysqli_query($connect, "DELETE FROM `threads` WHERE `threads`.`id` = '$id'");
  mysqli_query($connect, "DELETE FROM `threads` WHERE `threads`.`reply_to_thread` = '$id'");
  http_response_code(200);
  $res = [
    "status" => true,
    "message" => "Thread deleted"
  ];

  echo json_encode($res);
}
