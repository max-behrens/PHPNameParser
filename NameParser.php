<?php

require_once __DIR__ . '/Person.php';

class NameParser {

    private array $titleMappings = [
        'Mr' => 'Mr',
        'Mister' => 'Mr',
        'Mrs' => 'Mrs',
        'Ms' => 'Ms',
        'Dr' => 'Dr',
        'Prof' => 'Prof'
    ];

    /*    
    * Parses a full input string and splits it into individual Person objects.
    */
    public function parse(string $input): array {
        $people = [];
        
        // Function to decide whether to split or return each row as a single name.
        $names = $this->splitMultipleNames($input);
        
        foreach ($names as $name) {

            $parts = $this->parseSingleName($name);
            if ($parts) {
                $people[] = new Person(
                    $parts['title'],
                    $parts['first_name'],
                    $parts['initial'],
                    $parts['last_name']
                );
            }
        }
        
        return $people;
    }


    /*    
    * Splits a string containing multiple names into individual name strings.
    */
    private function splitMultipleNames(string $input): array {

        // Creates a regex pattern like "Mr|Mrs|Ms|Dr|Prof" from $titleMappings for easier matching.
        $titlePattern = implode('|', array_keys($this->titleMappings));
        
        // Check for "Title and Title LastName" pattern (e.g., "Mr and Mrs Smith").
        if (preg_match("/^($titlePattern) and ($titlePattern) (.+)$/i", $input, $matches)) {
            $title1 = $matches[1];
            $title2 = $matches[2];
            $lastName = $matches[3];
    
            return [
                "$title1 $lastName",
                "$title2 $lastName"
            ];
        }
        
        // Check for "Title & Title FirstName LastName" pattern (e.g., "Mr & Mrs Joe Bloggs").
        if (preg_match("/^($titlePattern) & ($titlePattern) (.+)$/i", $input, $matches)) {
            $title1 = $matches[1];
            $title2 = $matches[2];
            $nameParts = explode(' ', $matches[3]);
    
            // Extract the last name (assume it's the last word).
            $lastName = array_pop($nameParts);
            $firstName = implode(' ', $nameParts);
    
            return [
                "$title1 $firstName $lastName",
                "$title2 $firstName $lastName"
            ];
        }
    
        // Fallback to splitting on "and" or "&" for other patterns.
        return preg_split('/ and | & /i', $input);
    }
    
    

    /*    
    * Parses a single name and extracts title, first name, initial, and last name.
    */
    private function parseSingleName(string $name): ?array {
        $parts = explode(' ', trim($name));
        
        if (count($parts) < 2) return null;
        
        $title = $this->extractTitle($parts[0]);

        // Remove the title.
        array_shift($parts);
        
        $lastName = array_pop($parts);
        
        $firstName = null;
        $initial = null;
        
        if (!empty($parts)) {
            $middlePart = $parts[0];
            
            // Check if the middle section is an initial (e.g., "J." or "J").
            if (strlen($middlePart) === 1 || (strlen($middlePart) === 2 && $middlePart[1] === '.')) {
                $initial = rtrim($middlePart, '.');
            } else {
                $firstName = $middlePart;
            }
        }
        
        return [
            'title' => $title,
            'first_name' => $firstName,
            'initial' => $initial,
            'last_name' => $lastName
        ];
    }


    /*    
    * Extracts the title from a string.
    */
    private function extractTitle(string $input): string {
        $title = rtrim($input, '.');
        return $this->titleMappings[$title] ?? $title;
    }
}


/*
* CSV Usage Script.
*/
$filename = $argv[1] ?? 'data/homeowners.csv';

if (!file_exists($filename)) {
    die("Error: File '$filename' not found.\n");
}

$parser = new NameParser();
$results = [];

if (($handle = fopen($filename, "r")) !== FALSE) {
    
    // Skip the header row of the CSV file.
    fgetcsv($handle);
    
    while (($data = fgetcsv($handle)) !== FALSE) {
        $homeowner = $data[0];

        // Parse the name into individual person objects.
        $people = $parser->parse($homeowner);
        
        foreach ($people as $person) {
            $results[] = $person->toArray();
        }
    }
    fclose($handle);
}

// Output results as JSON.
echo json_encode($results, JSON_PRETTY_PRINT) . "\n";