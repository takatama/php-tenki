tenki-php
=========

コンソールから使います。1週間分の天気予報を表示します。
天気予報のデータは [OpenWeatherMap](http://openweathermap.org/) から入手します。

## インストール方法

```
$ git clone https://github.com/takatama/php-tenki
```

## 使い方

```
$ cd tenki-php
$ php tenki.php <場所,国>
```

例: 千葉県柏市のお天気

```
$ php tenki.php kashiwa,jp
Kashiwa, JP (lat:35.854439, lon:139.968887)
2014/12/27 Clear max:7.9 min:-0.4
2014/12/28 Clouds max:7.9 min:-1.8
2014/12/29 Rain max:5.2 min:-0.4
2014/12/30 Clear max:11.3 min:0.2
2014/12/31 Rain max:8.7 min:-0.5
2015/01/01 Rain max:10.1 min:-2.1
2015/01/02 Clear max:7.9 min:0.2
```

## 使った技術

### OpenWeatherMap API

一日単位の天気予報データ([Daily Forecast Data](http://openweathermap.org/forecast))を使います。

例）千葉県柏市のデータ
http://api.openweathermap.org/data/2.5/forecast/daily?q=kashiwa,JP

データの説明は[こちら](http://openweathermap.org/weather-data#16days)です。日本に合わせるために工夫が必要です。

* 時刻はGMT。JPNに変換するため 9時間進める。
* 温度は華氏。摂氏に変換するため 273.15 引く。
* 小数点1桁だけでいいので、round 関数で四捨五入する。

ついでに、
* コンソールの表示をカラフルにするため、[kevinlebrun/colors.php](https://github.com/kevinlebrun/colors.php) を使う。

```tenki.php
function show_forecast($time_gmt, $weather, $temp_max_K, $temp_min_K) {
    $time_jpn = $time_gmt + 9 * 60 * 60;
    $temp_max_C = round($temp_max_K - 273.15, 1); //小数点第一位まで表示
    $temp_min_C = round($temp_min_K - 273.15, 1);
    $c = new Color();
    echo gmdate('Y/m/d', $time_jpn) . ' ' . $c($weather)->bold . ' max:' . $c($temp_max_C)->red . ' min:' . $c($temp_min_C)->blue . PHP_EOL;
}
```


### Guzzle で REST API を利用する

最近はcURLより[Guzzle](http://docs.guzzlephp.org/en/latest/)みたいです。JSONならこれだけで大丈夫。


```tenki.php
require 'vendor/autoload.php';

use GuzzleHttp\Client;

$url = 'http://api.openweathermap.org/data/2.5/forecast/daily?q=' . $city_and_country;
$client = new Client();
$json = $client->get($url)->json();
```

### 参考にした情報

* PHP - Guzzle で Twitter REST API を叩く - Qiita
http://qiita.com/kawanamiyuu/items/2cab57e3d3932a2c5e4e


