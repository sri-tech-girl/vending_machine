<?php
// 自動販売機
// 商品のラインナップから考える
// 商品のラインナップを連想配列でもつ
// keyが商品名、valueが価格
// 連想配列で個数を持つ
$items = array(
    'コーラ' => array('price' => 120, 'count' => 0),
    'リンゴジュース' => array('price' => 120, 'count' => 10),
    'ミックスジュース' => array('price' => 100, 'count' => 3),
    'ブラックコーヒー' => array('price' => 110, 'count' => 10),
    '麦茶' => array('price' => 150, 'count' => 12),
);

$errors = array();

// お金一覧(電子マネーは後で考える（　＾ω＾）・・・)←ビットコインも。(優さんここ調べ中ってことはわかってるよね)orz
// item = 金種, count = おつりとして用意されているお金の枚数
$moneys = array(
    array(
        item => 10000,
        count => 100,
    ),
    array(
        item => 5000,
        count => 100,
    ),
    array(
        item => 2000,
        count => 100,
    ),//入れました！ GJ!
    array(
        item => 1000,
        count => 100,
    ),
    array(
        item => 500,
        count => 100,
    ),
    array(
        item => 100,
        count => 100,
    ),
    array(
        item => 50,
        count => 100,
    ),
    array(
        item => 10,
        count => 100,        
    ),
);

// お金を受け取る処理
// 受け取ったお金を変数にいれる
// submitの場合エラーになる
// issetとisintが必要
if (!isset($money) || !is_int($money)) {
    $errors[] = 'お金以外入れないでください！';
}
$money = array();

// 商品を選ぶ処理
// 選んだ商品を変数に入れる
// submitの場合エラーになる
// issetが必要
if (!isset($selected)) {
    $errors[] = '商品を選んでください★';
}
$selected = 'コーラ';

// 購入されるたびにマイナス1する
// 在庫が0の場合は購入ができないようにする
function check_count(string $result, array $items){
    // 在庫が０
    if (isset($items[$result]['count']) || $items[$result]['count'] == 0) {
        return false;
    }
    return true;
}
    
function check_price(array $items, string $result, int $money){
    $change = $money - $items[$result]['price'];

    // お金不足の場合足りない分return
    if ($money < $change) {
        return  $change;
    }
        
    // おつりが出る処理
    // 受け取ったお金の変数を連想配列のvalueの部分の差をとって1以上の時は余りを出力する
    if(isset($items[$result]['price']) && $change === 0){
        return $change; //あとでかえる
    }
}

/**
 * おつりを紙幣・硬貨に分ける処理
 * int $money
 */
function coin(int $money, array $arr_money)
{
    foreach ($arr_money as $key => $value) {
        // 同じ金種を複数返さなければならないときはどうなるか？
        // 金種の数を出す
        $num = ($money / $value['money']);
        $ret = [];
        // 0の場合、おつりが用意されている場合、計算
        if ($num > 0 &&  $num >= $value['count']){
            $money -= ($value['item'] * $num);
            $ret[] = array(
                'item' => $value['item'],
                'count' => $num,
            );
        }
    }
    return $ret;
}

/*

*/
function sum_money(array $money) {
    // 合計値
    $res = 0;

    foreach ($money as $key => $value) {
        // $value['item']：金種の種類、$value['cnt']：数
        $res += $value['item'] * $value['cnt'];
    }

    return $res;
}

/**
 * 自販機からおつりを減らす
 */
function sub_change(array $change, array $moneys)
{
    foreach ($change as $key => $value) {
        foreach ($moneys as $key_2 => $value_2) {
            if ($value['item'] === $value_2['item']) {
                $moneys[$key_2]['count'] = $value_2['count'] - $value['count'];
            }
        }
    }
    return $moneys;
}

if (!empty($errors)) {
    // 商品が出る処理
    // 連想配列のkeyと一致するものを検索する
    // 一致した場合はその商品の名前を出力
    $result = array_search($selected,$items); //keyが返される

    $res = check_count($result,$items);

    if ($res === true) {
        $items[$result]['count']--;
        
        $res2 = check_price($items, $result, $money);

        if ($res2 >= 0) {
            $result = coin($res2, $moneys);
            $moneys = sub_change($result, $moneys);
        } else {
            $errors[] = 'お金が'.abs($res2).'円たりません！';
        }
    } else {
        $errors[] = '在庫がありません！'; 
    }
}
