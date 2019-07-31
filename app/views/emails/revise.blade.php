<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
$user = \Sentry::getUser();
?>
<style>
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 14px;
        color: #000;
    }
</style>
<table style="background-color: #d1d1d1; border-radius: 2px;
border:1px solid #d1d1d1; margin: 0 auto;" border="1"
       bordercolor="#d1d1d1" cellpadding="0" cellspacing="0"
       width="850">
    <tbody>
    <tr>
        <td style="border: none; padding: 0 44px 16px 44px;"
            bgcolor="#f7f7f7" valign="top" width="850">
            <h3>Запрос акта сверки</h3>

            <p>
                Клиент : {{ isset($user->firm)?$user->firm:'' }} запросил акт сверки за период с {{ $data->begin }}
                по {{ $data->end }}.
            </p>

            <p>
                Документ необходимо высталать на email {{ isset($user->email)?$user->email:'' }}
            </p>
        </td>
    </tr>
    </tbody>
</table>