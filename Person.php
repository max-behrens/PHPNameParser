<?php

class Person {
    /*
     * ?string allows properties to be either a string or null. Only $first_name and $initial 
     * can be null because they might not appear in every name pattern (e.g., "Mr J. Smith").
     */
    public ?string $title;
    public ?string $first_name;
    public ?string $initial;
    public ?string $last_name;

    public  function __construct(
        string $title,
        ?string $first_name = null,
        ?string $initial = null,
        string $last_name
    ) {
        $this->title = $title;
        $this->first_name = $first_name;
        $this->initial = $initial;
        $this->last_name = $last_name;
    }

    /*    
    * Converts the Person object to an array.
    */
    public function toArray(): array {
        return [
            'title' => $this->title,
            'first_name' => $this->first_name,
            'initial' => $this->initial,
            'last_name' => $this->last_name
        ];
    }
}