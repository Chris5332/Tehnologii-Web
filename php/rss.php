<?php

header("Content-Type: application/rss+xml; charset=UTF-8");

$db = new SQLite3('../data/pow_db.sqlite');

if(isset($_GET['type']))
    $type=$_GET['type'];
else
    $type="recent";
$query=$db->prepare('SELECT * FROM animals ORDER BY created_at DESC LIMIT 10');

if($type==="popular")
{
    $query=$db->prepare('SELECT a.*,(SELECT COUNT(*) FROM requests r WHERE r.animal_id=a.id ) as request_counter FROM animals a 
        ORDER BY request_counter DESC LIMIT 10');
}
else if(in_array($type,["botosani","bacau","galati","iasi","neamt","suceava"]))
{
    $query=$db->prepare('SELECT * FROM animals WHERE region=:region ORDER BY created_at DESC LIMIT 10');
    $region=ucfirst($type);
    $query->bindValue(":region",$region,SQLITE3_TEXT);
}
else if(in_array($type,["healthy","injured","sick"]))
{
    $query=$db->prepare('SELECT * FROM animals WHERE health_status=:health_status ORDER BY created_at DESC LIMIT 10');
    $query->bindValue(":health_status",$type,SQLITE3_TEXT);
}
else if($type==="group")
{
    $query=$db->prepare('SELECT * FROM animals WHERE is_group=1 ORDER BY created_at DESC LIMIT 10');
}
else if($type==="pets")
{
    $query=$db->prepare('SELECT * FROM animals WHERE is_group=0 ORDER BY created_at DESC LIMIT 10');
}
else if($type==="users")
{
    $query=$db->prepare('SELECT a.* FROM animals a JOIN users u ON a.owner_id=u.id WHERE u.family=0 
    ORDER BY a.created_at DESC LIMIT 10');
}
else if($type==="families")
{
    $query=$db->prepare('SELECT a.* FROM animals a JOIN users u ON a.owner_id=u.id WHERE u.family=1 
    ORDER BY a.created_at DESC LIMIT 10');
}
else//for species
{
    $query=$db->prepare('SELECT COUNT(*) FROM animals WHERE species=:species');
    $query->bindValue(":species",$type,SQLITE3_TEXT);
    $result=$query->execute();
    $counter=$result->fetchArray(SQLITE3_NUM);
    if($counter[0]>0)
    {
        $query=$db->prepare('SELECT * FROM animals WHERE species=:species ORDER BY created_at DESC LIMIT 10');
        $query->bindValue(":species",$type,SQLITE3_TEXT);
    }
    else
        $query=$db->prepare('SELECT * FROM animals ORDER BY created_at DESC LIMIT 10');
}

$result=$query->execute();
$dom=new DOMDocument("1.0","UTF-8");
$dom->formatOutput=true;

$rss=$dom->createElement("rss");
$rss->setAttribute("version","2.0");
$dom->appendChild($rss);

$channel=$dom->createElement("channel");
$rss->appendChild($channel);

$titleHeader=$dom->createElement("title","PoW - Pet Adoption on Web (type: " . ucfirst($type) .")");
$channel->appendChild($titleHeader);

$linkHeader=$dom->createElement("link","http://localhost/pow/");
$channel->appendChild($linkHeader);

$descriptionHeader=$dom->createElement("description","RSS feed");
$channel->appendChild($descriptionHeader);

while($row=$result->fetchArray(SQLITE3_ASSOC))
{
    $item=$dom->createElement("item");

    $title=$dom->createElement("title","Name: " . $row['name']);
    $item->appendChild($title);

    $link=$dom->createElement("link","http://localhost/pow/index.html#animal_public?id=".$row['id']);
    $item->appendChild($link);

    //mini template
    $descriptionTemplate=sprintf("Species: %s\n\t\t   Breed: %s\n\t\t   Status: %s\n\t\t   Region: %s\n      ",$row['species'],
    $row['breed'], $row['health_status'], $row['region']);

    $desc=$dom->createElement("description",$descriptionTemplate);
    $item->appendChild($desc);

    $date=date(DATE_RSS, strtotime($row['created_at']));
    $pubDate=$dom->createElement("pubDate",$date);
    $item->appendChild($pubDate);

    $guid=$dom->createElement("guid",$row['id']);
    $guid->setAttribute("isPermaLink","false");
    $item->appendChild($guid);

    $channel->appendChild($item);
}

echo $dom->saveXML();

?>