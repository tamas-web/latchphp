<?php
use App\Http\URL;
use App\Config;

$brand = esc_html( Config::get( 'app.site.name' ) );
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head>
<!--[if gte mso 15]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]-->
<meta charset="UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title><?php echo $brand; ?></title>
<style>          img{-ms-interpolation-mode:bicubic;}
          table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;}
          .mceStandardButton, .mceStandardButton td, .mceStandardButton td a{mso-hide:all !important;}
          p, a, li, td, blockquote{mso-line-height-rule:exactly;}
          p, a, li, td, body, table, blockquote{-ms-text-size-adjust:100%; -webkit-text-size-adjust:100%;}
          @media only screen and (max-width: 480px){
            body, table, td, p, a, li, blockquote{-webkit-text-size-adjust:none !important;}
          }
          .mcnPreviewText{display: none !important;}
          .bodyCell{margin:0 auto; padding:0; width:100%;}
          .ExternalClass, .ExternalClass p, .ExternalClass td, .ExternalClass div, .ExternalClass span, .ExternalClass font{line-height:100%;}
          .ReadMsgBody{width:100%;} .ExternalClass{width:100%;}
          a[x-apple-data-detectors]{color:inherit !important; text-decoration:none !important; font-size:inherit !important; font-family:inherit !important; font-weight:inherit !important; line-height:inherit !important;}
            body{height:100%; margin:0; padding:0; width:100%; background: #ffffff;}
            p{margin:0; padding:0;}
            table{border-collapse:collapse;}
            td, p, a{word-break:break-word;}
            h1, h2, h3, h4, h5, h6{display:block; margin:0; padding:0;}
            img, a img{border:0; height:auto; outline:none; text-decoration:none;}
            a[href^="tel"], a[href^="sms"]{color:inherit; cursor:default; text-decoration:none;}
            li p {margin: 0 !important;}
            .ProseMirror a {
                pointer-events: none;
            }
                  .mceColumn .mceButtonLink,
                  .mceColumn-1 .mceButtonLink,
                  .mceColumn-2 .mceButtonLink,
                  .mceColumn-3 .mceButtonLink,
                  .mceColumn-4 .mceButtonLink {min-width:30px;}
            @media only screen and (max-width: 640px){
                .mceClusterLayout td{padding: 4px !important;}
            }
            @media only screen and (max-width: 480px){
                body{width:100% !important; min-width:100% !important; }
                body.mobile-native {
                    -webkit-user-select: none; user-select: none; transition: transform 0.2s ease-in; transform-origin: top center;
                }
                body.mobile-native.selection-allowed a, body.mobile-native.selection-allowed .ProseMirror {
                    user-select: auto;
                    -webkit-user-select: auto;
                }
                colgroup{display: none;}
                img{height: auto !important;}
                .mceWidthContainer{max-width: 660px !important;}
                .mceColumn{display: block !important; width: 100% !important;}
                .mceColumn-forceSpan{display: table-cell !important; width: auto !important;}
                .mceColumn-forceSpan .mceButton a{min-width:0 !important;}
                                  .mceReverseStack {display:table; width: 100%;}
                             .mceColumn-1 {display:table-footer-group; width: 100% !important;}
                             .mceColumn-2 {display: block !important; width: 100% !important;}
                             .mceColumn-3 {display:table-header-group; width: 100% !important;}
                             .mceColumn-4 {display:table-caption; width: 100% !important;}
                             .mceKeepColumns .mceButtonLink {min-width:0px;}
                .mceBlockContainer{padding-right:16px !important; padding-left:16px !important;}
                .mceTextBlockContainer{padding-right:16px !important; padding-left:16px !important;}
                .mceBlockContainerE2E{padding-right:0px; padding-left:0px;}
                .mceSpacing-24{padding-right:16px !important; padding-left:16px !important;}
                .mceImage, .mceLogo{width: 100% !important; height: auto !important;}
                .mceFooterSection .mceText, .mceFooterSection .mceText p{font-size: 16px !important; line-height: 140% !important;}
            }
            div[contenteditable="true"] {outline: 0;}
            .ProseMirror h1.empty-node:only-child::before,
            .ProseMirror h2.empty-node:only-child::before,
            .ProseMirror h3.empty-node:only-child::before,
            .ProseMirror h4.empty-node:only-child::before {
                content: 'Heading';
            }
            .ProseMirror p.empty-node:only-child::before, .ProseMirror:empty::before {
                content: 'Start typing...';
            }
            .mceImageBorder {display: inline-block;}
            .mceImageBorder img {border: 0 !important;}
body, #bodyTable { background-color: #ffffff; }.mceText, .mcnTextContent, .mceLabel { font-family: "Helvetica Neue", Helvetica, Arial, Verdana, sans-serif; }.mceText, .mcnTextContent, .mceLabel { color: rgb(0, 0, 0); }.mceText p { margin-bottom: 0px; }.mceText label { margin-bottom: 0px; }.mceText input { margin-bottom: 0px; }.mceSpacing-12 .mceInput + .mceErrorMessage { margin-top: -6px; }.mceText p { margin-bottom: 0px; }.mceText label { margin-bottom: 0px; }.mceText input { margin-bottom: 0px; }.mceSpacing-24 .mceInput + .mceErrorMessage { margin-top: -12px; }.mceInput { background-color: transparent; border: 2px solid rgb(208, 208, 208); width: 60%; color: rgb(77, 77, 77); display: block; }.mceInput[type="radio"], .mceInput[type="checkbox"] { width: auto !important; float: left; margin-right: 12px; display: inline; }.mceLabel > .mceInput { margin-bottom: 0px; margin-top: 2px; }.mceLabel { display: block; }.mceText p, .mcnTextContent p { color: rgb(0, 0, 0); font-family: "Helvetica Neue", Helvetica, Arial, Verdana, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.5; mso-line-height-alt: 150%; text-align: left; letter-spacing: 0px; direction: ltr; margin: 0px; }.mceText a, .mcnTextContent a { color: #65a30d; font-style: normal; font-weight: normal; text-decoration: underline; direction: ltr; }.mceSectionBody .mceText p, .mceSectionBody .mcnTextContent p { }.mceSectionBody .mceText a, .mceSectionBody .mcnTextContent a { font-style: normal; }.mceSectionFooter .mceText p, .mceSectionFooter .mcnTextContent p { }.mceSectionFooter .mceText a, .mceSectionFooter .mcnTextContent a { font-style: normal; }
@media only screen and (max-width: 480px) {
            .mceText p { font-size: 16px !important; margin: 0px; line-height: 1.5 !important; mso-line-height-alt: 150%; }
          }
@media only screen and (max-width: 480px) {
            .bodyCell { padding-left: 16px !important; padding-right: 16px !important; }
          }
@media only screen and (max-width: 480px) {
            .mceDividerBlock { border-top-width: 2px !important; }
          }
@media only screen and (max-width: 480px) {
            .mceDividerContainer { width: 100% !important; }
          }
#dataBlockId-13 p, #dataBlockId-13 h1, #dataBlockId-13 h2, #dataBlockId-13 h3, #dataBlockId-13 h4, #dataBlockId-13 ul { text-align: center; }</style></head>
<body>
<center>
<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="background-color: #ffffff;">
<tbody><tr>
<td class="bodyCell" align="center" valign="top">
<table id="root" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody data-block-id="5" class="mceWrapper"><tr><td style="background-color:transparent" valign="top" align="center" class="mceSectionHeader"><!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="660" style="width:660px;"><tr><td><![endif]--><table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:660px" role="presentation"><tbody><tr><td style="background-color:#ffffff" valign="top" class="mceWrapperInner"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation" data-block-id="4"><tbody><tr class="mceRow"><td style="background-position:center;background-repeat:no-repeat;background-size:cover" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tbody><tr><td style="padding-top:0;padding-bottom:0" valign="top" class="mceColumn" data-block-id="-4" colspan="12" width="100%"><table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tbody><tr><td style="background-color:transparent" valign="top" id="blockContainerId-2"><table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation" data-block-id="2"><tbody><tr><td valign="top" class="mceSpacerBlock" height="20"></td></tr></tbody></table></td></tr><tr><td style="padding-top:12px;padding-bottom:12px;padding-right:48px;padding-left:25px" valign="top" class="mceImageBlockContainer" align="left" id="blockContainerId-3"><div><!--[if !mso]><!--></div><a href="<?php echo URL::BUILD(); ?>" style="display:block" target="_blank" data-block-id="3"><table align="left" border="0" cellpadding="0" cellspacing="0" width="45%" style="border-collapse:separate;margin:0;vertical-align:top;max-width:45%;width:45%;height:auto" role="presentation" data-testid="image-3"><tbody><tr><td style="border:0;border-radius:0;margin:0" valign="top"><img alt="<?php echo $brand; ?>" src="<?php echo URL::BUILD( 'assets/images/mail-logo.png' ); ?>" width="264.15000000000003" height="auto" style="display:block;max-width:100%;height:auto;border-radius:0" class="mceLogo"/></td></tr></tbody></table></a><div><!--<![endif]--></div><div>
<!--[if mso]>
<a href="<?php echo URL::BUILD(); ?>"><span class="mceImageBorder" style="border:0;vertical-align:top;margin:0"><img role="presentation" class="mceLogo" src="<?php echo URL::BUILD( 'assets/images/mail-logo.png' ); ?>" alt="<?php echo $brand; ?>" width="264.15000000000003" height="auto" style="display:block;max-width:264.15000000000003px;width:264.15000000000003px;height:auto"/></span></a>
<![endif]-->
</div></td></tr><tr><td style="background-color:transparent" valign="top" id="blockContainerId-26"><table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation" data-block-id="26"><tbody><tr><td valign="top" class="mceSpacerBlock" height="20"></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td></tr></tbody><tbody data-block-id="11" class="mceWrapper"><tr><td style="background-color:transparent" valign="top" align="center" class="mceSectionBody"><!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="660" style="width:660px;"><tr><td><![endif]--><table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:660px" role="presentation"><tbody><tr><td style="background-color:#ffffff" valign="top" class="mceWrapperInner"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation" data-block-id="10"><tbody><tr class="mceRow"><td style="background-position:center;background-repeat:no-repeat;background-size:cover" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tbody><tr><td style="padding-top:0;padding-bottom:0" valign="top" class="mceColumn" data-block-id="-5" colspan="12" width="100%"><table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tbody><tr><td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0" valign="top" id="blockContainerId-24"><table width="100%" style="border:0;background-color:#ffffff;border-radius:0;border-collapse:separate"><tbody><tr><td style="padding-left:24px;padding-right:24px;padding-top:24px;padding-bottom:24px" class="mceTextBlockContainer"><div data-block-id="24" class="mceText" id="dataBlockId-24" style="width:100%"><?php echo $this->data['content']; ?></div></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td></tr></tbody><tbody data-block-id="17" class="mceWrapper"><tr><td style="background-color:transparent" valign="top" align="center" class="mceSectionFooter"><!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="660" style="width:660px;"><tr><td><![endif]--><table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:660px" role="presentation"><tbody><tr><td style="background-color:#ffffff" valign="top" class="mceWrapperInner"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation" data-block-id="16"><tbody><tr class="mceRow"><td style="background-position:center;background-repeat:no-repeat;background-size:cover" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tbody><tr><td style="padding-top:0;padding-bottom:0" valign="top" class="mceColumn" data-block-id="-6" colspan="12" width="100%"><table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tbody><tr><td style="padding-top:0;padding-bottom:0;padding-right:0;padding-left:0" valign="top" id="blockContainerId-29"><table width="100%" style="border:0;background-color:#84cc16;border-radius:0;border-collapse:separate"><tbody><tr><td style="padding-left:24px;padding-right:24px;padding-top:12px;padding-bottom:12px" class="mceTextBlockContainer"><div data-block-id="29" class="mceText" id="dataBlockId-29" style="width:100%"><p class="last-child"><span style="color:#13160f;"><span style="font-size: 12px">&copy; <?php echo date( 'Y' ); ?> </span></span><strong><span style="color:#13160f;"><span style="font-size: 12px"><?php echo $brand; ?></span></span></strong><span style="color:#13160f;"><span style="font-size: 12px">. All rights reserved.</span></span></p></div></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><!--[if (gte mso 9)|(IE)]></td></tr></table><![endif]--></td></tr></tbody></table>
</td>
</tr>
</tbody></table>
</center>
</body></html>