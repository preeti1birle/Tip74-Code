

<tr>
  <td style="padding:25px 40px;background:#FFFFFF;" class="content-padding"><table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tr>
        <td>
          <p style="font-family: 'Roboto', sans-serif; font-size:14px; color:#444444; line-height:21px; margin:0;">Thanks,</p>
          <p style="font-family: 'Roboto', sans-serif; font-size:14px; color:#444444; line-height:21px; margin: 4px 0;">Team <?php echo SITE_NAME; ?></p>
        </td>
        <td align="right"><table  border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td align="right" style="margin:0; padding:0;"><?php if(!empty(FACEBOOK_URL)){ ?>
                <a href="<?php echo FACEBOOK_URL;?>" style="text-decoration:none;display:inline-block; cursor:pointer; padding-right:0px;"> <img style="outline:none;border:none;" src="<?php echo ASSET_BASE_URL.'img/emailer/btn-fb.png'; ?>" alt="" /> </a>
                <?php } ?>
              </td>
              <td align="right" style="width:40px;margin:0; padding:0;"><?php if(!empty(TWITTER_URL)){ ?>
                <a href="<?php echo TWITTER_URL;?>" style="text-decoration:none;display:inline-block; cursor:pointer; padding-right:0px;"> <img style="outline:none;border:none;" src="<?php echo ASSET_BASE_URL.'img/emailer/btn-twit.png'; ?>" alt="" /> </a>
                <?php } ?>
              </td>
              <td align="right" style="width:40px;margin:0; padding:0;"><?php if(!empty(LINKEDIN_URL)){ ?>
                <a href="<?php echo LINKEDIN_URL;?>" style="text-decoration:none;display:inline-block; cursor:pointer; padding-right:0px;"> <img style="outline:none;border:none;" src="<?php echo ASSET_BASE_URL.'img/emailer/btn-linked.png'; ?>" alt="" /> </a>
                <?php } ?>
              </td>
              <td align="right" style="width:40px;margin:0; padding:0;"><?php if(!empty(GOOGLE_PLUS_URL)){ ?>
                <a href="<?php echo GOOGLE_PLUS_URL;?>" style="text-decoration:none;display:inline-block; cursor:pointer; padding-right:0px;"> <img style="outline:none;border:none;" src="<?php echo ASSET_BASE_URL.'img/emailer/btn-gplus.png'; ?>" alt="" /> </a>
                <?php } ?>
              </td>
            </tr>
          </table></td>
      </tr>
    </table></td>
</tr>
<tr>
  <td style="vertical-align:top;"><img src="<?php echo ASSET_BASE_URL.'img/emailer/shadow.png' ?>" alt="" style="vertical-align:top; width:100%;"></td>
</tr>
<tr>
  <td align="center" style="font-family: 'Roboto', sans-serif; font-size:13px; color:#999999; padding:10px 0 20px 0;">Copyright &copy; <?php echo date('Y') ?> <?php echo SITE_NAME ?>, All rights reserved. </td>
</tr>
</tbody>
</table>
</td>
</tr>
