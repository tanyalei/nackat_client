# Example of usage

```
$api = new \NationalCatalogApi\Client('tygcz9merg9cotv', 'eqx2g6w0rgnxmq3h'); //apikey, supplier_key

$api->setUrl('http://api.crpt.my');// can skip, then used default https://апи.национальный-каталог.рф

$feed = new \NationalCatalogApi\Feed();

$entry = $feed->newEntry();// returns empty object Entity, not related yet with a feed

$entry->setGoodId(123);
$entry->setGoodName("Шоколад");
$entry->setBrandId(456);
$entry->addCategory(78);
$entry->deleteCategory(90);

$entry->addIdentifiedBy("gtin", "4602065373085");
$entry->addIdentifiedBy("sku", "4602065373000", 3);

$entry->addAttr(2123, "4602065373085");
$entry->addAttr(1324, "4602065373000", 3);

$entry->addImage("default", "https://s1.1zoom.ru/prev2/534/Painting_Art_Big_cats_Tigers_Canine_tooth_fangs_533009_300x187.jpg", 2);
$entry->addImage("3ds", [
          "https://s1.1zoom.ru/prev2/534/Painting_Art_Big_cats_Tigers_Canine_tooth_fangs_533009_300x187.jpg",
          "https://s1.1zoom.ru/prev2/534/Painting_Art_Big_cats_Tigers_Canine_tooth_fangs_533009_300x187.jpg"
        ]);

$feed->addEntry($entry); //relate created entry with feed

print_r($feed->asJson()); 

$result = $api->postFeed($feed);// we can pass $feed or $feed->asJson() 
```

json 

```
[
    {
        "good_id": 123,
        "good_name": "Шоколад",
        "brand_id": 456,
        "categories": [
            {
                "cat_id": 78
            },
            {
                "cat_id": 90,
                "delete": 1
            }
        ],
        "identified_by": [
            {
                "type": "gtin",
                "value": "4602065373085",
                "multiplier": 1,
                "level": "trade-unit"
            },
            {
                "type": "sku",
                "value": "4602065373000",
                "multiplier": 1,
                "level": "trade-unit",
                "party_id": 3
            }
        ],
        "good_attrs": [
            {
                "attr_id": 2123,
                "attr_value": "4602065373085"
            },
            {
                "attr_id": 1324,
                "attr_value": "4602065373000",
                "attr_value_type": 3
            }
        ],
        "good_images": [
            {
                "photo_type": "default",
                "photo_url": "https:\/\/s1.1zoom.ru\/prev2\/534\/Painting_Art_Big_cats_Tigers_Canine_tooth_fangs_533009_300x187.jpg",
                "location_id": 2
            },
            {
                "photo_type": "3ds",
                "photo_url": [
                    "https:\/\/s1.1zoom.ru\/prev2\/534\/Painting_Art_Big_cats_Tigers_Canine_tooth_fangs_533009_300x187.jpg",
                    "https:\/\/s1.1zoom.ru\/prev2\/534\/Painting_Art_Big_cats_Tigers_Canine_tooth_fangs_533009_300x187.jpg"
                ]
            }
        ]
    }
]

```

result 

```
Array
(
    [apiversion] => 3
    [result] => Array
        (
            [feed_id] => 131
        )

)
```
