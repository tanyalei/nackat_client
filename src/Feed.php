<?php

namespace NationalCatalogApi;

final class Feed
{
	private $entries = [];

	public function newEntry()
	{
		return new Entry();
	}
	public function addEntry($entry)
	{
		$this->entries[] = $entry;
	}

	public function getList()
	{
		return $this->entries;
	}

	public function asJson()
	{
		$result = [];
		foreach ($this->entries as $entry) {
			$result[] = $entry->toArray();
		}
		return json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	}
}