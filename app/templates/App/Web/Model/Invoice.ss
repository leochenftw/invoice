<!DOCTYPE html>
<!--[if !IE]><!-->
<html lang="$ContentLocale">
<!--<![endif]-->
<!--[if IE 6 ]><html lang="$ContentLocale" class="ie ie6"><![endif]-->
<!--[if IE 7 ]><html lang="$ContentLocale" class="ie ie7"><![endif]-->
<!--[if IE 8 ]><html lang="$ContentLocale" class="ie ie8"><![endif]-->
<head>
    $SiteConfig.GoogleSiteVerificationCode.RAW
    <% base_tag %>
    <title><% if $URLSegment == 'home' %><% if $MetaTitle %>$MetaTitle<% else %>$SiteConfig.Title<% end_if %><% else %><% if $MetaTitle %>$MetaTitle<% else %>$Title<% end_if %><% end_if %></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    $MetaTags(false)
    <style>
        strong {
            font-weight: bold;
        }
    </style>
</head>
<body <% if $i18nScriptDirection %>dir="$i18nScriptDirection"<% end_if %>>
<div id="app" class="v-application v-application--is-ltr theme--light">
    <main data-v-6000fa2c="" class="v-main" data-booted="true">
        <div class="v-main__wrap">
            <div data-v-6000fa2c="" class="container root container--fluid">
                <section data-v-6000fa2c="" class="section">
                    <div class="container">
                    <form novalidate="novalidate" method="post" class="v-form invoice-form">
                        <div class="row" style="display: block; margin-left: 0; margin-right: 0;">
                            <div class="col col-9" style="float: left; width: 75%;">
                                <h2 class="h2 entity color--purple" style="margin: 0;">$EntityTitle</h2>
                                <div class="entity-address content">$EntityAddress</div>
                            </div>
                            <div class="col col-3" style="float: left; width: 25%;">
                                <p class="tax-no color--purple" style="margin: 0;"><% if $isGSTRegistered %>GST<% else %>IRD<% end_if %> No. $TaxNumber</p>
                                <% if not $isGSTRegistered %>
                                    <p style="margin: 0;"><strong>Not GST Registered</strong></p>
                                <% end_if %>
                            </div>
                            <div style="width: 100%; clear: both;"></div>
                        </div>
                        <div class="row" style="display: block; margin-left: 0; margin-right: 0;">
                            <div class="pb-0 col col-12">
                                <h2 class="h1 color--dark-blue" style="margin: 0;">Invoice</h2>
                            </div>
                            <div style="float: left; width: 75%;" class="col col-9">
                                <div class="row" style="display: block; margin-left: 0; margin-right: 0;">
                                    <div style="float: left; width: 50%;" class="pt-0 pb-0 pink--text col">$Content</div>
                                    <div style="float: left; width: 50%;" class="pt-0 pb-0 col">$SideNote</div>
                                    <div style="clear: both; width: 100%;" class="col">
                                        <h3 style="margin: 0;" class="h4">Invoice to</h3>
                                        <p style="margin-top: 0;" class="h3">Bridget Williams Books</p>
                                        <div class="content">32 Salamanca Road<br>
                                            Kelburn<br>
                                            Wellington 6012
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="float: left; width: 25%;" class="col col-3">
                                <h3 class="h3" style="margin: 0;">Invoice #</h3>
                                <p style="margin-top: 0;">$Title</p>
                                <h3 style="margin: 0;" class="h3">Due date</h3>
                                <p style="margin-top: 0;">$Due</p>
                            </div>
                        </div>
                        <hr role="separator" style="margin-bottom: 0;" aria-orientation="horizontal" class="v-divider theme--light">
                        <div class="v-data-table theme--light">
                            <div class="v-data-table__wrapper">
                                <table style="width: 100%; border-spacing: 0;">
                                    <thead class="v-data-table-header">
                                        <tr>
                                            <th style="border-bottom: 1 solid #d8d8d8; padding: 0 16px; height: 48px; font-size: 12px; line-height: 18px; font-family: 'Roboto',sans-serif; text-align: left;" role="columnheader" scope="col" aria-label="Description"><span>Description</span></th>
                                            <th style="border-bottom: 1 solid #d8d8d8; padding: 0 16px; height: 48px; font-size: 12px; line-height: 18px; font-family: 'Roboto',sans-serif; text-align: right;" role="columnheader" scope="col" aria-label="Hours" class="text-end"><span>Hours</span></th>
                                            <th style="border-bottom: 1 solid #d8d8d8; padding: 0 16px; height: 48px; font-size: 12px; line-height: 18px; font-family: 'Roboto',sans-serif; text-align: right;" role="columnheader" scope="col" aria-label="Hourly Rate" class="text-end"><span>Hourly Rate</span></th>
                                            <th style="border-bottom: 1 solid #d8d8d8; padding: 0 16px; height: 48px; font-size: 12px; line-height: 18px; font-family: 'Roboto',sans-serif; text-align: right;" role="columnheader" scope="col" aria-label="Total price" class="text-end"><span>Total price</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <% loop $StoredLogs %>
                                            <tr class="">
                                                <td style="border-bottom: 1 solid #d8d8d8; padding: 0 16px; height: 48px; font-size: 14px; line-height: 18px; font-family: 'Roboto',sans-serif; text-align: left;">$title</td>
                                                <td style="border-bottom: 1 solid #d8d8d8; padding: 0 16px; height: 48px; font-size: 14px; line-height: 18px; font-family: 'Roboto',sans-serif; text-align: right;">
                                                    $Top.numberFormatter($hours)
                                                </td>
                                                <td style="border-bottom: 1 solid #d8d8d8; padding: 0 16px; height: 48px; font-size: 14px; line-height: 18px; font-family: 'Roboto',sans-serif; text-align: right;">
                                                    ${$Top.numberFormatter($hourly_rate)}
                                                </td>
                                                <td style="border-bottom: 1 solid #d8d8d8; padding: 0 16px; height: 48px; font-size: 14px; line-height: 18px; font-family: 'Roboto',sans-serif; text-align: right;">
                                                    ${$Top.numberFormatter($sum)}
                                                </td>
                                            </tr>
                                        <% end_loop %>
                                        <tr>
                                            <td colspan="3"  style="border-bottom: 1 solid #d8d8d8; padding: 0 16px; height: 48px; font-size: 14px; line-height: 18px; font-family: 'Roboto',sans-serif; text-align: right;" class="text-right">Subtotal</td>
                                            <td style="border-bottom: 1 solid #d8d8d8; padding: 0 16px; height: 48px; font-size: 14px; line-height: 18px; font-family: 'Roboto',sans-serif; text-align: right;" class="text-right">$Subtotal</td>
                                        </tr>
                                        <!---->
                                        <tr>
                                            <th style="color: #e91e63; font-size: 36px; font-weight: 500; height: 72px; font-family: 'Roboto',sans-serif; text-align: right;" colspan="4" class="title text-right grand-total">$Grandtotal</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row invoice-footer">
                            <div class="col col-12">
                                $FooterContent.RAW
                            </div>
                        </div>
                    </form>
                    <div class="v-menu">
                        <!---->
                    </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>
</body>
</html>
