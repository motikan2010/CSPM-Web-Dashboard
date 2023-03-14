# CSPM Dashboard

```
make app 

    docker compose exec app php artisan cspm:load --all

    php artisan cspm:create_collection_diff
```

## Build

### Front

```

```

## Docs

### storage/app/

```
cspm_collection/
    CSMPのコレクション

cspm_collection_2/
    CSPMのコレクションから不要な項目を削除

cspm_diff/
    コレクションのdiffファイル

cspm_diff_html/
    コレクションのdiffのHTMLファイル

cspm_result/
    CSPMの検査結果
```
