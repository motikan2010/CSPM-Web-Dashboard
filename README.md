# CSPM Dashboard

<img src="https://user-images.githubusercontent.com/3177297/225024533-d82498f3-5505-4c27-b89b-f7b8584f44df.png" width="600px">
<img src="https://user-images.githubusercontent.com/3177297/225024552-390ad6eb-3125-45c8-b2ae-88568481cc01.png" width="600px">

## Command

```
make app 

    docker compose exec app php artisan cspm:load --all

    php artisan cspm:create_collection_diff
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
