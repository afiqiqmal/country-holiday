# National Holidays :airplane:
Parsing National Public Holidays

[![Packagist](https://img.shields.io/packagist/dt/afiqiqmal/country-holiday.svg)](https://packagist.org/packages/afiqiqmal/country-holiday)
[![Packagist](https://img.shields.io/packagist/v/afiqiqmal/country-holiday.svg)](https://packagist.org/packages/afiqiqmal/country-holiday)
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/paypalme/mhi9388?locale.x=en_US)


![](https://banners.beyondco.de/Country%20Holiday.png?theme=dark&packageManager=composer+require&packageName=afiqiqmal%2FCountryHoliday&pattern=ticTacToe&style=style_1&description=Parsing+National+Public+Holiday&md=1&showWatermark=0&fontSize=100px&images=flag)


### How to Use

Declare
```php
$holiday = new Holiday;
app(Holiday::class); // if bound with laravel refer here - https://laravel.com/docs/11.x/container#contextual-binding
```


Holidays in current year

```php
Holiday::for('countryName')->fromAllState()->get();
Holiday::make()->fromAllState()->get();
```

Holidays in specific years

```php
Holiday::for('countryName')->fromAllStates(2017)->get();
Holiday::for('countryName')->fromAllStates([2017, 2019])->get();
Holiday::for('countryName')->fromAllStates()->ofYear(2017)->get();
Holiday::make()->fromAllStates()->ofYear(2017)->get();
```

Holidays by region

```php
Holiday::for('malaysia')->fromState("Selangor")->get();
Holiday::for('malaysia')->fromState(["Selangor","Malacca"])->get();
```

Holidays by region and year

```php
Holiday::for('malaysia')->fromState("Selangor","2017")->get();
Holiday::for('malaysia')->fromState("Selangor", [2017, 2019])->get();
Holiday::for('malaysia')->fromState(["Selangor","Malacca"], [2017, 2019])->get();
Holiday::for('malaysia')->fromState(["Selangor","Malacca"])->ofYear([2017, 2019])->get();
```


Group and filter results

```php
$holiday = new Holiday;
Holiday::for('malaysia')->fromAllStates()->groupByMonth()->get();
Holiday::for('malaysia')->fromAllStates()->filterByMonth(1)->get();  //date('F')
```

### Requirements
PHP 8.2 and above

### To install

run

`composer require afiqiqmal/country-holiday`

### Sample
<pre>
{
   "status":true,
   "data":[
      {
         "regional":"Selangor",
         "collection":[
            {
               "year":2019,
               "data":[
                  {
                     "day":"Tuesday",
                     "date":"2019-01-01",
                     "date_formatted":"01 January 2019",
                     "month":"January",
                     "name":"New Year's Day",
                     "description":"Regional Holiday",
                     "is_holiday":true,
                     "type":"Regional Holiday",
                     "type_id":4
                  },
                  {
                     "day":"Monday",
                     "date":"2019-01-21",
                     "date_formatted":"21 January 2019",
                     "month":"January",
                     "name":"Thaipusam",
                     "description":"Regional Holiday",
                     "is_holiday":true,
                     "type":"Regional Holiday",
                     "type_id":4
                  }
               ]
            }
         ]
      },
      {
         "regional":"Johor",
         "collection":[
            {
               "year":2019,
               "data":[
                  {
                     "day":"Monday",
                     "date":"2019-01-21",
                     "date_formatted":"21 January 2019",
                     "month":"January",
                     "name":"Thaipusam",
                     "description":"Regional Holiday",
                     "is_holiday":true,
                     "type":"Regional Holiday",
                     "type_id":4
                  }
               ]
            }
         ]
      }
   ],
   "developer":{
      "name":"Hafiq",
      "email":"hafiqiqmal93@gmail.com",
      "github":"https://github.com/afiqiqmal"
   }
}
</pre>

### Source :date:

Scraped from - http://www.officeholidays.com/countries

### MIT Licence

Copyright © 2017 @afiqiqmal

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the “Software”), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

<br>

### Donate to the project :tea: 

<a href="https://www.paypal.com/paypalme/mhi9388?locale.x=en_US"><img src="https://i.imgur.com/Y2gqr2j.png" height="40"></a> 



