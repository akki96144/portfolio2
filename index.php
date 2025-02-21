<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>為替レート検索</title>
</head>
<body>
    <form action="" method="POST">
        <label for="date">日付を入力してください: </label>
        <input type="date" id="date" name="date">
        <br><br>

        <label for="base_currency">比較元の通貨を選択してください: </label>
        <select id="base_currency" name="base_currency">
            <option value="JPY">JPY (日本円)</option>
            <option value="USD">USD (アメリカドル)</option>
            <option value="EUR">EUR (ユーロ)</option>
            <option value="GBP">GBP (イギリスポンド)</option>
            <option value="AUD">AUD (オーストラリアドル)</option>
        </select>
        <br><br>

        <label for="target_currency">比較先の通貨を選択してください: </label>
        <select id="target_currency" name="target_currency">
            <option value="USD">USD (アメリカドル)</option>
            <option value="EUR">EUR (ユーロ)</option>
            <option value="GBP">GBP (イギリスポンド)</option>
            <option value="AUD">AUD (オーストラリアドル)</option>
            <option value="JPY">JPY (日本円)</option>
        </select>
        <br><br>

        <button type="submit">為替レートを表示</button>
    </form>

    <?php
    $dsn = 'sqlite:exchange_rates.db';
    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "データベースに接続できません。管理者にお問い合わせください。";
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $date = !empty($_POST["date"]) ? $_POST["date"] : date("Y-m-d");
        $baseCurrency = $_POST["base_currency"];
        $targetCurrency = $_POST["target_currency"];

        $stmt = $pdo->prepare("SELECT rate FROM exchange_rates WHERE date = :date AND base_currency = :base AND target_currency = :target");
        $stmt->execute([':date' => $date, ':base' => $baseCurrency, ':target' => $targetCurrency]);
        $result = $stmt->fetch();

        if ($result) {
            $rate = $result['rate'];
            echo "<p>$date の $baseCurrency から $targetCurrency への為替レートは $rate です。（データベースから取得）</p>";
        } else {
            $apiUrl = "https://api.frankfurter.app/$date?from=$baseCurrency&to=$targetCurrency";
            $response = @file_get_contents($apiUrl);
            $data = json_decode($response, true);

            if (isset($data['rates'][$targetCurrency])) {
                $rate = $data['rates'][$targetCurrency];
                echo "<p>$date の $baseCurrency から $targetCurrency への為替レートは $rate です。（APIから取得）</p>";

                $insertStmt = $pdo->prepare("INSERT INTO exchange_rates (date, base_currency, target_currency, rate) VALUES (:date, :base, :target, :rate)");
                $insertStmt->execute([':date' => $date, ':base' => $baseCurrency, ':target' => $targetCurrency, ':rate' => $rate]);
            } else {
                echo "<p>指定された日付、または比較した国同士の為替レートが見つかりませんでした。</p>";
            }
        }
    }
    ?>
</body>
</html>
