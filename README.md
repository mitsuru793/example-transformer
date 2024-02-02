## functional test

### アサーションで比較するデータ

expected
* Entity(repositoryから取得)

リポジトリを使わない場合
* productionで使わないコードを定義せずに済む

リポジトリを使う場合
* production

actual
* デコードしたJSONレスポンス(array)
* DBから取得したEntity(保存されているかの確認)

パターン
* array === array
* Entity === Entity
* Entity === array

変換

* ○ Entity(public property) -> array
* ✗ array -> Entity
