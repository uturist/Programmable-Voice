<?php
header('Content-type: text/xml; charset=UTF-8');
require("tw/Services/Twilio.php");				//�ݒ荀��(1)
$response = new Services_Twilio_Twiml();
$url ="http://<�ݒu����h���C��>/���f�B���N�g����/TwikiML.php";	//�ݒ荀��(2)
$db = "kino2/hideable/data/index.db";				//�ݒ荀��(3)
$ininum = 22;							//�ݒ荀��(4)

//GET�p�����[�^�[�擾
if (empty($_GET["n"])) {
 $gnum = $ininum;
}else{
 if ( is_numeric($_GET["n"]) ) {
  $gnum = $_GET["n"];
 }else{
  $gnum = $ininum;
 }
}

//DB�I�[�v��
$dbhandle = sqlite_open($db);

//�����K�C�_���X�p�e�L�X�g�擾
$query = sqlite_query($dbhandle, 'SELECT pagename, source FROM purepage WHERE num = '.$gnum);
$entry = sqlite_fetch_array($query, SQLITE_ASSOC);	//�P�ڂ̗v�f
$contents = $entry['source'];
$pgname = $entry['pagename'];
if (empty($contents)) {					// n�����w��or�s���̏ꍇ�A�ŏ��̃y�[�W�ŏ�������B
 $query = sqlite_query($dbhandle, 'SELECT pagename, source FROM purepage WHERE num = '.$ininum);
 $entry = sqlite_fetch_array($query, SQLITE_ASSOC);	//�P�ڂ̗v�f
 $contents = $entry['source'];
 $pgname = $entry['pagename'];
}

//Twiml���g
if (empty($_POST["Digits"])) {			//�ԍ����͖����ꍇ�A�����K�C�_���X�𗬂�
  $gather = $response->gather(array('numDigits' => 1, 'timeout' => 30));
  $gather->say($contents, array('language' => 'ja-jp', 'voice' => 'woman'));
}else {						//�ԍ����͂���ꍇ�A�ԍ��ɑΉ�����URL�Ƀ��_�C���N�g����
 //�J�ڐ�y�[�WURL�擾
 $linkphrase ="";	//�����N��y�[�W��
 $lnknarray = array();	//�����N��y�[�W�i���o�[�i�R���e���c���ł̏o�����j
 $query = sqlite_query($dbhandle, 'SELECT linked, linker FROM linklist WHERE linker = "'.$pgname.'"');
 while ($entry = sqlite_fetch_array($query, SQLITE_ASSOC)) {
  $linkphrase = $entry['linked'];
  $query2 = sqlite_query($dbhandle, 'SELECT num FROM purepage WHERE pagename = "'.$linkphrase.'"');
  $entry2 = sqlite_fetch_array($query2, SQLITE_ASSOC);	//�P�ڂ̗v�f
  $lnknarray[strpos($contents,$linkphrase)] = $url."?n=".$entry2['num'];
 }
 ksort($lnknarray);				//�L�[�̏����\�[�g
 $plunkary =  array_values($lnknarray);		//�v���[���Ȕz��
 array_unshift($plunkary, $url);		//0�Ԗڂ̗v�f�ǉ��i�d�b�{�^���͂P���牟���Ă��炤�O��j
 if (count($plunkary) > $_POST["Digits"]) {
  if (1 > $_POST["Digits"]) {
   $nxturl = $url;		//���߂�URL���o��
  } else {
   $nxturl = $plunkary[$_POST["Digits"]];		//���߂�URL���o��
  }
 } else {
  $nxturl = $url."?n=".$gnum;
 }
 $response->Redirect($nxturl);
}
print $response;
?>