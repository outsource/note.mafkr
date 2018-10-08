<?php
require("connection.php");

function is_sha256($str) {
  return (bool) preg_match('/^[0-9a-f]{64}$/i', $str);
}

$url =  mysqli_real_escape_string($conn,$_GET['id']);

//dont even try a query unless its sha256
if(!is_sha256($url))  {
  header("Location: https://note.mafkr.com/?invalid-url");
  die();
}

$rs = mysqli_query($conn,"SELECT * FROM privnotes WHERE url='$url'") or die(mysqli_error($conn));
$row = mysqli_fetch_object($rs);

if(mysqli_num_rows($rs) === 1){
  $method = "aes-256-cbc";

  $decrypted = openssl_decrypt($row->note,$method,$row->cryptkey);
  mysqli_query($conn,"DELETE FROM privnotes WHERE url='$url'") or die(mysqli_error($conn));

} else {
  header("Location: https://note.mafkr.com/?note-not-found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>note.mafkr.com : Encrypted, self-destructing, private notes</title>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <style>
  .btn {
    border-width: 1px 3px 3px 1px;
    border-color: rgba(0,0,0,0.1)!important;
  }
  .panel-body {
    word-wrap: break-word;
  }
  </style>
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
  <header class="container-fluid">
    <div class="row">
      <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="https://note.mafkr.com/"><span class="glyphicon glyphicon-fire" aria-hidden="true"></span> Private Note</a>
          </div>
          <div class="collapse navbar-collapse" id="navbar">
            <!--
            <ul class="nav navbar-nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#"></a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
              <li><a href="#"></a></li>
            </ul>
            -->
          </div>
        </div>
      </nav>
    </div>
  </header>
<form id="submit-form" action="process.php" method="post" autocomplete="off">
  <main>
    <div class="container">
      <div class="row">
        <div class="col-xs-1-12">
          <h1>Read Note</<h1>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-1-12">

            <div class="panel panel-default">
              <div class="panel-body" id="private-note">
                <?=nl2br($decrypted)?>
              </div>
            </div>
            <p class="help-block">This note has been destroyed, copy it if you need to save it.</p>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <button id="copy-text" type="button" name="button" class="btn btn-primary" data-clipboard-action="copy" data-clipboard-target="#private-note"><span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span> Copy Note to Clipboard</button>
          </div>
          <div class="col-xs-6" id="copy-status">
          </div>
        </div>
      </div>
    </main>
  </form>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.16/clipboard.min.js"></script>
  <script>

  $(function(){
    var copybtn = document.getElementById('copy-text');
    var clipboard = new Clipboard(copybtn);

    clipboard.on('success', function(e) {
      $('#copy-status').html('<span class="label label-success pull-left">Copied!</span>');
    });
    clipboard.on('error', function(e) {
      $('#copy-status').html('<span class="label label-danger pull-left">Failed!</span>')
    });

  })
  </script>
</body>
</html>
