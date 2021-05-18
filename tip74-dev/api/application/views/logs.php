<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>CI Logviewer</title>
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <!-- GOOGLE FONT -->
  <link href='http://fonts.googleapis.com/css?family=Noto+Sans|Roboto:300,400,500' rel='stylesheet'>
  <style type="text/css">
  body {
    padding-top: 70px;
    background-color: #fafafa;
    color: #111;
    font-family: Roboto, sans-serif;
  }
  .panel {
    border: 0px;
    box-shadow: none;
  }
  .panel-body {
    padding: 0px;
  }
  th{
    font-weight: 500;
  }
  @media (max-width: 768px) {
    body {
      padding-top: 140px;
    }
  }
</style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand" href="<?=$url;?>">Logviewer (<?php echo SITE_NAME; ?>)</a>
      </div>
      <div class="navbar-form navbar-right">
        <?php
        if ( !empty($logs) ) {
          echo '<div class="form-group">';
          echo '<select class="form-control" onchange="window.location = \''.$url.'?log_date=\' + this.options[this.selectedIndex].value">';
          foreach ($logs as $key => $value) {
              // check if the selected date
            $selected_option = ($key == $selected) ? 'selected="selected"' : null;
              // set the option
            echo '<option value="'.$key.'" '.$selected_option.'>'.$value.'</option>';
          }
          echo '</select>';
          echo '</div>';
        }
        ?>
      </div>
    </div>
  </div>
</nav>
<!-- // Navbar -->

<div class="container">
  <div class="panel panel-default">
    <div class="panel-body">
      <?php
      if (!empty($log)) {
        echo '<table class="table table-responsive table-striped table-bordered ">
        <thead>
        <tr>
        <th class="text-center">Time</th>
        <th class="text-center">Type</th>
        <th>Details</th>
        </tr>
        </thead>';
        echo '<tbody>';
        foreach ($log as $value) {
          echo '<tr>';
          echo '<td>'.$value['time'].'</td>';
          switch($value['severity']) {
            case 'Error':
            $button_type = 'btn-danger';
            break;
            case 'Warning':
            $button_type = 'btn-warning';
            break;
            case 'Notice':
            $button_type = 'btn-default';
            break;
          }
          echo '<td><button class="btn btn-xs '.$button_type.' btn-block" disabled>'.$value['severity'].'</button></td>';
          echo '<td>'.$value['error'].'</td>';
          echo '</tr>';

        }
        echo '</tbody>';
        echo '</table>';

      } elseif ( $log_threshold < 1 && empty($logs) ) {
        // the setting in config are not correct
        echo 'Please change the setting $config["log_threshold"] = 1 in config/config.php';
      } elseif ( $log_threshold >= 1 && empty($logs) ) {
        // there are no logs
        echo 'No log files found';
      } else {
        // there are some notice in the log file
        echo 'No errors or warnings, please set hide_notices to false if you want to view notices';
      }
      ?>

    </div>
  </div>
</div>
</body>
</html>
