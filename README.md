# Example of usage

```
$api = new \NationalCatalogApi\Client('tygcz9merg9cotv', 'eqx2g6w0rgnxmq3h');

$feed = new \NationalCatalogApi\Feed();
$entry = $feed->newEntry();
$entry->setGoodId(123);
$entry->setGoodName("Шоколад");
$entry->setBrandId(456);
$entry->addCategory(78);
$entry->deleteCategory(90);

$entry->addIdentifedBy("gtin", "4602065373085");
$entry->addIdentifedBy("sku", "4602065373000", 3);

$entry->addAttr(2123, "4602065373085");
$entry->addAttr(1324, "4602065373000", 3);

$entry->addImage("default", "https://s1.1zoom.ru/prev2/534/Painting_Art_Big_cats_Tigers_Canine_tooth_fangs_533009_300x187.jpg", 2);
$entry->addImage("3ds", [
          "https://s1.1zoom.ru/prev2/534/Painting_Art_Big_cats_Tigers_Canine_tooth_fangs_533009_300x187.jpg",
          "https://s1.1zoom.ru/prev2/534/Painting_Art_Big_cats_Tigers_Canine_tooth_fangs_533009_300x187.jpg"
        ]);

$feed->addEntry($entry);
print_r($feed->asJson());
$result = $api->postFeed($feed);
```
