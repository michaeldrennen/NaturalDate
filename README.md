# NaturalDate
When telling stories, people usually don't give an exact date for the events of the story. They often say things like "the summer of '87" or "last Christmas".

You can use NaturalDate to translate these date approximations to usable dates or date ranges in your code.

If NaturalDate can't determine an exact date from the input, it will give you a start and end date that the actual date should be between.


```php
// Simple Usage

$date     = NaturalDate::parse( "Early 2017", 'America/Denver' );
$jsonDate = $date->toJson();

```