<?php

header("Content-Type: application/rss+xml; charset=UTF-8");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";

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

?>

<rss version="2.0">
<channel>
    <title><?= htmlspecialchars("PoW - Pet Adoption on Web (type: " . ucfirst($type) .")") ?></title>
    <link>http://localhost/pow/</link>
    <description>Rss feed</description>
    <?php
        while($row=$result->fetchArray(SQLITE3_ASSOC))
        {
            $title="Name: " . $row['name'];
            $description="\n\t\t\tSpecies: " . $row['species'] . "\n\t\t\tBreed: " . $row['breed'] . "\n\t\t\tStatus: "
            . $row['health_status'] . "\n\t\t\tRegion: " . $row['region']. "\n";
            $link="http://localhost/pow/index.html#animal_public?id=".$row['id'];
            $pubDate=$row['created_at'];
            $guid=$row['id'];

            echo "\t<item>\n";
                echo "\t\t<title>". htmlspecialchars($title) ."</title>\n";
                echo "\t\t<link>". htmlspecialchars($link) ."</link>\n";
                echo "\t\t<description>". htmlspecialchars($description) ."\t\t</description>\n";
                echo "\t\t<pubDate>". htmlspecialchars($pubDate) ."</pubDate>\n";
                echo "\t\t<guid>". htmlspecialchars($guid) ."</guid>\n";
            echo "\t</item>\n";
        }
    ?>
</channel>
</rss>