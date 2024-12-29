以下のエラーが発生しました。

ファイル名：{{ $error->getFile() }}
行番号：{{ $error->getLine() }}
エラーコード：{{ $error->getCode() }}
エラーメッセージ：{{ $error->getMessage() }}
スタックトレース：
{{ $error->getTraceAsString() }}