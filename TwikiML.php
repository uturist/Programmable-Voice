<?php
header('Content-type: text/xml; charset=UTF-8');
require("tw/Services/Twilio.php");				//設定項目(1)
$response = new Services_Twilio_Twiml();
$url ="http://<設置するドメイン>/＜ディレクトリ＞/TwikiML.php";	//設定項目(2)
$db = "kino2/hideable/data/index.db";				//設定項目(3)
$ininum = 22;							//設定項目(4)

//GETパラメーター取得
if (empty($_GET["n"])) {
 $gnum = $ininum;
}else{
 if ( is_numeric($_GET["n"]) ) {
  $gnum = $_GET["n"];
 }else{
  $gnum = $ininum;
 }
}

//DBオープン
$dbhandle = sqlite_open($db);

//音声ガイダンス用テキスト取得
$query = sqlite_query($dbhandle, 'SELECT pagename, source FROM purepage WHERE num = '.$gnum);
$entry = sqlite_fetch_array($query, SQLITE_ASSOC);	//１個目の要素
$contents = $entry['source'];
$pgname = $entry['pagename'];
if (empty($contents)) {					// nが無指定or不正の場合、最初のページで処理する。
 $query = sqlite_query($dbhandle, 'SELECT pagename, source FROM purepage WHERE num = '.$ininum);
 $entry = sqlite_fetch_array($query, SQLITE_ASSOC);	//１個目の要素
 $contents = $entry['source'];
 $pgname = $entry['pagename'];
}

//Twiml中身
if (empty($_POST["Digits"])) {			//番号入力無い場合、音声ガイダンスを流す
  $gather = $response->gather(array('numDigits' => 1, 'timeout' => 30));
  $gather->say($contents, array('language' => 'ja-jp', 'voice' => 'woman'));
}else {						//番号入力ある場合、番号に対応したURLにリダイレクトする
 //遷移先ページURL取得
 $linkphrase ="";	//リンク先ページ名
 $lnknarray = array();	//リンク先ページナンバー（コンテンツ内での出現順）
 $query = sqlite_query($dbhandle, 'SELECT linked, linker FROM linklist WHERE linker = "'.$pgname.'"');
 while ($entry = sqlite_fetch_array($query, SQLITE_ASSOC)) {
  $linkphrase = $entry['linked'];
  $query2 = sqlite_query($dbhandle, 'SELECT num FROM purepage WHERE pagename = "'.$linkphrase.'"');
  $entry2 = sqlite_fetch_array($query2, SQLITE_ASSOC);	//１個目の要素
  $lnknarray[strpos($contents,$linkphrase)] = $url."?n=".$entry2['num'];
 }
 ksort($lnknarray);				//キーの昇順ソート
 $plunkary =  array_values($lnknarray);		//プレーンな配列化
 array_unshift($plunkary, $url);		//0番目の要素追加（電話ボタンは１から押してもらう前提）
 if (count($plunkary) > $_POST["Digits"]) {
  if (1 > $_POST["Digits"]) {
   $nxturl = $url;		//求めるURL取り出し
  } else {
   $nxturl = $plunkary[$_POST["Digits"]];		//求めるURL取り出し
  }
 } else {
  $nxturl = $url."?n=".$gnum;
 }
 $response->Redirect($nxturl);
}
print $response;
?>