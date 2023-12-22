<html>

<head>
    <meta charset='utf-8' />
</head>

<body>
    {{-- フォーム --}}
    <form method="POST" action="{{ route('chat_gpt-chat') }}"  enctype="multipart/form-data">
        @csrf
        <textarea rows="10" cols="50" name="sentence">{{ isset($sentence) ? $sentence : '' }}</textarea>
        <input type="radio" id="option1" name="option" value="1">
        <label for="option1">マークダウン</label><br>
        <input type="radio" id="option2" name="option" value="2">
        <label for="option2">そのまま</label><br>
        <input type="file" name="audio_input" accept="audio/*">
        <button type="submit">ChatGPT</button>
    </form>

    {{-- 結果 --}}
    {!! isset($text) ? $text : '' !!}
</body>

</html>
