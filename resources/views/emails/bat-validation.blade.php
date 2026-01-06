<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="x-apple-disable-message-reformatting" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>BAT a valider</title>

    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <style type="text/css">
        table { border-collapse: collapse; }
        td, th, div, p, a, h1, h2, h3, h4, h5, h6 { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
    <![endif]-->

    <style type="text/css">
        body {
            margin: 0 !important;
            padding: 0 !important;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
        }
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        a {
            text-decoration: none;
        }
        @media only screen and (max-width: 620px) {
            .mobile-padding {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .mobile-full-width {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <!-- Preheader (hidden text for email preview) -->
    <div style="display: none; max-height: 0; overflow: hidden; mso-hide: all;">
        Un nouveau BAT est pret pour validation : {{ $bat->title }}
    </div>

    <!-- Main wrapper table -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 10px;">

                <!-- Email container -->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" class="mobile-full-width" style="max-width: 600px; background-color: #ffffff;">

                    <!-- Header with KEYMEX branding -->
                    <tr>
                        <td align="center" style="background-color: #C41E3A; padding: 30px 40px;">
                            <!--[if mso]>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                            <td align="center" style="padding: 0;">
                            <![endif]-->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 800; letter-spacing: -1px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">KEYMEX</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 5px;">
                                        <p style="margin: 0; color: #ffffff; opacity: 0.8; font-size: 11px; text-transform: uppercase; letter-spacing: 3px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Marketing</p>
                                    </td>
                                </tr>
                            </table>
                            <!--[if mso]>
                            </td>
                            </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>

                    <!-- Main content -->
                    <tr>
                        <td class="mobile-padding" style="padding: 40px;">

                            <!-- Greeting -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding-bottom: 20px;">
                                        <h2 style="margin: 0; color: #1a1a1a; font-size: 22px; font-weight: 600; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                                            Bonjour {{ $bat->advisor_name }},
                                        </h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 30px;">
                                        <p style="margin: 0; color: #4a4a4a; font-size: 16px; line-height: 24px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                                            Un nouveau BAT (Bon A Tirer) est pret pour votre validation. Merci de le consulter et de nous donner votre retour.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- BAT Details Card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                                <tr>
                                    <td style="padding: 25px;">

                                        <!-- BAT Title -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding-bottom: 15px; border-bottom: 2px solid #C41E3A;">
                                                    <h3 style="margin: 0; color: #C41E3A; font-size: 18px; font-weight: 600; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                                                        {{ $bat->title }}
                                                    </h3>
                                                </td>
                                            </tr>
                                        </table>

                                        @if($bat->description)
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding-top: 15px; padding-bottom: 15px;">
                                                    <p style="margin: 0; color: #666666; font-size: 14px; line-height: 21px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                                                        {{ $bat->description }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif

                                        <!-- Details list -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 10px;">
                                            @if($bat->supportType)
                                            <tr>
                                                <td width="120" style="padding: 8px 0; color: #888888; font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Type :</td>
                                                <td style="padding: 8px 0; color: #333333; font-size: 13px; font-weight: 600; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">{{ $bat->supportType->name }}</td>
                                            </tr>
                                            @endif
                                            @if($bat->format)
                                            <tr>
                                                <td width="120" style="padding: 8px 0; color: #888888; font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Format :</td>
                                                <td style="padding: 8px 0; color: #333333; font-size: 13px; font-weight: 600; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">{{ $bat->format->name }}</td>
                                            </tr>
                                            @endif
                                            @if($bat->quantity)
                                            <tr>
                                                <td width="120" style="padding: 8px 0; color: #888888; font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Quantite :</td>
                                                <td style="padding: 8px 0; color: #333333; font-size: 13px; font-weight: 600; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">{{ number_format($bat->quantity, 0, ',', ' ') }} ex.</td>
                                            </tr>
                                            @endif
                                            @if($bat->grammage)
                                            <tr>
                                                <td width="120" style="padding: 8px 0; color: #888888; font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Grammage :</td>
                                                <td style="padding: 8px 0; color: #333333; font-size: 13px; font-weight: 600; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">{{ $bat->grammage }}</td>
                                            </tr>
                                            @endif
                                            @if($bat->delivery_time)
                                            <tr>
                                                <td width="120" style="padding: 8px 0; color: #888888; font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">Delai :</td>
                                                <td style="padding: 8px 0; color: #333333; font-size: 13px; font-weight: 600; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">{{ $bat->delivery_time }}</td>
                                            </tr>
                                            @endif
                                        </table>

                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding: 35px 0 30px 0;">
                                        <!--[if mso]>
                                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $bat->validation_url }}" style="height:50px;v-text-anchor:middle;width:250px;" arcsize="10%" stroke="f" fillcolor="#C41E3A">
                                        <w:anchorlock/>
                                        <center style="color:#ffffff;font-family:'Segoe UI',Tahoma,sans-serif;font-size:16px;font-weight:bold;">Voir et valider le BAT</center>
                                        </v:roundrect>
                                        <![endif]-->
                                        <!--[if !mso]><!-->
                                        <a href="{{ $bat->validation_url }}" style="display: inline-block; background-color: #C41E3A; color: #ffffff; text-decoration: none; padding: 16px 40px; font-size: 16px; font-weight: 600; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; border-radius: 8px; mso-hide: all;">
                                            Voir et valider le BAT
                                        </a>
                                        <!--<![endif]-->
                                    </td>
                                </tr>
                            </table>

                            <!-- Link info -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 10px;">
                                        <p style="margin: 0; color: #888888; font-size: 13px; line-height: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                                            Ce lien est valide pendant 30 jours.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0; color: #888888; font-size: 12px; line-height: 18px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                                            Si le bouton ne fonctionne pas, copiez ce lien :<br />
                                            <a href="{{ $bat->validation_url }}" style="color: #C41E3A; word-break: break-all;">{{ $bat->validation_url }}</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef; padding: 25px 40px;">
                            <p style="margin: 0; color: #888888; font-size: 12px; line-height: 18px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                                Cet email a ete envoye automatiquement par l'equipe Marketing KEYMEX.<br />
                                Merci de ne pas repondre directement a cet email.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Email container -->

            </td>
        </tr>
    </table>
</body>
</html>
