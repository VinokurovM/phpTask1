<table border="2" cellpadding ="3" width="50%" >
<?php


if(strlen($_POST['musician'])!=0)
{
    $musician = str_replace(" ", "+", $_POST['musician']);
    $appAPI = "https://itunes.apple.com/search?media=music&term=$musician&entity=album&country=ru";

    $searchResult = file_get_contents($appAPI);                                                                                     // получаем таблицу json с itunesAPI
    $jsone = json_decode($searchResult);
    $bdconnect = pg_connect("host = localhost port = 5432  dbname = phpTaskBase user = postgres password = 123");   // подключаемся к postrgeSQL
    echo "<tr>
                <td>ID альбома</td>
                <td>Название альбома</td>
                <td>ID исполнителя</td>
                <td>Имя исполнителя</td>
                <td>Ссылка в Itunes</td>
                <td>Колличество треков</td>
                <td>Цена альбома Руб.</td>
                <td>Источник записи</td>
            </tr>";


    for ($i = 0; $i < $jsone -> resultCount; $i++) {
        $array = $jsone->results[$i];
        $collectionId = $array->collectionId;

        $insertFind = pg_query($bdconnect, "select * from musicianalbum where collectionid = $collectionId");             // проверка наличия collectionid в БД
        $insertFindArray = pg_fetch_assoc($insertFind);
        echo "<tr>";

        if($insertFindArray)
        {
            foreach ($insertFindArray as $key => $value)
            {
                if($key == "collectionviewurl")
                {
                    echo "<td><a href='$value'>Ituens</a></td>";
                }
                else
                echo "<td>$value</td>";
            }
            echo "<td>База данных</td>";

        }
        else
            {
            $artistName = str_replace("'","''",$array->artistName);
            $artistId = $array->artistId;
            $collectionName = str_replace("'","''",$array->collectionName);
            $collectionViewUrl = $array->collectionViewUrl;
            $collectionPrice = $array->collectionPrice;
            $trackCount = $array->trackCount;

            $insertLine = "insert into musicianalbum (collectionid,collectionname,artistid,artistname, collectionviewurl, trackcount, collectionprice) values ($collectionId,'$collectionName',$artistId,'$artistName',
            '$collectionViewUrl', $trackCount, $collectionPrice);";
            $insert = pg_query($bdconnect, $insertLine);

                echo "<td>$collectionId</td>
                     <td>$array->collectionName</td>
                     <td>$artistId</td>
                     <td>$array->artistName</td>
                     <td><a href='$collectionViewUrl'>Ituens</a></td>
                     <td>$trackCount</td>
                     <td>$collectionPrice</td>
                     <td>Itunes</td>";
            }
        echo "</tr>";
    }

    pg_close($bdconnect);

}
else
{
    echo "Поле пустое";
}

echo "<form action=\"actionPost.php\" method=\"post\">
    <p>Имя: <input type=\"text\" name=\"musician\"/></p>
    <p><input type=\"submit\"/> </p>
</form>"

/*
создание таблицы

create table musicianalbum (
collectionid int PRIMARY KEY,
collectionname text,
artistid int,
artistname text,
collectionviewurl text,
trackcount int,
collectionprice int);


*/
?>
</table>

