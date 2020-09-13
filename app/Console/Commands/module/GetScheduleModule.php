<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Storage;

use Google_Client;
use Google_Service_YouTube;

class GetScheduleModule{
    const MAX_SNIPPETS_COUNT = 50;
    const DEFAULT_ORDER_TYPE = 'viewCount';

    public function seveVideoInfo()
    {

        $videoIdList= $this->getListByChannelId();

        foreach($videoIdList as $videoId){
            $this->getVideoInfoByVideolId($videoId);
        }
    }

    /**
     * チャンネルIDから動画情報を取得する。
     *
     * @return array
     */
    private function getListByChannelId():array
    {
        // Googleへの接続情報のインスタンスを作成と設定
        $client = new Google_Client();
        $client->setDeveloperKey(env('GOOGLE_API_KEY'));

        // 接続情報のインスタンスを用いてYoutubeのデータへアクセス可能なインスタンスを生成
        $youtube = new Google_Service_YouTube($client);

        // 必要情報を引数に持たせ、listSearchで検索して動画一覧を取得
        $items = $youtube->search->listSearch('snippet', [
            'channelId'  => 'UCeLzT-7b2PBcunJplmWtoDg',
        ]);

        // 連想配列だと扱いづらいのでcollection化して処理
        //videoIdのみを取得
        $snippets = collect($items->getItems())->pluck('id')->pluck('videoId')->all();

        //nullと空文字の配列を削除
        $videoId = array_filter($snippets,function($val){
            return !is_null($val);
        });
        return $videoId;
    }


    private function getVideoInfoByVideolId(string $videoId){
        // Googleへの接続情報のインスタンスを作成と設定
        $client = new Google_Client();
        $client->setDeveloperKey(env('GOOGLE_API_KEY'));
         // 接続情報のインスタンスを用いてYoutubeのデータへアクセス可能なインスタンスを生成
         $youtube = new Google_Service_YouTube($client);

         // 必要情報を引数に持たせ、listSearchで検索して動画一覧を取得
         $items = $youtube->videos->listVideos('liveStreamingDetails,snippet', [
             'id'  => $videoId
         ]);

        // 連想配列だと扱いづらいのでcollection化して処理
        //videoIdのみを取得
        $videoList_id = collect($items->getItems())->pluck(['id'])->first();
        $videoList_snippet = collect($items->getItems())->pluck(['snippet'])->first();
        $videoList_liveStreamingDetails = collect($items->getItems())->pluck(['liveStreamingDetails'])->first();
        $video=['id'=>$videoList_id,'snippet'=>$videoList_snippet,'liveStreamingDetails'=>$videoList_liveStreamingDetails];
        print_r($videoList_id);
    }
}
