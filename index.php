<?php
// Prepared Statement
?>

<form action="<?php $_SERVER['REQUEST_URI']; ?>" method="POST">
    Shop ID: <input type="text" name="shop_id">
    <input type="submit" value="検索">
</form>

<?php
if (isset($_POST['shop_id'])) {
    try {

        $shop_id = $_POST['shop_id'];

        $user = 'test_user';
        $pwd = 'pwd';
        $host = 'localhost';
        $dbName = 'test_phpdb';
        $dsn = "mysql:host={$host};port=8889;dbname={$dbName};";
        $conn = new PDO($dsn, $user, $pwd);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // PDO内のprepare statementではなく、DB上のprepare statement(自分で作成したもの)を使用するといった内容（preparestatement使用時入れる）
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $pst = $conn->prepare("select * from test_phpdb.mst_shops where id = :id;");
        // prepare statementの基本的な流れ。　
        // ① prepare statementでは実行したいsqlを事前に準備し（）で括っておく。（pre-compiled）　この時点で、実行の命令とパラメーターが来れば動ける状態になっている
        // ② 処理したい内容の範囲をbindで指定する。
        // ③ prepareによってデータベース状に保持されているsqlをexecuteによって実行する。

        // メリットとして、sqlインジェクション対策、sql処理の省略化（頻繁に使用するものほど効果あり）

        // $pst->bindValue(':id', $shop_id, PDO::PARAM_STR);
        $pst->execute([
            ':id' => $shop_id
            //　bindvalueでパラメーターを指定しても良いが、データ型を気にしないのであれば直接exec時に配列形式で値を渡してあげると簡単（ちなみにデータ型はデフォルトでSTR）
        ]);
        $result = $pst->fetch();

        if (!empty($result) && count($result) > 0) {
            echo "店舗名は[{$result['name']}]です。";
        } else {
            echo "店舗が見つかりません。";
        }
    } catch (PDOException $e) {
        echo 'エラーが発生しました。';
    }
}
?>