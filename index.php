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

        <? if(isset($_GET['note-not-found']))  { ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> <strong>Note Not Found!</strong> This note either never existed or has been read and destroyed already.
          </div>
        <? } ?>

        <? if(isset($_GET['invalid-url']))  { ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> <strong>Note Not Found!</strong> The note url was not valid.
          </div>
        <? } ?>

        <div class="row">
          <div class="col-xs-1-12">
            <h1>New note</<h1>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-1-12">
              <div class="form-group">
                <label for="private-note">This note will self-destruct once it is read.</label>
                <textarea rows="10" maxlength="10000" type="text" class="form-control" name="note" id="private-note" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="Don't tell anyone, but..."></textarea>
                <p class="help-block">This note will be encrypted via <abbr title="256-bit Advanced Encryption Standard">AES-256</abbr> and stored until it is read &amp; deleted.</p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-6">
              <button type="submit" name="button" class="btn btn-success"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Create Note</button>
            </div>
            <div class="col-xs-6 text-right small">
              <p>
                This note is <span id="counter">0</span> of 10,000 characters long.
              </p>
            </div>
          </div>
        </div>
        <div class="modal fade" id="dialog" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" data-backdrop="static">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="">Your Note Link is Ready!</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="url">The note is encrypted and ready to send.</label>
                  <input type="text" class="form-control" id="url" placeholder="" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
                  <p class="help-block">Send this link to the recipient, it will be destroyed upon reading.</p>
                </div>
              </div>
              <div class="modal-footer">
                <div id="copy-status">
                </div>
                <button id="copy-text" type="button" class="btn btn-primary" data-clipboard-action="copy" data-clipboard-target="#url"><span class="glyphicon glyphicon-send" aria-hidden="true"></span> Copy to Clipboard</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span> Close</button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </form>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.16/clipboard.min.js"></script>
    <script>
    function NumberFormat(x) {
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    $(function(){
      var copybtn = document.getElementById('copy-text');
      var clipboard = new Clipboard(copybtn);
      clipboard.on('success', function(e) {
        $('#copy-status').html('<span class="label label-success pull-left">Copied!</span>');
      });
      clipboard.on('error', function(e) {
        $('#copy-status').html('<span class="label label-danger pull-left">Failed!</span>')
      });

      $('#private-note').on('keydown keyup',function(){
        $('#counter').html(NumberFormat(($(this).val().length)));
      })
      $('#url').on('focus click',function(){
        $(this).select();
      })
      $('#dialog').on('shown.bs.modal', function () {
        $('#url').select();
      })
      $(document).on('submit','#submit-form',function(event){
        event.preventDefault();
		if($('#private-note').val().length > 0)
		{
	        $.post('process.php',$(this).serialize(),function(response){
	          $('#dialog').modal('show')
	          //$('#url').val('http://localhost/privnote/'+response);
	          $('#url').val('https://note.mafkr.com/'+response);
	          $('#url').click();
	        })
		}
		else {
			alert("No point in sending empty notes ;)");
		}

      })
    })
    </script>
  </body>
  </html>
