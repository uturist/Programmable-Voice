#Programmable-Voice  
======================


このプログラムについて
======================

電話をかけるとwikiに登録したテキストを読み上げます。プッシュボタンで操作します。  
稼動イメージ→http://shop.que.jp/p/view.html  
シンプルな電話の自動音声応答システム（IVR）ならProgrammable-Voiceで代用できるかも。  

KinoWiki2.0とTwilioを使用します。  


今後やりたいこと  
======================

IVR向け機能の高度化。  
音声インターフェイスを備える機器・アプリ・ロボット等向け機能の追加。  


インストール方法  
======================

・KinoWiki2.0のインストール  
説明省略  

・KinoWikiへの読上げ用テキストの登録  
説明省略  

・Twilioのライブラリ設置  
↓のページから「twilio-php」を取得しサーバーに設置してください。  
http://jp.twilio.com/docs/libraries  

・TwikiML.phpの設定・アップロード  
設定  
　TwikiML.php内の以下の部分を環境にあわせて指定して下さい。  
　(1)require("tw/Services/Twilio.php");					←ライブラリを指定  
　(2)$url ="http://<設置するドメイン>/＜ディレクトリ＞/TwikiML.php";	←TwikiML.phpのURLを指定  
　(3)$db = "kino2/hideable/data/index.db";				←KinoWikiのDBの設置場所を指定  
　(4)$ininum = 22;							←電話をかけた直後に読上げるWikiページの番号  
アップロード  
　設定が終了したらTwikiML.phpをサーバーにUPしてください。  

・Twilioのアカウント取得・URL登録  
Twilio管理画面の電話番号設定ページにある「Voice Request URL」に、TwikiML.phpのURLを指定してください。  
参考ページ：http://blog.twilio.kddi-web.com/2013/02/18/twilio-call-incomming/  


ライセンス  
======================
MIT License

