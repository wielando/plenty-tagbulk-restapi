<?php

/**
 * @package CsvConverter
 * @author Wieland-Friedrich Wüller
 */
class CsvTagConverter extends AbstractCsvConverter
{

    /** @var array */
    private array $rawCsvData = [];

    /** @var array */
    public array $convertedCsvData = [];

    /** @var array */
    public array $tagJsonObject = [];

    /** @var array */
    public array $tagOrderedJsonObject = [];

    /** @var string */
    private string $pId = "";

    /** @var array */
    private array $csvHeaderKeys = [];

    /**
     * @param string $fileName
     * @param string $csvFolder
     * @param string $separator
     * @param bool $skipHead
     * @param string $pId
     * @param array $csvHeaderKeys
     */
    public function __construct(
        string $fileName,
        string $csvFolder,
        string $separator,
        bool   $skipHead,
        string $pId,
        array  $csvHeaderKeys
    )
    {
        parent::__construct($fileName, $csvFolder, $separator, $skipHead);

        $this->pId = $pId;

        $this->setHeaderKeys($csvHeaderKeys);
    }

    private function setHeaderKeys(array $headerKeys)
    {
        foreach ($headerKeys as $headerName => $headerKey) {

            $this->csvHeaderKeys[$headerName] = $headerKey;
        }
    }

    /**
     * @return CsvTagConverter|null
     */
    public function load(): ?self
    {

        $this->rawCsvData = $this->getCsvData();

        if (empty($this->rawCsvData)) {
            return null;
        }

        return $this;
    }

    public function modifyData(array $prefix = null): void
    {
        $this->setTags();
        $this->setTagJsonObject($prefix);
    }

    private function setTags(): void
    {
        $csvData = $this->rawCsvData;

        foreach ($this->csvHeaderKeys as $headerName => $headerKey) {
            $convertedTagData[$headerName] = [];

            foreach ($csvData as $arrayKey => $rawCsvData) {
                $csvTagData = $rawCsvData[$headerKey];

                if (empty($csvTagData)) {
                    continue;
                }

                $splitTagData = explode(",", $csvTagData);

                foreach ($splitTagData as $splitTagDataKey => $rawSplitTagData) {
                    $rawSplitTagData = trim($rawSplitTagData, " ");

                    if (empty($convertedTagData[$headerName])) {
                        $convertedTagData[$headerName][] = $rawSplitTagData;
                    } else {
                        $lastValue = $rawSplitTagData;
                        $isDuplicate = false;

                        for ($i = 0; $i <= count($convertedTagData[$headerName]) - 1; $i++) {

                            // Skip when value already in our array
                            if ($convertedTagData[$headerName][$i] == $lastValue) {
                                $isDuplicate = true;
                                break;
                            }
                        }

                        if (!$isDuplicate) {
                            $convertedTagData[$headerName][] = $lastValue;
                        }

                    }

                }
            }

            $this->convertedCsvData[$headerName] = $convertedTagData[$headerName];
        }
    }

    private function setTagJsonObject(array $prefix = null): void
    {
        $data = [];
        $identifier = "";

        foreach ($this->convertedCsvData as $key => $tagValues) {

            foreach ($tagValues as $tagValue) {

                if (!is_null($prefix)) {
                    $identifier = $prefix[$key];
                }

                $tagName = "{$identifier}{$tagValue}";

                $referenceData = [
                    [
                        "tagName" => "{$tagName}",
                        "tagType" => "variation",
                        "plentyId" => "{$this->pId}",
                        "names" => [
                            [
                                "tagLang" => "de",
                                "tagName" => "{$tagName}"
                            ]
                        ],
                        "tagLang" => "de"
                    ]
                ];

                $data[] = $referenceData;

                $this->tagOrderedJsonObject[$key][] = $referenceData;
            }

        }

        $this->tagJsonObject = $data;
    }

}
