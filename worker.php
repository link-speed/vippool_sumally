<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="robots" content="noindex, nofollow" />
<link rel="icon" href="./favicon.ico" />
<link rel="apple-touch-icon" size="144x144" href="apple-touch-icon.png" />
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<title>Worker稼働状況</title>
</head>
<body>
<h1Worker稼働状況></h1>

<h2>サマリー</h2>

<?php
//マイニングプールから必要な情報を取ってくる
$url_mona = "https://vippool.net/index.php?page=api&action=getusertransactions&api_key=[API Keys]&id=[YourID]";
$json_mona = file_get_contents($url_mona);
$mona_min = json_decode($json_mona,false ) ;
$mona_now = $mona_min->getusertransactions->data->transactionsummary->Credit;
$mona_debit = $mona_min->getusertransactions->data->transactionsummary->Debit_MP;
$mona_txfee = $mona_min->getusertransactions->data->transactionsummary->TXFee;
$mona_paid = $mona_now - ($mona_debit + $mona_txfee);

//現在の価格(Zaif)
$url_monalp = "https://api.zaif.jp/api/1/last_price/mona_jpy";
$json_monalp = file_get_contents($url_monalp);
$json_monalp = mb_convert_encoding($json_monalp, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$monalp_rate = json_decode($json_monalp,true);
$monalp_jpy = floatval($monalp_rate['last_price']);

//総額を計算
$minig_total = $mona_now * $monalp_jpy;

//日付を追加
$d =  date("Y-m-d H:i:s", time());

//採掘開始から計算('YYYY-MM-DD')
$sday = strtotime('2016-01-01');
$today = strtotime("today");

//ex. $from = strtotime("2017-11-13 16:00:00");
$from = strtotime("2017-12-01 12:00:00");
$to   = strtotime("now");         // 現在日時

//***************************************
// 日時の差を計算
//***************************************
function time_diff($time_from, $time_to)
{
    // 日時差を秒数で取得
    $dif = $time_to - $time_from;
    // 時間単位の差
    $dif_time = date("H:i:s", $dif);
    // 日付単位の差
    $dif_days = (strtotime(date("Y-m-d", $dif)) - strtotime("1970-01-01")) / 86400;
    return "{$dif_days}days {$dif_time}";
}


//ワーカー情報を取ってくる
$url_workers = "https://vippool.net/index.php?page=api&action=getuserworkers&api_key=[API Keys]&id=[YourID]"; //冗長なのでそのうち直します。
$json_workers = file_get_contents($url_workers);
$workers_min = json_decode($json_workers,true ) ;


print "<table data-role=\"table\" class=\"ui-responsive table-stroke\"><tbody><tr>";
print "<td>総採掘量</td><td>";
echo number_format($mona_now,8). ' mona';

print "</td></tr><tr><td>MONA現在価格<br/>(Zaif)</td><td>";
echo number_format($monalp_jpy). '円';

print "</td></tr><tr><td>採掘総額</td><td>";
echo number_format($minig_total). '円';

print "</td></tr><tr><td>経過日数</td><td>";
//経過日
//echo ($today - $sday) / (60 * 60 * 24). '日';

echo time_diff($from, $to);

print "</td></tr><tr><td>日次採掘量</td><td>";
//日次採掘量
echo $mona_now / (($today - $sday) / (60 * 60 * 24)). ' mona';

print "</td></tr><tr><td>日次採掘額</td><td>";
//日次採掘量
echo number_format($monalp_jpy * ($mona_now / (($today - $sday) / (60 * 60 * 24)))). '円';

print "</td></tr><tr><td>払出済み額</td><td>";
//払出済み額
echo number_format(($mona_debit + $mona_txfee),8). ' mona';

print "</td></tr><tr><td>払出後残高</td><td>";
//払出後残高
echo number_format($mona_paid,8). ' mona';

print "</td></tr></tbody></table>";

print "<br/>";

//ワーカー0
print "<table data-role=\"table\" class=\"ui-responsive table-stroke\"><tbody><tr>";
print "<td>ワーカー名</td><td>";

echo ($workers_min['getuserworkers']['data']{'0'}['username']);

print "</td></tr><tr><td>状態</td><td>";

$moniter_0 = ($workers_min['getuserworkers']['data']{'0'}['monitor']);

if ($moniter_0 = 1) {
        echo "稼働中";
}else {
        echo "停止中";
}

print "</td></tr><tr><td>ハッシュレート</td><td>";

echo ($workers_min['getuserworkers']['data']{'0'}['hashrate']);

print "</td></tr><tr><td>シェアレート</td><td>";

echo ($workers_min['getuserworkers']['data']{'0'}['shares']);

print "</td></tr><tr><td>Difficulty</td><td>";

echo ($workers_min['getuserworkers']['data']{'0'}['difficulty']);

print "</td></tr></tbody></table>";

print "<br/>";

//ワーカー1
print "<table data-role=\"table\" class=\"ui-responsive table-stroke\"><tbody><tr>";
print "<td>ワーカー名</td><td>";

echo ($workers_min['getuserworkers']['data']{'1'}['username']);

print "</td></tr><tr><td>状態</td><td>";

$moniter_1 = ($workers_min['getuserworkers']['data']{'1'}['monitor']);

if ($moniter_1 = 1) {
        echo "稼働中";
}else {
        echo "停止中";
}

print "</td></tr><tr><td>ハッシュレート</td><td>";

echo ($workers_min['getuserworkers']['data']{'1'}['hashrate']);

print "</td></tr><tr><td>シェアレート</td><td>";

echo ($workers_min['getuserworkers']['data']{'1'}['shares']);

print "</td></tr><tr><td>Difficulty</td><td>";

echo ($workers_min['getuserworkers']['data']{'1'}['difficulty']);

print "</td></tr></tbody></table>";


?>


<SCRIPT LANGUAGE="javascript" TYPE="text/javascript">
<!--
var Nowymdhms　=　new Date();
var NowMon = Nowymdhms.getMonth() + 1;
var NowDay = Nowymdhms.getDate();
var NowWeek = Nowymdhms.getDay();
var NowHour = Nowymdhms.getHours();
var NowMin = Nowymdhms.getMinutes();
var NowSec = Nowymdhms.getSeconds();
var Week = new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
if(NowHour < 10){
	NowHour = "0"+NowHour;
}
if(NowMin < 10){
	NowMin = "0"+NowMin;
}
document.write(NowMon+"月"+NowDay+"日("+Week[NowWeek]+") "+NowHour+":"+NowMin+":"+NowSec)
// -->
</SCRIPT>

<input type="button" value="Reload" onclick="window.location.reload();" />

</body>
</html>

