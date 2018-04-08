# NaturalDate
When telling stories, people usually don't give an exact date for the events of the story. They often say things like "the summer of '87" or "last Christmas".

You can use NaturalDate to translate these date approximations to usable dates or date ranges in your code.

If NaturalDate can't determine an exact date from the input, it will give you a start and end date that the actual date should be between.


```php
// Simple Usage

$date     = NaturalDate::parse( "Early 2016", 'America/Denver' );
echo $date->toJson();

```

```
// Will output (prettified for readability)

{  
   "utcStart":{  
      "date":"2016-01-01 07:00:00.000000",
      "timezone_type":3,
      "timezone":"UTC"
   },
   "utcEnd":{  
      "date":"2016-05-01 05:59:59.000000",
      "timezone_type":3,
      "timezone":"UTC"
   },
   "localStart":{  
      "date":"2016-01-01 00:00:00.000000",
      "timezone_type":3,
      "timezone":"America\/Denver"
   },
   "localEnd":{  
      "date":"2016-04-30 23:59:59.000000",
      "timezone_type":3,
      "timezone":"America\/Denver"
   }
}

```