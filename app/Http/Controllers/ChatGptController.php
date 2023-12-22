<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatGptController extends Controller
{

    private $openaiClient;

    public function __construct()
    {
        $api_key = env('CHAT_GPT_KEY');
        $this->openaiClient = \Tectalic\OpenAi\Manager::build(
            new \GuzzleHttp\Client(),
            new \Tectalic\OpenAi\Authentication($api_key)
        );
    }
    /**
     * index
     *
     * @param  Request  $request
     */
    public function index(Request $request)
    {
        return view('chat');
    }

    /**
     * ChatGPT API呼び出し
     * ライブラリ
     */
    function chat_gpt($system, $user)
    {

        // パラメータ
        $data = array(
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "system",
                    "content" => $system
                ],
                [
                    "role" => "user",
                    "content" => $user
                ]
            ]
        );



        try {

            $response = $this->openaiClient->chatCompletions()->create(
                new \Tectalic\OpenAi\Models\ChatCompletions\CreateRequest($data)
            )->toModel();

            return $response->choices[0]->message->content;
        } catch (\Exception $e) {
            return "ERROR";
        }
    }


    function whisper_speech_to_text($audio_path)
    {
        try {
        $response = $this->openaiClient->audioTranscriptions()->create(
            new \Tectalic\OpenAi\Models\AudioTranscriptions\CreateRequest([
                'file' => $audio_path,
                'model' => 'whisper-1',
        ])
        )->toModel();

        return $response->text;
// Your audio transcript in your source language...
        }
        catch (\Exception $e) {
            return "ERROR";
        }
    }


    /**
     * chat
     *
     * @param  Request  $request
     */
    function chat(Request $request)
    {


        if ($request->hasFile('audio_input')) {
            $audioFile = $request->file('audio_input');
            $destinationPath = 'uploads';
            $audioFile->move($destinationPath, $audioFile->getClientOriginalName());
            $audioFilePath = '/var/www/html/public/uploads/' . $audioFile->getClientOriginalName();
            $sentence = $this->whisper_speech_to_text($audioFilePath);
        } else {
            $sentence = $request->input('sentence');
        }


        // 文章
        // ChatGPT API処理
        if ($request->option === "1") {
            $chat_response = $this->chat_gpt("マークダウンで出力してください。勉強会を開催して、それについての記事を作成しようと思っています。
            音声ファイルに勉強会の様子を載せた音声を登録しました。これについて記事を書いてください。その際に以下の構成で書いてください。
            ・はじめに
            ここではどのような内容の勉強会なのかまとめてください
            ・タイムテーブル
            どのような
            ・まとめ",  $sentence);

        } else {
            $chat_response = $this->chat_gpt("勉強会を開催して、それについての記事を作成しようと思っています。
            音声ファイルに勉強会の様子を載せた音声を登録しました。これについて記事を書いてください。その際に以下の構成で書いてください。
            ・はじめに
            ここではどのような内容の勉強会なのかまとめてください
            ・タイムテーブル
            どのような
            ・まとめ", $sentence);
        }
            $text = $this->simple_format($chat_response);




        return view('chat', compact('sentence', 'text'));
    }
    function simple_format($text) {
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\r\n|\r|\n/', "<br>", $text);
        $text = '<p>' . preg_replace('/<br><br>/', '</p><p>', $text) . '</p>';
        return $text;
    }
}
