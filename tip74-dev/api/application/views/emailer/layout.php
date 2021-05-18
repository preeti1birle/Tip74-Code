<!doctype html>
<html>
<head>
  <!-- <meta charset="utf-8"> -->
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <title><?php echo SITE_NAME; ?></title>
  <style>
  p{margin:0;}
  a{text-decoration: none;}
  @import url(https://fonts.googleapis.com/css?family=Roboto:400,500);
  @media only screen and (max-width:767px) {
    table[class="main-wrapper"] {width:94% !important;}
    td[class="content-padding"] {padding:20px !important;}
    td[class="small"] {width:30% !important; display:block !important; padding:10px !important;}
    img[class="get-startedbtn"]{width:100% !important;}
    td[class="mob-content"] {display:block !important; width:100% !important; padding:0 0 10px 10px !important;}
    td[class="mob-padding"] {padding:10px !important;}
    td[class="add-frnds"] {padding:10px 0 !important;}
    td[class="mob-paddinglr"] {padding:0 10px !important;}
    a {outline:none;}
    img {border:0; outline:none;}
  }
</style>
</head>
<body style="background:#EEEEF0; font-family: 'Roboto', sans-serif;">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0; padding:0 0 20px 0; font-size:16px; color:#444444; font-family: 'Roboto', sans-serif; background:#EEEEF0;">
    <tbody>
      <?php
      $this->load->view("emailer/includes/header"); /*header*/
      ?>
      <tr>
        <td colspan="2" class="content-padding" style="padding:40px 40px 20px; border-bottom:1px solid #E5E5E5;background:#FFFFFF;" >
          <?php
          if (!empty($HTML))
          {
            echo $HTML; /*main containt*/
          }
          ?>
        </td>
      </tr>
      <?php
      $this->load->view("emailer/includes/footer"); /*footer*/
      ?>
    </tbody>
  </table>
</body>
</html>
