<?php


if(strlen($_POST['musician'])!=0)
{
    $musician = str_replace(" ", "+", $_POST['musician']);
    $appAPI = "https://itunes.apple.com/search?media=music&term=$musician&entity=album&country=ru";

    $searchResult = file_get_contents($appAPI);
    $jsone = json_decode($searchResult);
    for ($i = 0; $i < $jsone -> resultCount; $i++) {
        $array = $jsone->results[$i];
        $artistName = $array->artistName;
        $artistId = $array->artistId;
        $collectionName = $array->collectionName;
        $collectionViewUrl = $array->collectionViewUrl;
        $collectionPrice = $array->collectionPrice;
        $primaryGenreName = $array->primaryGenreName;
        $trackCount = $array->trackCount;
        $collectionId = $array->collectionId;

        echo "artistName: ", $artistName, "<br>";
        echo "artistId: ", $artistId, "<br>";
        echo "collectionName: ", $collectionName, "<br>";
        echo "collectionId: ", $collectionId, "<br>";
        echo "collectionViewUrl: ", $collectionViewUrl, "<br>";
        echo "trackCount: ", $trackCount, "<br>";
        echo "primaryGenreName: ", $primaryGenreName, "<br>";
        echo "collectionPrice: ", $collectionPrice, "<br><br>";
    }


}
else
{
    echo "Поле пустое";
}


?>