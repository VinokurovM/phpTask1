<table border="2" cellpadding ="3" width="50%" >
<?php


if(strlen($_POST['musician'])!=0)
{
    $musician = str_replace(" ", "+", $_POST['musician']);
    $appAPI = "https://itunes.apple.com/search?media=music&term=$musician&entity=album&country=ru";

    $searchResult = file_get_contents($appAPI);                                                                                     // получаем таблицу json с itunesAPI
    $jsone = json_decode($searchResult);
    $dbconnect = pg_connect("host = localhost port = 5432  dbname = phpTaskBase user = postgres password = 123");   // подключаемся к postrgeSQL
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

        $insertFind = pg_query($dbconnect, "select collectionid from musicianalbum where collectionid = $collectionId");             // проверка наличия collectionid в БД
        $insertFindArray = pg_fetch_assoc($insertFind);
        echo "<tr>";

        if($insertFindArray) {
            $insertFindBD = pg_query($dbconnect, "select * from musicianalbum where collectionid = $collectionId");
            $insertFindBDArray = pg_fetch_assoc($insertFindBD);
            foreach ($insertFindBDArray as $value)
            {
                echo "<td>$value</td>";
            }
            echo "<td>База данных</td>";

        }
        else{
            $artistName = str_replace("'","''",$array->artistName);
            $artistId = $array->artistId;
            $collectionName = str_replace("'","''",$array->collectionName);
            $collectionViewUrl = $array->collectionViewUrl;
            $collectionPrice = $array->collectionPrice;
            $trackCount = $array->trackCount;

            $insertLine = "insert into musicianalbum (collectionid,collectionname,artistid,artistname, collectionviewurl, trackcount, collectionprice) values ($collectionId,'$collectionName',$artistId,'$artistName', '$collectionViewUrl', $trackCount, $collectionPrice);
";
            $insert = pg_query($dbconnect, $insertLine);
            if(!$insert) {
                echo "<br>Error! $collectionName<br>";
            }
            echo "<td>$collectionId</td>
                  <td>$collectionName</td>
                  <td>$artistId</td>
                  <td>$artistName</td>
                  <td>$collectionViewUrl</td>
                  <td>$trackCount</td>
                  <td>$collectionPrice</td>
                   <td>Itunes</td>";
        }
        echo "</tr>";
    }


    pg_close($dbconnect);


}
else
{
    echo "Поле пустое";
}

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


insert into musicianalbum (collectionid,collectionname,artistid,artistname, collectionviewurl, trackcount, collectionprice) values (123,'text',457,'text', 'text', 111, 111);

*/
?>
</table>

