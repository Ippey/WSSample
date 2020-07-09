# API GatewayをつかってWebSocketしちゃうサンプル

## 必要なもの
- PHP
- composer
- [bref](https://bref.sh/)
- [Serverless Framework](https://www.serverless.com/)
- [Symfony CLI](https://symfony.com/download)
- AWS CLI
- AWS系のもろもろの設定(鍵を仕込んだり)

## インストール

```shell script
composer install
```

## デプロイ
```shell script
serverless deploy
```

- AWS CLIとserverlessの設定が必要

## デプロイ後設定
- LambdaにDynamoDBのアクセス権を追加

## 動作確認
```shell script
symfony server:start -d
symfony open:local
```

デプロイ後に、WebSocketのURLが出力されるので
templates/default/index.html.twigの

```twig
const ws = new WebSocket("wss://ws24dza6oh.execute-api.ap-northeast-1.amazonaws.com/dev");
```

↑の部分のURLを差し替えればOKです。  
双方向テストは、ブラウザを2つ立ち上げればいけます。