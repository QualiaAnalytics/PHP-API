
<p align="center"><img src="https://s3-eu-west-1.amazonaws.com/qa-survey-system/image-upload/PWT62RP7xsUfQpH9.png"></p>

### Qualia Analytics
[![Build Status](https://travis-ci.org/QualiaAnalytics/PHP-API.svg?branch=master)](http://travis-ci.org/QualiaAnalytics/PHP-API)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/QualiaAnalytics/PHP-API/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/QualiaAnalytics/PHP-API/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/QualiaAnalytics/PHP-API/master.svg)](https://scrutinizer-ci.com/g/QualiaAnalytics/PHP-API/)

PHP wrapper to interact with Qualia Analytics API

### Prerequisites

* PHP 5.3+
* Composer

### Installing

#### Composer
```
composer require qualiaanalytics/php-api
```

```php
// Import library from composer
require __DIR__ . '/vendor/autoload.php';
```

### Usage

#### Create Client
```php
$client = new \Qualia\Client("YOUR_SURVEY_ID", "API_KEY");
```
Please provide SURVEY_ID and API_KEY parameters to client constructor.

#### Build a request and submit
```php
$response = \Qualia\Submission\Entry::build($client)
                                  ->name("QUESTION_ID", "First Name", "Last Name")
                                  ->email("QUESTION_ID", "email@example.com")
                                  ->date("QUESTION_ID", "2020-01-02")
                                  ->response("QUESTION_ID", "RESPONSE")
                                  ->send();
```
Please provide question identifiers for each field. A full list of question identifiers can be retrieved using [this helper method](#retrieving-question-identifiers-for-surveys)

#### Provide an unique identifier (recommended, optional)
To ensure that you are not sending duplicate entries please provide some sort of identifier for that specific entry if you have in your system. This can be anything: order id, customer id, user id, etc. 
```php
$response = \Qualia\Submission\Entry::build($client)
                                  ->uniqueId("123")
                                  ...
                                  ->send();
```

#### Provide a language for entry (recommended, optional)
If the survey has multiple languages enabled, you may set the language for an entry using below syntax depending on your website language.
```php
$response = \Qualia\Submission\Entry::build($client)
                                  ...
                                  ->language("en")
                                  ->send();
```
If not provided, system will assign the default survey language. Be aware that if language provided is not in a list of survey languages, a default survey language will be assigned.


#### Allow duplicate emails
By default, we will reject duplicate emails and throw an EmailExistsException.
However, if you would like to allow duplicate emails to be submitted, you may call allowDuplicates() method. *Please ensure that you are not submitting duplicate emails too often as that will result in sending repeated emails to same email addresses.*
```php
$response = \Qualia\Submission\Entry::build($client)
                                  ->allowDuplicates()
                                  ...
                                  ->send();
```


#### Retrieving question identifiers for surveys
If you are not sure what fields to provide, please retrieve a full list of questions used in the survey. Note: This will retrieve all questions available and usually you should only provide email, name, maybe a date of visit and other applicable fields that you already collect.
```php
$questions = \Qualia\Configuration\Questions::get($client);

var_dump($questions);
/*
    [
        "surveys": [
            [ "name": "Enrollment Survey","key": "enrollment"],
            [ "name": "Initial Survey","key": "initial_survey"], 
        ],
        [
            key: "QUESTION_ID",
            name: "Question Name"
            type: "Question Type",
            options: [
                OPTION_ID: "Option #1 name",
                OPTION_ID: "Option #2 name",
                ...
            ],
            help: "guidance which method to use"
        ],
        [
            key: "q_BFmVBf1TSb11xAU0",
            name: "What's your date of Visit?"
            type: "date",
            options: [ ],
            help: "use date("q_BFmVBf1TSb11xAU0", "2020-01-02") method in API Client. Date Value must be provided in Y-m-d."
        ],
        ...
    ]
*/
                              
```

#### Full example
This is a full example of general configuration that will work in most cases. You will need to replace the strings in CAPITAL letters.
```php
// Import library from composer
require __DIR__ . '/vendor/autoload.php';

// Initialize client
$client = new \Qualia\Client("YOUR_SURVEY_ID", "API_KEY");

try {
    // Create entry
    $response = \Qualia\Submission\Entry::build($client)
                                        ->uniqueId("SOME_ID")
                                        ->name("QUESTION_ID", "First Name", "Last Name")
                                        ->email("QUESTION_ID", "email@example.com")
                                        ->date("QUESTION_ID", "2020-01-02")
                                        ->response("QUESTION_ID", "RESPONSE")
                                        ->send();
} catch (\Qualia\Exceptions\ConnectionErrorException $e) {
    // unable to connect to server
} catch (\Qualia\Exceptions\EmailExistsException $e) {
    // echo $e->getEntryId();
    // The submitted email already exists in the server.
    // You may or may not need to handle this in your code..
    // By default, we are preventing duplicate submissions,
    // if you would like to submit it otherwise, call
    // allowDuplicates() method when building an entry
} catch (\Qualia\Exceptions\RequestException $e) {
    // some other unexpected error
    // echo $e->getMessage();
}
```
