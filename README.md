# NaturalDate  

[![Latest Stable Version](https://poser.pugx.org/michaeldrennen/natural-date/version)](https://packagist.org/packages/michaeldrennen/natural-date) [![Total Downloads](https://poser.pugx.org/michaeldrennen/natural-date/downloads)](https://packagist.org/packages/michaeldrennen/natural-date) [![License](https://poser.pugx.org/michaeldrennen/natural-date/license)](https://packagist.org/packages/michaeldrennen/natural-date) [![Build Status](https://travis-ci.org/michaeldrennen/NaturalDate.svg?branch=master)](https://travis-ci.org/michaeldrennen/NaturalDate) [![Coverage Status](https://coveralls.io/repos/github/michaeldrennen/natural-date/badge.svg?branch=master)](https://coveralls.io/github/michaeldrennen/natural-date?branch=master)

When telling stories, people usually don't give an exact date for the events of the story. They often say things like:
> the summer of '87

or 
> last Christmas  
  
You can use NaturalDate to translate these date approximations to usable dates or date ranges in your code.  
  
If NaturalDate can't determine an exact date from the input, it will give you a start and end date that the actual date should be between.  
  
# Example
```php  
$naturalDate = NaturalDate::parse( "Early 2016", 'America/Denver' );  
echo $naturalDate->toJson();
```
## Output
<pre>
{  
  "utcStart": {  
    "date": "2016-01-01 07:00:00.000000",  
    "timezone_type": 3,  
    "timezone": "UTC"  
  },  
  "utcEnd": {  
    "date": "2016-05-01 05:59:59.000000",  
    "timezone_type": 3,  
    "timezone": "UTC"  
  },  
  "localStart": {  
    "date": "2016-01-01 00:00:00.000000",  
    "timezone_type": 3,  
    "timezone": "America\/Denver"  
  },  
  "localEnd": {  
    "date": "2016-04-30 23:59:59.000000",  
    "timezone_type": 3,  
    "timezone": "America\/Denver"  
  }  
}
</pre>

# Summary of Functions

```php
$naturalDate = NaturalDate::parse( "between thanksgiving and christmas 2017", 'America/Denver' );

// $localStartDateTime will be a Carbon object.
$localStartDateTime = $naturalDate->getLocalStart();
echo $localStartDateTime; // '2017-11-23 00:00:00'


// $utcStartDateTime will be a Carbon object.
$utcStartDateTime = $naturalDate->getUtcStart();
echo $utcStartDateTime; // '2017-11-23 07:00:00'


// $localEndDateTime will be a Carbon object as well.
$localEndDateTime = $naturalDate->getLocalEnd();
echo $localEndDateTime; // '2017-12-31 23:59:59'


// $utcEndDateTime will be a Carbon object.
$utcEndDateTime = $naturalDate->getUtcEnd();
echo $utcEndDateTime; // '2018-01-01 06:59:59'

echo $naturalDate->getType(); // 'range'

```

## parse( string $string = '', string $timezoneId = 'UTC', string $languageCode = 'en', $patternModifiers = [], NaturalDate $existingNaturalDate = NULL, bool $cleanOutput = TRUE ): NaturalDate
Explaining the parameters:
- $string - The user supplied string that you want to parse.
- $timezoneId - The timezone for the user. Defaults to UTC
- $languageCode - The 2 character language code of the user.
- $patternModifiers - An array of custom pattern modifiers you want NaturalDate to use. Explained in detail below.
- $existingNaturalDate - Shouldn't really ever be used by you. More for internal usage.
- $cleanOutput - Shouldn't really be used by you either. But set to false if you want to see what NaturalDate is doing under the hood.

## getLocalStart()
This method returns a Carbon object with the user's timezone. The Carbon object represents when NaturalDate thinks the earliest possible time was based on the user's input.

## getLocalEnd()
This method returns a Carbon object with the user's timezone. The Carbon object represents when NaturalDate thinks the latest possible time was based on the user's input.

## getUtcStart() and getUtcEnd()
These methods act the same as getLocalStart() and getLocalEnd() but the Carbon objects they return use the UTC timezone.

## getType()
This method will return a string that gives you an idea of the "confidence" that NaturalDate has on the data it's returning to you.

Possible values are:
- datetime - The most specific/highest confidence.
- date
- week       
- month      
- year       
- season     
- quarter    
- range - Often seen when the user asks for "Between x and y"  

## toJson()
For convenience, I've added a toJson() method that returns the NaturalDate object created by NaturalDate::parse() in a json encoded string.    

## __toString()
I also created an implementation of the magic __toString() method, so if you echo the NaturalDate object, you will get a prettified output describing the NaturalDate object.

## Missing holidays? Just ask for them.
If you see that NaturalDate is missing some holidays (it is), create an issue on GitHub and I will add them. Holidays from all countries should be part of the base library.

## Building onto the PatternModifiers
This is the really cool thing. You can create your own PatternModifiers. What does that mean exactly?

Think about this scenario... 
You want to let your users enter a date like:
"my freshman year in high school"

Well obviously NaturalDate doesn't know when that is. So you could create your own PatternModifier to pass into the parse() method.

Your pattern modifier would need a regular expression that would trigger it, and dates to fill in when triggered.

Use the class named **JohnMcClanesBirthday** in the PatternModifiers directory as a model.

It should be pretty easy for you to copy that and modify it to suit your needs.

```php

use MichaelDrennen\NaturalDate\PatternModifiers\PatternModifier;
use MichaelDrennen\NaturalDate\NaturalDate;


class JohnMcClanesBirthday extends PatternModifier {

    protected $patterns = [
        "/john mcclane\'s birthday/i",
        "/john mcclanes birthday/i",
        "/john mcclane birthday/i",
    ];


    public function modify( NaturalDate $naturalDate ): NaturalDate {
        $naturalDate->setStartYear( 1955 );
        $naturalDate->setStartMonth( 11 );
        $naturalDate->setStartDay( 2 );
        $naturalDate->setStartHour( 0 );
        $naturalDate->setStartMinute( 0 );
        $naturalDate->setStartSecond( 0 );

        $naturalDate->setEndYear( 1955 );
        $naturalDate->setEndMonth( 11 );
        $naturalDate->setEndDay( 2 );
        $naturalDate->setEndHour( 23 );
        $naturalDate->setEndMinute( 59 );
        $naturalDate->setEndSecond( 59 );

        $naturalDate->setType( NaturalDate::date );

        return $naturalDate;
    }
}
```