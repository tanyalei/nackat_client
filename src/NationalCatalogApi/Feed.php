<?php

namespace NationalCatalogApi;

final class Feed
{
    /**
     * @var array
     */
    private $entries = [];

    /**Returns empty entry
     * @return Entry
     */
    public function newEntry() : Entry
	{
		return new Entry();
	}

    /**Add formed Entry
     * @param Entry $entry
     */
	public function addEntry(Entry $entry)
	{
		$this->entries[] = $entry;
	}

    /**
     * Returns entries
     * @return array of Entry obj
     */
	public function getList() : array
	{
		return $this->entries;
	}

    /**Converts entries to json
     * @return string
     */
	public function asJson()
	{
		$result = [];
		foreach ($this->entries as $entry) {
			$result[] = $entry->toArray();
		}
		return json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}
}
