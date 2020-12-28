<?php

namespace App\Console\Commands;

use Google_Client;
use Google_Service_YouTube;
use DateTime;

use App\Enums\MemberChannelId;
use Illuminate\Support\Facades\Storage;

class GetScheduleModule
{
    const MAX_SNIPPETS_COUNT = 50;
    const DEFAULT_ORDER_TYPE = 'viewCount';

    private string $googleApiKye;

    public function seveVideoInfo()
    {
        //APIキーの設定
        $this->googleApiKye = $this->getGoogleApiKey();
        echo $this->googleApiKye;

        //チェンネルIdの配列を取得
        $channelIds = MemberChannelId::getValues();

        $videoIdList = $this->getListByChannelId($channelIds);
        print_r(implode($videoIdList) . "\n");

        /*
        foreach ($videoIdList as $videoId) {
            $this->getVideoInfoByVideolId($videoId);
        }*/
        $this->getVideoInfoByVideolId(implode(',', $videoIdList));
    }

    /**
     * チャンネルIDから動画情報を取得する。
     *
     * @return array
     */
    private function getListByChannelId(array $channelIdList): array
    {
        // Googleへの接続情報のインスタンスを作成と設定
        $client = new Google_Client();
        $client->setDeveloperKey($this->googleApiKye);

        // 接続情報のインスタンスを用いてYoutubeのデータへアクセス可能なインスタンスを生成
        $youtube = new Google_Service_YouTube($client);

        //gmdateを使うべきかよくわからんが仮置き
        $date = date('Y-m-d', strtotime('-1 day')) . 'T' . date('H:m:s', mktime(0, 0, 0)) . 'Z';
        print_r($date . "\n");

        $videoIdList = array();

        foreach ($channelIdList as $channelId) {
            // 必要情報を引数に持たせ、listSearchで検索して動画一覧を取得
            $items = $youtube->search->listSearch('snippet', [
                'channelId'  => $channelId,
                'publishedAfter' => $date,
            ]);

            // 連想配列だと扱いづらいのでcollection化して処理
            //videoIdのみを取得
            $videoIds = collect($items->getItems())->pluck('id')->pluck('videoId')->all();

            foreach ($videoIds as $videoId) {
                array_push($videoIdList, $videoId);
            }
        }

        //nullと空文字の配列を削除
        $videoIdList = array_filter($videoIdList, function ($val) {
            return !is_null($val);
        });

        return $videoIdList;
    }

    private function getVideoInfoByVideolId(string $videoIds)
    {
        // Googleへの接続情報のインスタンスを作成と設定
        $client = new Google_Client();
        $client->setDeveloperKey($this->googleApiKye);
        // 接続情報のインスタンスを用いてYoutubeのデータへアクセス可能なインスタンスを生成
        $youtube = new Google_Service_YouTube($client);

        // 必要情報を引数に持たせ、listSearchで検索して動画一覧を取得
        $items = $youtube->videos->listVideos('liveStreamingDetails,snippet', [
            'id'  => $videoIds
        ]);

        // 連想配列だと扱いづらいのでcollection化して処理
        //videoIdのみを取得
        /*
        $videoList_id = collect($items->getItems())->pluck(['id'])->first();
        $videoList_snippet = collect($items->getItems())->pluck(['snippet'])->first();
        $videoList_liveStreamingDetails = collect($items->getItems())->pluck(['liveStreamingDetails'])->first();
        $video = ['id' => $videoList_id, 'snippet' => $videoList_snippet, 'liveStreamingDetails' => $videoList_liveStreamingDetails];
        */
        $str = json_encode($items, JSON_UNESCAPED_UNICODE);

        //JSONで保存（storage/app）
        Storage::put(env('SCHEDULE_FILE_NAME'), $str);
    }

    private function getGoogleApiKey(): string
    {
        //現在の時間を取得し、2時間おきに使用するキーを変更する。
        $now = new DateTime();
        switch ($now->format('H')) {
            case '00';
                return env('GOOGLE_API_KEY_1');
            case '02';
                return env('GOOGLE_API_KEY_2');
            case '04';
                return env('GOOGLE_API_KEY_3');
            case '06';
                return env('GOOGLE_API_KEY_4');
            case '08';
                return env('GOOGLE_API_KEY_5');
            case '10';
                return env('GOOGLE_API_KEY_6');
            case '12';
                return env('GOOGLE_API_KEY_7');
            case '14';
                return env('GOOGLE_API_KEY_8');
            case '16';
                return env('GOOGLE_API_KEY_9');
            case '18';
                return env('GOOGLE_API_KEY_10');
            case '20';
                return env('GOOGLE_API_KEY_11');
            case '22';
                return env('GOOGLE_API_KEY_12');
            default;
                return env('GOOGLE_API_KEY_1');
        }
    }
}
