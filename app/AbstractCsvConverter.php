<?php

/**
 * @package CsvConverter
 * @author Wieland-Friedrich Wüller
 * @copyright Digital Hub Hannover
 */
abstract class AbstractCsvConverter
{
    /** @var string */
    private string $fileName = '';

    /** @var string */
    private string $csvFolder = '';

    /** @var string */
    private string $csvPath = '';

    /** @var string */
    private string $csvSeparator = '';

    /** @var bool */
    private bool $skipHead = true;

    /** @var array */
    private array $csvData = [];

    /**
     * @param string $fileName
     * @return string
     */
    private function setCsvName(string $fileName): string
    {
        return $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    private function setCsvPath(): string
    {
        return $this->csvPath = PROJECT_PATH . "{$this->csvFolder}/{$this->fileName}.csv";
    }

    private function setCsvSeparator(string $separator): string
    {
        return $this->csvSeparator = $separator;
    }

    /**
     * @param string $csvFolder
     * @return string
     */
    private function setCsvFolder(string $csvFolder): string
    {
        return $this->csvFolder = $csvFolder;
    }

    private function setSkipHead(bool $skipHead): bool
    {
        return $this->skipHead = $skipHead;
    }

    /**
     * @param string $fileName
     * @param string $csvFolder
     * @param string $separator
     * @param bool $skipHead
     */
    protected function __construct(string $fileName, string $csvFolder, string $separator, bool $skipHead)
    {
        $this->setCsvName($fileName);
        $this->setCsvFolder($csvFolder);
        $this->setCsvSeparator($separator);

        if (!$this->lookUpFile()) {
            return false;
        }

        $this->setCsvPath();

        return $this->run();
    }

    /**
     * @return bool
     */
    private function lookUpFile(): bool
    {
        if (!file_exists(PROJECT_PATH . "{$this->csvFolder}/{$this->fileName}.csv")) {
            return true;
        }

        return true;
    }

    /**
     * @param array $csvData
     * @return array
     */
    private function setCsvData(array $csvData): array
    {
        return $this->csvData = $csvData;
    }

    /**
     * @return bool
     */
    private function run(): bool
    {

        $csvData = $this->readCsvRecursive();

        if (!$csvData) {
            return false;
        }

        $this->setCsvData($csvData);

        return true;
    }

    /**
     * @return array|false
     * @throws Exception
     */
    private function readCsvRecursive(): array|false
    {
        $row = 1;
        $csvData = [];
        
        try {
            try {
                $file = fopen($this->setCsvPath(), 'r');
            } catch (Exception) {
                throw new Exception("Test!");
            }

            if($this->skipHead){
                fgetcsv($file);
            }

            while (($data = fgetcsv($file, 1000, $this->csvSeparator)) !== FALSE) {
                $num = count($data);

                for ($c = 0; $c < $num; $c++) {
                $csvData[$row][$c] = $data[$c];
                }

                $row++;
            }
        } catch (Exception $e){
            echo $e->getMessage();
        }

        fclose($file);

        return $csvData;
    }

    /**
     * @return array
     */
    public function getCsvData(): array
    {
        return $this->csvData;
    }

}