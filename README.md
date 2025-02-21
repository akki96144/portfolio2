# 為替レートの表示
このコードはAPIを使って為替レートを取得し、表示するコードの実装です。
使用したAPI: https://frankfurter.dev/

## 工夫した点
### 1. 一度検索した内容をデータベースに記録する
題名にもあるとおり、一度検索した内容をデータベースに記録するように実装しました。
APIは使用した回数に応じて料金がかかるものが多いです。一度検索した内容をデータベースに記録することで、APIの使用回数を減らし使用料金を抑えることができます。

---

## 使用方法
### 1.SQLiteのデータベースを作成する
ターミナルで以下のコマンドを実行してください。

```sh
sqlite3 exchange_rates.db
```

### 2. テーブルを作成する。
SQLiteのコンソールに入ったら以下のSQLを打ち、テーブルを作成してください

```sql
CREATE TABLE exchange_rates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    date TEXT NOT NULL,
    base_currency TEXT NOT NULL,
    target_currency TEXT NOT NULL,
    rate REAL NOT NULL
);
```

### 3. ターミナルでフォルダに移動
ターミナルに戻り、以下のコマンドを実行してください。

```sh
cd path/to/your/folder  # 例: cd /Users/maedaakihiro/folder
```

### 4. ローカルサーバを起動
以下のコマンドでローカルサーバを起動します。

```sh
php -S localhost:8000
```

### 5. ブラウザで確認
ブラウザで以下の URL にアクセスしてください。

```
http://localhost:8000
```
