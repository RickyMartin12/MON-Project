 <?php

$wlead=$mon3->query("select * from property_leads where id=".$lead_id." ")->fetch_assoc();
$status40="
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\"><head><meta content=\"text/html;charset=UTF-8\" http-equiv=\"Content-Type\"><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"/><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"/><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/><meta name=\"x-apple-disable-message-reformatting\"/><meta name=\"apple-mobile-web-app-capable\" content=\"yes\"/><meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\"/><meta name=\"format-detection\" content=\"telephone=no\"/><title>Lazer - Successful Paperwork Submission</title><link href=\"https://fonts.google.com/specimen/Open+Sans?selection.family=Open+Sans\" rel=\"stylesheet\" type=\"text/css\"/><link href=\"https://fonts.google.com/specimen/Montserrat\" rel=\"stylesheet\" type=\"text/css\"/><link href=\"https://fonts.google.com/specimen/Mea+Culpa\" rel=\"stylesheet\" type=\"text/css\"/><link href=\"https://fonts.google.com/specimen/The+Nautigal?category=Handwriting#standard-styles\" rel=\"stylesheet\" type=\"text/css\"/><style type=\"text/css\">
/* Resets */
.ReadMsgBody { width: 100%; background-color: #ebebeb;}
.ExternalClass {width: 100%; background-color: #ebebeb;}
.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height:100%;}
a[x-apple-data-detectors]{
color:inherit !important;
text-decoration:none !important;
font-size:inherit !important;
font-family:inherit !important;
font-weight:inherit !important;
line-height:inherit !important;
}
body {-webkit-text-size-adjust:none; -ms-text-size-adjust:none;}
body {margin:0; padding:0;}
.yshortcuts a {border-bottom: none !important;}
.rnb-del-min-width{ min-width: 0 !important; }
/* Add new outlook css start */
.templateContainer{
max-width:590px !important;
width:auto !important;
}
/* Add new outlook css end */
/* Image width by default for 3 columns */
img[class=\"rnb-col-3-img\"] {
max-width:170px;
}
/* Image width by default for 2 columns */
img[class=\"rnb-col-2-img\"] {
max-width:264px;
}
/* Image width by default for 2 columns aside small size */
img[class=\"rnb-col-2-img-side-xs\"] {
max-width:180px;
}
/* Image width by default for 2 columns aside big size */
img[class=\"rnb-col-2-img-side-xl\"] {
max-width:350px;
}
/* Image width by default for 1 column */
img[class=\"rnb-col-1-img\"] {
max-width:550px;
}
/* Image width by default for header */
img[class=\"rnb-header-img\"] {
max-width:590px;
}
/* Ckeditor line-height spacing */
.rnb-force-col p, ul, ol{margin:0px!important;}
.rnb-del-min-width p, ul, ol{margin:0px!important;}
/* tmpl-2 preview */
.rnb-tmpl-width{ width:100%!important;}
/* tmpl-11 preview */
.rnb-social-width{padding-right:15px!important;}
/* tmpl-11 preview */
.rnb-social-align{float:right!important;}
/* Ul Li outlook extra spacing fix */
li{mso-margin-top-alt: 0; mso-margin-bottom-alt: 0;}
/* Outlook fix */
table {mso-table-lspace:0pt; mso-table-rspace:0pt;}
/* Outlook fix */
table, tr, td {border-collapse: collapse;}
/* Outlook fix */
p,a,li,blockquote {mso-line-height-rule:exactly;}
/* Outlook fix */
.msib-right-img { mso-padding-alt: 0 !important;}
@media only screen and (min-width:590px){
/* mac fix width */
.templateContainer{width:590px !important;}
}
@media screen and (max-width: 360px){
/* yahoo app fix width \"tmpl-2 tmpl-10 tmpl-13\" in android devices */
.rnb-yahoo-width{ width:360px !important;}
}
@media screen and (max-width: 380px){
/* fix width and font size \"tmpl-4 tmpl-6\" in mobile preview */
.element-img-text{ font-size:24px !important;}
.element-img-text2{ width:230px !important;}
.content-img-text-tmpl-6{ font-size:24px !important;}
.content-img-text2-tmpl-6{ width:220px !important;}
}
@media screen and (max-width: 480px) {
td[class=\"rnb-container-padding\"] {
padding-left: 10px !important;
padding-right: 10px !important;
}
/* force container nav to (horizontal) blocks */
td.rnb-force-nav {
display: inherit;
}
/* fix text alignment \"tmpl-11\" in mobile preview */
.rnb-social-text-left {
width: 100%;
text-align: center;
margin-bottom: 15px;
}
.rnb-social-text-right {
width: 100%;
text-align: center;
}
}
@media only screen and (max-width: 600px) {
/* center the address & social icons */
.rnb-text-center {text-align:center !important;}
/* force container columns to (horizontal) blocks */
th.rnb-force-col {
display: block;
padding-right: 0 !important;
padding-left: 0 !important;
width:100%;
}
table.rnb-container {
width: 100% !important;
}
table.rnb-btn-col-content {
width: 100% !important;
}
table.rnb-col-3 {
/* unset table align=\"left/right\" */
float: none !important;
width: 100% !important;
/* change left/right padding and margins to top/bottom ones */
margin-bottom: 10px;
padding-bottom: 10px;
/*border-bottom: 1px solid #eee;*/
}
table.rnb-last-col-3 {
/* unset table align=\"left/right\" */
float: none !important;
width: 100% !important;
}
table.rnb-col-2 {
/* unset table align=\"left/right\" */
float: none !important;
width: 100% !important;
/* change left/right padding and margins to top/bottom ones */
margin-bottom: 10px;
padding-bottom: 10px;
/*border-bottom: 1px solid #eee;*/
}
table.rnb-col-2-noborder-onright {
/* unset table align=\"left/right\" */
float: none !important;
width: 100% !important;
/* change left/right padding and margins to top/bottom ones */
margin-bottom: 10px;
padding-bottom: 10px;
}
table.rnb-col-2-noborder-onleft {
/* unset table align=\"left/right\" */
float: none !important;
width: 100% !important;
/* change left/right padding and margins to top/bottom ones */
margin-top: 10px;
padding-top: 10px;
}
table.rnb-last-col-2 {
/* unset table align=\"left/right\" */
float: none !important;
width: 100% !important;
}
table.rnb-col-1 {
/* unset table align=\"left/right\" */
float: none !important;
width: 100% !important;
}
img.rnb-col-3-img {
/**max-width:none !important;**/
width:100% !important;
}
img.rnb-col-2-img {
/**max-width:none !important;**/
width:100% !important;
}
img.rnb-col-2-img-side-xs {
/**max-width:none !important;**/
width:100% !important;
}
img.rnb-col-2-img-side-xl {
/**max-width:none !important;**/
width:100% !important;
}
img.rnb-col-1-img {
/**max-width:none !important;**/
width:100% !important;
}
img.rnb-header-img {
/**max-width:none !important;**/
width:100% !important;
margin:0 auto;
}
img.rnb-logo-img {
/**max-width:none !important;**/
width:100% !important;
}
td.rnb-mbl-float-none {
float:inherit !important;
}
.img-block-center{text-align:center !important;}
.logo-img-center
{
float:inherit !important;
}
/* tmpl-11 preview */
.rnb-social-align{margin:0 auto !important; float:inherit !important;}
/* tmpl-11 preview */
.rnb-social-center{display:inline-block;}
/* tmpl-11 preview */
.social-text-spacing{margin-bottom:0px !important; padding-bottom:0px !important;}
/* tmpl-11 preview */
.social-text-spacing2{padding-top:15px !important;}
/* UL bullet fixed in outlook */
ul {mso-special-format:bullet;}
}@media screen{body{font-family:'Open Sans','Arial',Helvetica,sans-serif;}}@media screen{body{font-family:'Montserrat','Arial',Helvetica,sans-serif;}}@media screen{body{font-family:'Mea Culpa','Times New Roman',Times,serif;}}@media screen{body{font-family:'Nautigal','Arial',Helvetica,sans-serif;}}</style><!--[if gte mso 11]><style type=\"text/css\">table{border-spacing: 0; }table td {border-collapse: separate;}</style><![endif]--><!--[if !mso]><!--><style type=\"text/css\">table{border-spacing: 0;} table td {border-collapse: collapse;}</style> <!--<![endif]--><!--[if gte mso 15]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]--><!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]--></head><body>

<table border=\"0\" align=\"center\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" class=\"main-template\" bgcolor=\"#f1f2f2\" style=\"background-color: rgb(241, 242, 242);\">

    <tbody><tr>
        <td align=\"center\" valign=\"top\">
        <!--[if gte mso 9]>
                        <table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"590\" style=\"width:590px;\">
                        <tr>
                        <td align=\"center\" valign=\"top\" width=\"590\" style=\"width:590px;\">
                        <![endif]-->
            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"590\" class=\"templateContainer\" style=\"max-width:590px!important; width: 590px;\">
        <tbody><tr>

        <td align=\"center\" valign=\"top\">

            <div style=\"background-color: rgb(241, 242, 242);\">
                
                <table class=\"rnb-del-min-width rnb-tmpl-width\" width=\"100%\" cellpadding=\"0\" border=\"0\" cellspacing=\"0\" style=\"min-width:590px;\" name=\"Layout_18\" id=\"Layout_18\">
                    
                    <tbody><tr>
                        <td class=\"rnb-del-min-width\" valign=\"top\" align=\"center\" style=\"min-width: 590px;\">
                            <a href=\"#\" name=\"Layout_18\"></a>
                            <table width=\"100%\" cellpadding=\"0\" border=\"0\" bgcolor=\"#f1f2f2\" align=\"center\" cellspacing=\"0\" style=\"background-color: rgb(241, 242, 242);\">
                                <tbody><tr>
                                    <td height=\"10\" style=\"font-size:1px; line-height:1px; mso-hide: all;\"> </td>
                                </tr>
                                <tr>
                                    <td align=\"center\" height=\"20\" style=\"font-family:&#39;Quicksand&#39;,&#39;Arial&#39;,Helvetica,sans-serif; color:#888888;font-size:13px;font-weight:normal;text-align: center;\">
                                        <span style=\"color: rgb(136, 136, 136); text-decoration: none;\">
                                            </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td height=\"10\" style=\"font-size:1px; line-height:1px; mso-hide: all;\"> </td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                </tbody></table>
                
            </div></td>
    </tr><tr>

        <td align=\"center\" valign=\"top\">

            <div style=\"background-color: rgb(255, 255, 255); border-radius: 0px;\">
                
                <!--[if mso]>
                <table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" style=\"width:100%;\">
                <tr>
                <![endif]-->
                
                <!--[if mso]>
                <td valign=\"top\" width=\"590\" style=\"width:590px;\">
                <![endif]-->
                <table class=\"rnb-del-min-width\" width=\"100%\" cellpadding=\"0\" border=\"0\" cellspacing=\"0\" style=\"min-width:590px;\" name=\"Layout_1\" id=\"Layout_1\">
                <tbody><tr>
                    <td class=\"rnb-del-min-width\" align=\"center\" valign=\"top\" style=\"min-width:590px;\">
                        <a href=\"#\" name=\"Layout_1\"></a>
                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"rnb-container\" bgcolor=\"#ffffff\" style=\"background-color: rgb(255, 255, 255); border-radius: 0px; padding-left: 20px; padding-right: 20px; border-collapse: separate;\">
                            <tbody><tr>
                                <td height=\"20\" style=\"font-size:1px; line-height:20px; mso-hide: all;\"> </td>
                            </tr>
                            <tr>
                                <td valign=\"top\" class=\"rnb-container-padding\" align=\"left\">
                                    <table width=\"100%\" cellpadding=\"0\" border=\"0\" align=\"center\" cellspacing=\"0\">
                                        <tbody><tr>
                                            <td valign=\"top\" align=\"center\">
                                                <table cellpadding=\"0\" border=\"0\" align=\"center\" cellspacing=\"0\" class=\"logo-img-center\"> 
                                                    <tbody><tr>
                                                        <td valign=\"middle\" align=\"center\" style=\"line-height: 1px;\">
                                                            <div style=\"border-top:0px None #000;border-right:0px None #000;border-bottom:0px None #000;border-left:0px None #000;display:inline-block; \" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><div><img width=\"550\" vspace=\"0\" hspace=\"0\" border=\"0\" alt=\"Sendinblue\" style=\"float: left;max-width:550px;display:block;\" class=\"rnb-logo-img\" src=\"https://img-cache.net/im/2574191/da93912ca78c9127fdf79ab12be0863978870e21371eb65a901ca291dea4baf8.png?e=fS54OTC_Twa8-tqi_LdUyJXEe0FLS4RPmTgBl7Fs8Dee3Ryzg3yuXIjmVJr9HfrvJq03QMgtlTCdyrRPH8FaakzPPnixBT-UJ9xVi9PbRKUWkvI6ufIP3pt2JwSFCJ207tcmakEe6CQ0VhXkJJQtjC82j6DcSS1VVvH_Uu8BIENYzJKN4lJNgUoz28-ynpDOYcpLBOaA-0u4nDSFHIDmQiJWgvVQKWn62DJL\" sib_link_id=\"0\"/></div></div></td>
                                                    </tr>
                                                </tbody></table>
                                                </td>
                                        </tr>
                                    </tbody></table></td>
                            </tr>
                            <tr>
                                <td height=\"20\" style=\"font-size:1px; line-height:20px; mso-hide: all;\"> </td>
                            </tr>
                        </tbody></table>
                    </td>
                </tr>
            </tbody></table>
            <!--[if mso]>
                </td>
                <![endif]-->
                
                <!--[if mso]>
                </tr>
                </table>
                <![endif]-->
            
        </div></td>
    </tr><tr>

        <td align=\"center\" valign=\"top\">

            <table class=\"rnb-del-min-width\" width=\"100%\" cellpadding=\"0\" border=\"0\" cellspacing=\"0\" style=\"min-width:590px;\" name=\"Layout_51\" id=\"Layout_51\">
                <tbody><tr>
                    <td class=\"rnb-del-min-width\" valign=\"top\" align=\"center\" style=\"min-width:590px;\">
                        <a href=\"#\" name=\"Layout_51\"></a>
                        <table width=\"100%\" cellpadding=\"0\" border=\"0\" height=\"7\" cellspacing=\"0\">
                            <tbody><tr>
                                <td valign=\"top\" height=\"7\">
                                    <img width=\"20\" height=\"7\" style=\"display:block; max-height:7px; max-width:20px;\" alt=\"\" src=\"https://img-cache.net/im/2574191/407248b07fd3d8f7e8dc20606539d2491090c41e91c9c6c9f7e1162bd7833734.gif?e=-PqlJURmkW5M4XI4AKCYPgp1QrrPCEmOAYu95GvARH2daDEMWS8uvux4CXcOc2g8dS4FswCblYUFI-Uu6KYEO4Y2itywiSvpxnEv7I2QoiAf5AuWVh1l5258p2PYyq9NVux4JOa2h6sUaYUl_Qj4eq3zw6R_Uqs-FXsbdBRlxvc82THY0JOsMdwGuA1WcxarD4cKMEWhMqswvIg\" sib_link_id=\"1\"/>
                                </td>
                            </tr>
                        </tbody></table>
                    </td>
                </tr>
            </tbody></table>
            </td>
    </tr><tr>

        <td align=\"center\" valign=\"top\">

            <div style=\"background-color: rgb(255, 255, 255); border-radius: 0px;\">
            
                <!--[if mso]>
                <table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" style=\"width:100%;\">
                <tr>
                <![endif]-->
                
                <!--[if mso]>
                <td valign=\"top\" width=\"590\" style=\"width:590px;\">
                <![endif]-->
                <table class=\"rnb-del-min-width\" width=\"100%\" cellpadding=\"0\" border=\"0\" cellspacing=\"0\" style=\"min-width:100%;\" name=\"Layout_19\">
                <tbody><tr>
                    <td class=\"rnb-del-min-width\" align=\"center\" valign=\"top\">
                        <a href=\"#\" name=\"Layout_19\"></a>
                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"rnb-container\" bgcolor=\"#ffffff\" style=\"background-color: rgb(255, 255, 255); padding-left: 20px; padding-right: 20px; border-collapse: separate; border-radius: 0px; border-bottom: 0px none rgb(245, 0, 0);\">

                                        <tbody><tr>
                                            <td height=\"20\" style=\"font-size:1px; line-height:20px; mso-hide: all;\"> </td>
                                        </tr>
                                        <tr>
                                            <td valign=\"top\" class=\"rnb-container-padding\" align=\"left\">

                                                <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"rnb-columns-container\">
                                                    <tbody><tr>
                                                        <th class=\"rnb-force-col\" style=\"text-align: left; font-weight: normal; padding-right: 0px;\" valign=\"top\">

                                                            <table border=\"0\" valign=\"top\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" align=\"left\" class=\"rnb-col-1\">

                                                                <tbody><tr>
                                                                    <td style=\"font-size:14px; font-family:&#39;Open Sans&#39;,&#39;Arial&#39;,Helvetica,sans-serif, sans-serif; color:#3c4858;\"><div style=\"line-height: 24px; text-align: justify;\"><span style=\"background-color: transparent;\">Dear Customer,</span></div>

<div style=\"line-height: 24px; text-align: justify;\"><br/>
<span style=\"background-color: transparent;\">

We have good news for you! Our planning team has validated the network in your area and we are ready to schedule the connection for your property <b>".$wlead['address'].
" </b>.


</span></div>

<div style=\"line-height: 24px; text-align: justify;\"><br/>
<span style=\"background-color: transparent;\">Our installations team will soon get in touch to book the installation at a date and time convenient for you.</span></div>

<div style=\"line-height: 24px; text-align: justify;\"><br/>
<span style=\"background-color: transparent;\">Should you have any questions please contact <a href=\"mailto:customer.support@lazerspeed.com\" style=\"text-decoration: underline; color: rgb(0, 146, 255);\">customer.support@lazerspeed.com</a></span></div>

<div style=\"line-height: 24px; text-align: justify;\"><br/>
<span style=\"background-color: transparent;\">Thanks,<br/>
<strong>Lazer Team</strong></span></div>
</td>
                                                                </tr>
                                                                </tbody></table>

                                                            </th></tr>
                                                </tbody></table></td>
                                        </tr>
                                        <tr>
                                            <td height=\"20\" style=\"font-size:1px; line-height:20px; mso-hide: all;\"> </td>
                                        </tr>
                                    </tbody></table>
                    </td>
                </tr>
            </tbody></table><!--[if mso]>
                </td>
                <![endif]-->
                
                <!--[if mso]>
                </tr>
                </table>
                <![endif]-->

            </div></td>
    </tr><tr>

        <td align=\"center\" valign=\"top\">

            <table class=\"rnb-del-min-width\" width=\"100%\" cellpadding=\"0\" border=\"0\" cellspacing=\"0\" style=\"min-width:590px;\" name=\"Layout_54\" id=\"Layout_54\">
                <tbody><tr>
                    <td class=\"rnb-del-min-width\" valign=\"top\" align=\"center\" style=\"min-width:590px;\">
                        <a href=\"#\" name=\"Layout_54\"></a>
                        <table width=\"100%\" cellpadding=\"0\" border=\"0\" height=\"8\" cellspacing=\"0\">
                            <tbody><tr>
                                <td valign=\"top\" height=\"8\">
                                    <img width=\"20\" height=\"8\" style=\"display:block; max-height:8px; max-width:20px;\" alt=\"\" src=\"https://img-cache.net/im/2574191/407248b07fd3d8f7e8dc20606539d2491090c41e91c9c6c9f7e1162bd7833734.gif?e=QyVaqbeJekRiq714prgLHr_yl9OONnd6YSLfHcCfJwyNksXKIvd4q8PnjIgiwdhMfvloXJlVLm3CF6CW7kpDkbHopTkjIcNAsfrI3cNZ9DkhL9VNoc9Zi6bF033Rrlm3S50A3O4h34mYiYP3ATNpbpp9zLEgAy3wBE_QkzNS_tJi5pX89jnMmm5_sbiHt6kygCoSj-HhpMTmEL4\" sib_link_id=\"1\"/>
                                </td>
                            </tr>
                        </tbody></table>
                    </td>
                </tr>
            </tbody></table>
            </td>
    </tr><tr>

        <td align=\"center\" valign=\"top\">

            <div style=\"background-color: rgb(245, 0, 0);\">
                
                <table class=\"rnb-del-min-width rnb-tmpl-width\" width=\"100%\" cellpadding=\"0\" border=\"0\" cellspacing=\"0\" style=\"min-width:590px;\" name=\"Layout_\" id=\"Layout_\">
                    <tbody><tr>
                        <td class=\"rnb-del-min-width\" align=\"center\" valign=\"top\" bgcolor=\"#f50000\" style=\"min-width:590px; background-color: #f50000;\">
                            <table width=\"590\" class=\"rnb-container\" cellpadding=\"0\" border=\"0\" align=\"center\" cellspacing=\"0\" bgcolor=\"#f50000\" style=\"background-color: rgb(245, 0, 0);\">
                                <tbody><tr>
                                    <td height=\"20\" style=\"font-size:1px; line-height:20px; mso-hide: all;\"> </td>
                                </tr>
                                <tr>
                                    <th class=\"rnb-force-col\" style=\"padding-bottom:20px; padding-right:20px; padding-left:20px; mso-padding-alt: 0 20px 20px 20px; font-weight: normal;\" valign=\"top\">

                                        <table border=\"0\" valign=\"top\" cellspacing=\"0\" cellpadding=\"0\" width=\"264\" align=\"center\" class=\"rnb-col-2 social-text-spacing\" style=\"border-bottom:0;\">

                                            <tbody><tr>
                                                <td valign=\"top\">
                                                    <table cellpadding=\"0\" border=\"0\" align=\"center\" cellspacing=\"0\" class=\"rnb-btn-col-content\">
                                                        <tbody><tr>
                                                            <td valign=\"middle\" align=\"center\" style=\"font-size:14px; font-family:&#39;Quicksand&#39;,&#39;Arial&#39;,Helvetica,sans-serif; color:#888888;\" class=\"rnb-text-center\">
                                                                <div><div><span style=\"color:#FFFFFF;\"><span style=\"font-size:20px;\">Follow us on social media!</span></span></div>
</div> 
                                                            </td></tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                            </tbody></table>
                                        </th>
                                    </tr><tr>
                                    <td valign=\"top\" class=\"rnb-container-padding\" style=\"font-size: 14px; font-family: &#39;Quicksand&#39;,&#39;Arial&#39;,Helvetica,sans-serif; color: #888888;\" align=\"center\">

                                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"rnb-columns-container\">
                                            <tbody><tr>
                                                <th class=\"rnb-force-col rnb-social-width2\" valign=\"top\" style=\"mso-padding-alt: 0 20px 0 20px; padding-right: 28px; padding-left: 28px;\">

                                                    <table border=\"0\" valign=\"top\" cellspacing=\"0\" cellpadding=\"0\" width=\"533\" align=\"center\" class=\"rnb-last-col-2\">

                                                        <tbody><tr>
                                                            <td valign=\"top\">
                                                                <table cellpadding=\"0\" border=\"0\" cellspacing=\"0\" class=\"rnb-social-align2\" align=\"center\">
                                                                    <tbody><tr>
                                                                        <td valign=\"middle\" style=\"line-height: 0px;\" class=\"rnb-text-center\" ng-init=\"width=setSocialIconsBlockWidth(item)\" width=\"533\" align=\"center\">
                                                                            <!--[if mso]>
                                                                            <table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                                                                            <tr>
                                                                            <![endif]-->

                                                                                <div class=\"rnb-social-center\" style=\"display: inline-block;\">
                                                                                <!--[if mso]>
                                                                                <td align=\"center\" valign=\"top\">
                                                                                <![endif]-->
                                                                                    <table align=\"left\" style=\"float:left; display: inline-block\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                                                                                        <tbody><tr>
                                                                                            <td style=\"padding:0px 5px 5px 0px; mso-padding-alt: 0px 4px 5px 0px;\" align=\"left\">
                                                                                <span style=\"color:#ffffff; font-weight:normal;\">
                                                                                    <a target=\"_blank\" href=\"https://facebook.com/lazertelecom/?utm_source=sendinblue&amp;utm_campaign=Status 40&amp;utm_medium=email\"><img alt=\"Facebook\" border=\"0\" hspace=\"0\" vspace=\"0\" style=\"vertical-align:top;\" target=\"_blank\" src=\"https://img-cache.net/im/2574191/7aca3ddba89ce601207ad1e176206a9d8003861862055733736fdc6427b5a1e6.png?e=58x9MYt0b7Ogzg1YRmF624VkxZZ82Lc21RY4ccN2quLf6vIxcWm-ouHWMP1KY-BAGAUTXlG4DjGXvKDAwhp8QIGXSoEQsqDmlht0davyOmu_TKMBEfWzOuixOWGbsLuYrLFiMml6ocwWUr31foMgsoiPHvjT88L1y1Q7yHxm4we_GBMhc5zVd_s4TGaBb-F6wLHZZz0\" sib_link_id=\"2\"/></a></span>
                                                                                            </td></tr></tbody></table>
                                                                                <!--[if mso]>
                                                                                </td>
                                                                                <![endif]-->
                                                                                </div><div class=\"rnb-social-center\" style=\"display: inline-block;\">
                                                                                <!--[if mso]>
                                                                                <td align=\"center\" valign=\"top\">
                                                                                <![endif]-->
                                                                                    <table align=\"left\" style=\"float:left; display: inline-block\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                                                                                        <tbody><tr>
                                                                                            <td style=\"padding:0px 5px 5px 0px; mso-padding-alt: 0px 4px 5px 0px;\" align=\"left\">
                                                                                <span style=\"color:#ffffff; font-weight:normal;\">
                                                                                    <a target=\"_blank\" href=\"https://www.linkedin.com/company/lazer-telecom/?utm_source=sendinblue&amp;utm_campaign=Status 40&amp;utm_medium=email\"><img alt=\"LinkedIn\" border=\"0\" hspace=\"0\" vspace=\"0\" style=\"vertical-align:top;\" target=\"_blank\" src=\"https://img-cache.net/im/2574191/842c0f4aa25ca475174c0860039c2e9d9e2046a904d3a49c6d12983806e3978d.png?e=KIpHOwCBCfvar76n0qRvcrwCX7ZeuBa7zTJMXMC9IXLWbaN_XXbZTEaocT9N8wbZ5ypEtqyHJoo7s9o9XvzX7bXPMEWCL37YxLgUr3mqumNts6nleM0i_i9IwxJVNHawvwesW7i9-6PinacuepwHE2wHNxZT3Prolt5FJSNUCgmxVtmFSYEbD1PrmJUcqMj95T35cz8\" sib_link_id=\"3\"/></a></span>
                                                                                            </td></tr></tbody></table>
                                                                                <!--[if mso]>
                                                                                </td>
                                                                                <![endif]-->
                                                                                </div><div class=\"rnb-social-center\" style=\"display: inline-block;\">
                                                                                <!--[if mso]>
                                                                                <td align=\"center\" valign=\"top\">
                                                                                <![endif]-->
                                                                                    <table align=\"left\" style=\"float:left; display: inline-block\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                                                                                        <tbody><tr>
                                                                                            <td style=\"padding:0px 5px 5px 0px; mso-padding-alt: 0px 4px 5px 0px;\" align=\"left\">
                                                                                <span style=\"color:#ffffff; font-weight:normal;\">
                                                                                    <a target=\"_blank\" href=\"https://www.instagram.com/lazertelecom/?utm_source=sendinblue&amp;utm_campaign=Status 40&amp;utm_medium=email\"><img alt=\"Instagram\" border=\"0\" hspace=\"0\" vspace=\"0\" style=\"vertical-align:top;\" target=\"_blank\" src=\"https://img-cache.net/im/2574191/cf03b9e3d1d2bc3ab40fa940492e6f47f684824cd204f2670c1da9fad66aae30.png?e=UvfM6080znUkqqFUBrjXoClN7xUPemoHPTQNrVSLjZGHgPCIyFDcbp1ZN4G87T-LkLzPK6YeUayFjc5LgSkIrHxUA6yrybQB_M-idNFYrFPT-GVnD_GC9KCmZda2G-5A8ItXkDhNxay0wfDn3pA7AA5KtcheJlZx64kjC9KjKvYZhQXlUv0oagYEIiA8Roeq4QW5Csc\" sib_link_id=\"4\"/></a></span>
                                                                                            </td></tr></tbody></table>
                                                                                <!--[if mso]>
                                                                                </td>
                                                                                <![endif]-->
                                                                                </div><div class=\"rnb-social-center\" style=\"display: inline-block;\">
                                                                                <!--[if mso]>
                                                                                <td align=\"center\" valign=\"top\">
                                                                                <![endif]-->
                                                                                    <table align=\"left\" style=\"float:left; display: inline-block\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                                                                                        <tbody><tr>
                                                                                            <td style=\"padding:0px 5px 5px 0px; mso-padding-alt: 0px 4px 5px 0px;\" align=\"left\">
                                                                                <span style=\"color:#ffffff; font-weight:normal;\">
                                                                                    <a target=\"_blank\" href=\"https://www.youtube.com/channel/UCs8a4t-kbRtEaZpMEFeLxfQ?utm_source=sendinblue&amp;utm_campaign=Status 40&amp;utm_medium=email\"><img alt=\"YouTube\" border=\"0\" hspace=\"0\" vspace=\"0\" style=\"vertical-align:top;\" target=\"_blank\" src=\"https://img-cache.net/im/2574191/182d040df53ea01cb530f1fb4ee284a704c70a2035dbd7e8e1149c0eae5523b5.png?e=fz6tAzdl2_tZwO_ZabvRIacdc5aS-1GemZKxw_DEYGk-VbzH91WDhFGPIj1ZZYJE9IQw38GonEYAIcVp-LfruSP8cTaLpzbjHb93LpVyLXDofTcY6q-iJ7BhlGv-dHyjsPBBuCga7NtFyjWDPjXdOYeIr_TL-fUNjPPCiTRS34A0wBJZImeMlRunnaW7NGU_V__KJRw\" sib_link_id=\"5\"/></a></span>
                                                                                            </td></tr></tbody></table>
                                                                                <!--[if mso]>
                                                                                </td>
                                                                                <![endif]-->
                                                                                </div><!--[if mso]>
                                                                            </tr>
                                                                            </table>
                                                                            <![endif]-->
                                                                        </td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        </tbody></table>
                                                    </th>
                                            </tr>
                                        </tbody></table></td>
                                </tr>
                                <tr>
                                    <td height=\"20\" style=\"font-size:1px; line-height:20px; mso-hide: all;\"> </td>
                                </tr>
                            </tbody></table>

                        </td>
                    </tr></tbody></table>
                
            </div></td>
    </tr><tr>
        <td align=\"center\" valign=\"top\">
            <table class=\"rnb-del-min-width\" width=\"100%\" cellpadding=\"0\" border=\"0\" cellspacing=\"0\" style=\"min-width:590px;\" name=\"Layout_8330\" id=\"Layout_8330\">
                <tbody><tr>
                    <td class=\"rnb-del-min-width\" valign=\"top\" align=\"center\" style=\"min-width:590px;\">
                        <a href=\"#\" name=\"Layout_8330\"></a>
                        <table width=\"100%\" cellpadding=\"0\" border=\"0\" height=\"8\" cellspacing=\"0\">
                            <tbody><tr>
                                <td valign=\"top\" height=\"8\">
                                    <img width=\"20\" height=\"8\" style=\"display:block; max-height:8px; max-width:20px;\" alt=\"\" src=\"https://img-cache.net/im/2574191/407248b07fd3d8f7e8dc20606539d2491090c41e91c9c6c9f7e1162bd7833734.gif?e=hvpfWZDHNZXKUveWN86Yd7M7TH7hPrrupS3DZCmwp8abqJmbb3NmpJN83ZDg-_tyKCW8WGpCf3Uuba0LqU3nZu5Q-77yuJOslSHRXIKjFp_pQgZ9RryWB_dyszJZcwKwZ27aUMV_tCPYZa2Zk3FQ6Ff6NEyF1NlX9IGRuYFW6UYFDsSWetv2koALYaz5vhu-RZzN01vizz0yHhg\" sib_link_id=\"1\"/>
                                </td>
                            </tr>
                        </tbody></table>
                    </td>
                </tr>
            </tbody></table>
            </td>
    </tr><tr>

        <td align=\"center\" valign=\"top\">

            <div style=\"background-color: rgb(55, 55, 55);\">
                
                <table class=\"rnb-del-min-width rnb-tmpl-width\" width=\"100%\" cellpadding=\"0\" border=\"0\" cellspacing=\"0\" style=\"min-width:590px;\" name=\"Layout_52\" id=\"Layout_52\">
                    <tbody><tr>
                        <td class=\"rnb-del-min-width\" align=\"center\" valign=\"top\" bgcolor=\"#373737\" style=\"min-width:590px; background-color: #373737;\">
                            <table width=\"590\" class=\"rnb-container\" cellpadding=\"0\" border=\"0\" align=\"center\" cellspacing=\"0\" bgcolor=\"#373737\" style=\"background-color: rgb(55, 55, 55);\">
                                <tbody><tr>
                                    <td height=\"0\" style=\"font-size:1px; line-height:0px; mso-hide: all;\"> </td>
                                </tr>
                                <tr>
                                    <td valign=\"top\" class=\"rnb-container-padding\" style=\"font-size: 14px; font-family: &#39;Quicksand&#39;,&#39;Arial&#39;,Helvetica,sans-serif; color: #888888;\" align=\"center\">

                                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"rnb-columns-container\">
                                            <tbody><tr>
                                                <th class=\"rnb-force-col rnb-social-width2\" valign=\"top\" style=\"mso-padding-alt: 0 20px 0 20px; padding-right: 28px; padding-left: 28px;\">

                                                    <table border=\"0\" valign=\"top\" cellspacing=\"0\" cellpadding=\"0\" width=\"533\" align=\"center\" class=\"rnb-last-col-2\">

                                                        <tbody><tr>
                                                            <td valign=\"top\">
                                                                <table cellpadding=\"0\" border=\"0\" cellspacing=\"0\" class=\"rnb-social-align2\" align=\"center\">
                                                                    <tbody><tr>
                                                                        <td valign=\"middle\" style=\"line-height: 0px;\" class=\"rnb-text-center\" ng-init=\"width=setSocialIconsBlockWidth(item)\" width=\"533\" align=\"center\">
                                                                            <!--[if mso]>
                                                                            <table align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                                                                            <tr>
                                                                            <![endif]-->

                                                                                <!--[if mso]>
                                                                            </tr>
                                                                            </table>
                                                                            <![endif]-->
                                                                        </td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        </tbody></table>
                                                    </th>
                                            </tr>
                                        </tbody></table></td>
                                </tr>
                                <tr>
                                    <th class=\"rnb-force-col\" style=\"padding-top:15px; padding-right:20px; padding-left:20px; mso-padding-alt: 20px 0 0 20px; font-weight: normal;\" valign=\"top\">

                                        <table border=\"0\" valign=\"top\" cellspacing=\"0\" cellpadding=\"0\" width=\"264\" align=\"center\" class=\"rnb-col-2 social-text-spacing\" style=\"border-bottom:0;\">

                                            <tbody><tr>
                                                <td valign=\"top\">
                                                    <table cellpadding=\"0\" border=\"0\" align=\"center\" cellspacing=\"0\" class=\"rnb-btn-col-content\">
                                                        <tbody><tr>
                                                            <td valign=\"middle\" align=\"center\" style=\"font-size:14px; font-family:&#39;Quicksand&#39;,&#39;Arial&#39;,Helvetica,sans-serif; color:#888888;\" class=\"rnb-text-center\">
                                                                <div><div><span style=\"font-size:10px;\"><a href=\"http://www.lazertelecom.com\" style=\"text-decoration: underline; color: rgb(102, 102, 102);\"><span style=\"color:#FFFFFF;\">www.lazertelecom.com</span></a></span></div>
</div> 
                                                            </td></tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                            </tbody></table>
                                        </th>
                                    </tr><tr>
                                    <td height=\"10\" style=\"font-size:1px; line-height:10px; mso-hide: all;\"> </td>
                                </tr>
                            </tbody></table>

                        </td>
                    </tr></tbody></table>
                
            </div></td>
    </tr></tbody></table>
            <!--[if gte mso 9]>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                        </td>
        </tr>
        </tbody></table>

<div style=\"color: #727272; font-size: 10px;\"><center>Lead_id: $lead_id </center></div></body></html>
";

